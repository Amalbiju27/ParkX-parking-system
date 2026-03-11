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
        $slots = DB::table('parking_slots')
            ->where('parking_space_id', $parkingSpaceId)
            ->select('id', 'slot_number', 'status')
            // 👇 FIX: Change 'UNSIGNED' to 'INTEGER' for PostgreSQL
            ->orderByRaw("CAST(SUBSTR(slot_number, 2) AS INTEGER)") 
            ->get();

        return response()->json($slots);
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
            DB::table('vehicles')->insert([
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
                ->with('success', 'Vehicle Parked! Exit expected at ' . $expectedExitTime->format('h:i A'));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}