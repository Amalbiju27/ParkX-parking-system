<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use Carbon\Carbon;

class VehicleController extends Controller
{
    /**
     * Exit a vehicle and calculate penalty using grace period
     */
    public function exitVehicle($id)
    {
        $vehicle = Vehicle::findOrFail($id);

        // Safety check
        if (!$vehicle->entry_time) {
            return back()->with('error', 'Entry time missing');
        }

        $entryTime = Carbon::parse($vehicle->entry_time);
        $exitTime  = Carbon::now();

        // CONFIGURATION
        $baseHours = 1;          // allowed parking hours
        $graceMinutes = 10;      // grace period
        $penaltyRate = 50;       // ₹ per extra hour

        $allowedMinutes = ($baseHours * 60) + $graceMinutes;
        $actualMinutes  = $entryTime->diffInMinutes($exitTime);

        $penalty = 0;

        // Apply penalty only if grace period exceeded
        if ($actualMinutes > $allowedMinutes) {
            $extraMinutes = $actualMinutes - $allowedMinutes;
            $extraHours = ceil($extraMinutes / 60);
            $penalty = $extraHours * $penaltyRate;
        }

        // Update vehicle record
        $vehicle->update([
            'exit_time' => $exitTime,
            'penalty' => $penalty,
            'status' => 'exited'
        ]);

        return redirect()->back()->with(
            'success',
            $penalty > 0
                ? "Vehicle exited successfully. Penalty ₹$penalty"
                : "Vehicle exited successfully. No penalty."
        );
    }
}
