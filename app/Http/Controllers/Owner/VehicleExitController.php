<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Services\PricingService;

class VehicleExitController extends Controller
{
    protected $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    // 1. Show the Exit Form with List of Parked Vehicles
    public function index()
    {
        $ownerId = Auth::id();

        // Fetch only 'parked' vehicles belonging to this owner
        $vehicles = DB::table('vehicles')
            ->join('parking_spaces', 'vehicles.parking_space_id', '=', 'parking_spaces.id')
            ->where('parking_spaces.owner_id', $ownerId)
            ->where('vehicles.status', 'parked')
            ->select('vehicles.*', 'parking_spaces.name as space_name')
            ->orderBy('vehicles.entry_time', 'desc')
            ->get();

        return view('owner.vehicle_exit.index', compact('vehicles'));
    }

    // 2. Process the Exit (Legacy Form Entry)
    public function exit(Request $request)
    {
        $request->validate([
            'vehicle_number' => 'required|string'
        ]);

        $vehicle = DB::table('vehicles')
            ->where('vehicle_number', $request->vehicle_number)
            ->where('status', 'parked')
            ->first();

        if (!$vehicle) {
            return back()->with('error', 'Vehicle not found or is not currently parked.');
        }

        // Reroute to the new unified processor
        return $this->processExit($request, $vehicle->id, 'manual');
    }

    // 3. Unified Exit Processor (Handles Dashboard Card clicks)
    public function processExit(Request $request, $id, $type)
    {
        DB::beginTransaction();

        try {
            $now = Carbon::now();
            $baseCharge = 20; // Default fallback
            $hourlyRate = 20; // Default fallback
            $penaltyPerMinute = 2; // ₹2 flat per minute overstay
            
            // --- A. Process User Booking ($type === 'booking') ---
            if ($type === 'booking' && class_exists('\App\Models\Booking')) {
                $record = \App\Models\Booking::leftJoin('vehicle_categories', 'bookings.vehicle_category_id', '=', 'vehicle_categories.id')
                    ->where('bookings.id', $id)
                    ->select('bookings.*', 'vehicle_categories.base_charge', 'vehicle_categories.hourly_rate')
                    ->first();
                    
                if (!$record || !in_array($record->status, ['booked', 'occupied', 'parked', 'active'])) {
                    return back()->with('error', 'Booking not found or already completed.');
                }
                
                $baseCharge = $record->base_charge ?? $baseCharge;
                $hourlyRate = $record->hourly_rate ?? $hourlyRate;
                $expectedExit = Carbon::parse($record->expires_at ?? $record->created_at->addHours($record->duration_hours ?? 1));
                $bookedHours = $record->duration_hours ?? 1;

                $standardFee = $this->pricingService->calculateFee((int)$bookedHours, (float)$hourlyRate, (float)$baseCharge);
                $penalty = $this->pricingService->calculatePenalty($expectedExit, $now, (float)$penaltyPerMinute);
                
                $totalCharge = $this->pricingService->calculateTotalWithPenalty($standardFee, $penalty);
                
                DB::table('bookings')->where('id', $id)->update([
                    'status' => 'completed',
                    'amount' => $totalCharge, // Update if you want to overwrite initial expected cost
                    'updated_at' => $now
                ]);
                
                DB::table('parking_slots')->where('id', $record->slot_id)->update(['status' => 'available']);
                
                $message = $penalty > 0 
                    ? "Checkout successful with Penalty! Overtime: " . $expectedExit->diffInMinutes($now) . " mins. Total: ₹{$totalCharge}" 
                    : "Checkout successful. Total: ₹{$totalCharge}";

            } 
            // --- B. Process Manual Entry ($type === 'manual') ---
            else if ($type === 'manual') {
                $vehicle = DB::table('vehicles')
                    ->join('vehicle_categories', 'vehicles.category_id', '=', 'vehicle_categories.id')
                    ->where('vehicles.id', $id)
                    ->select('vehicles.*', 'vehicle_categories.base_charge', 'vehicle_categories.hourly_rate')
                    ->first();

                if (!$vehicle || $vehicle->status !== 'parked') {
                    return back()->with('error', 'Vehicle record not found or already exited.');
                }
                
                $baseCharge = $vehicle->base_charge ?? $baseCharge;
                $hourlyRate = $vehicle->hourly_rate ?? $hourlyRate;
                $entryTime = Carbon::parse($vehicle->entry_time);
                
                $expectedExit = $vehicle->expected_exit_time 
                    ? Carbon::parse($vehicle->expected_exit_time) 
                    : $entryTime->copy()->addHour();
                    
                if (isset($vehicle->duration) && $vehicle->duration > 0) {
                    $bookedHours = $vehicle->duration;
                } else {
                    $bookedHours = max(1, $entryTime->diffInHours($expectedExit));
                }
                
                $standardFee = $this->pricingService->calculateFee((int)$bookedHours, (float)$hourlyRate, (float)$baseCharge);
                $penalty = $this->pricingService->calculatePenalty($expectedExit, $now, (float)$penaltyPerMinute);
                
                $totalCharge = $this->pricingService->calculateTotalWithPenalty($standardFee, $penalty);
                
                DB::table('vehicles')->where('id', $id)->update([
                    'exit_time' => $now,
                    'status'    => 'exited',
                    'charge'    => $totalCharge,
                    'penalty'   => $penalty, 
                    'updated_at'=> $now
                ]);
                
                DB::table('parking_slots')->where('id', $vehicle->slot_id)->update(['status' => 'available']);
                DB::table('parking_spaces')->where('id', $vehicle->parking_space_id)->increment('available_slots');

                $message = $penalty > 0 
                    ? "Vehicle exited with PENALTY! Overtime: " . $expectedExit->diffInMinutes($now) . " mins. Total Bill: ₹{$totalCharge}" 
                    : "Vehicle exited successfully. Total Bill: ₹{$totalCharge}";
                    
            } else {
                return back()->with('error', 'Invalid checkout type specified.');
            }

            DB::commit();
            return redirect()->route('owner.dashboard')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}