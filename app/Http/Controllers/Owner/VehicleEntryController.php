<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VehicleEntryController extends Controller
{
    /**
     * Show the Vehicle Entry Form
     */
    public function index()
    {
        $ownerId = Auth::id();

        // 1. Get Parking Spaces
        $parkingSpaces = DB::table('parking_spaces')
            ->where('owner_id', $ownerId)
            ->get();

        // 2. Get Vehicle Categories (Car, Bike, etc.)
        $categories = DB::table('vehicle_categories')->get();

        // Make sure this view name matches the file you created
        // e.g. resources/views/owner/vehicle_entry.blade.php
       // Use dot notation to go inside the folder: owner -> vehicle_entry -> create
return view('owner.vehicle_entry.create', compact('parkingSpaces', 'categories'));
    }

    /**
     * AJAX: Get Slots for a specific Parking Space
     * This is called by the JavaScript in your view when you select a space.
     */
  public function getSlots($parkingSpaceId)
    {
        $space = DB::table('parking_spaces')->where('id', $parkingSpaceId)->first();
        
        if (!$space) {
            return response()->json([]);
        }

        // 1. Get existing slots
        $slots = DB::table('parking_slots')
            ->where('parking_space_id', $parkingSpaceId)
            ->get();

        // 2. Sync if missing (only if capacity is higher than actual slot records)
        if ($slots->count() < $space->capacity) {
            $existingNumbers = $slots->pluck('slot_number')->map(function($n) {
                return (int) preg_replace('/[^0-9]/', '', $n);
            })->toArray();

            for ($i = 1; $i <= $space->capacity; $i++) {
                // Try to find if this index already exists (S1, S2, or just 1, 2)
                $slotName = 'S' . $i;
                
                // Check if any existing slot matches this index (to prevent duplicates if named A1, A2 etc)
                // But generally we just want to ensure we have 'capacity' items.
                // If we have 2 slots (A1, A2), and capacity is 10, we'll add S3...S10.
                if ($slots->count() < $space->capacity && $i > $slots->count()) {
                    DB::table('parking_slots')->insert([
                        'parking_space_id' => $parkingSpaceId,
                        'slot_number'      => 'S' . $i,
                        'status'           => 'available',
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
                }
            }
            
            // Re-fetch everything after sync
            $slots = DB::table('parking_slots')
                ->where('parking_space_id', $parkingSpaceId)
                ->get();
        }

        // 3. Sort and Return
        $sortedSlots = $slots->sortBy(function($slot) {
            return (int) preg_replace('/[^0-9]/', '', $slot->slot_number);
        })->values();

        return response()->json($sortedSlots);
    }

    /**
     * Store the new vehicle entry
     */
    public function store(Request $request)
    {
        $request->validate([
            'parking_space_id' => 'required|exists:parking_spaces,id',
            'category_id'      => 'required|exists:vehicle_categories,id',
            'vehicle_number'   => 'required|string|max:20',
            'slot_id'          => 'required|exists:parking_slots,id',
            'start_time'       => 'required|date',
            'end_time'         => 'required|date|after:start_time',
            'total_amount'     => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Check slot availability
            $slot = DB::table('parking_slots')->where('id', $request->slot_id)->first();

            if (!$slot || $slot->status === 'occupied') {
                return back()->with('error', 'Sorry, that slot was just occupied. Please choose another.');
            }

            // Calculate Expected Exit Time
            $entryTime = \Carbon\Carbon::parse($request->start_time);
            $expectedExitTime = \Carbon\Carbon::parse($request->end_time); 

            // Insert Vehicle Record
            $vehicleId = DB::table('vehicles')->insertGetId([
                'parking_space_id'    => $request->parking_space_id,
                'slot_id'             => $request->slot_id,
                'category_id'         => $request->category_id,
                'vehicle_number'      => strtoupper($request->vehicle_number),
                'entry_time'          => $entryTime,
                'expected_exit_time'  => $expectedExitTime, // Save calculated time
                'charge'              => $request->total_amount, // DB column for the amount
                'status'              => 'parked',
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            // Update Slot Status
            DB::table('parking_slots')
                ->where('id', $request->slot_id)
                ->update(['status' => 'occupied']);

            DB::commit();

            return redirect()->route('owner.dashboard')
                ->with('success', 'Vehicle Parked! Exit expected at ' . $expectedExitTime->format('h:i A'))
                ->with('collect_amount', $request->total_amount)
                ->with('collect_vehicle', strtoupper($request->vehicle_number))
                ->with('collect_id', $vehicleId)
                ->with('collect_type', 'manual');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}