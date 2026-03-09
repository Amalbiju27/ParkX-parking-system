<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $ownerId = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | 1️⃣ Parking spaces with LIVE availability
        |--------------------------------------------------------------------------
        */
        $parkingSpaces = DB::table('parking_spaces')
            ->where('owner_id', $ownerId)
            ->get();

        $spaceIds = $parkingSpaces->pluck('id');

        foreach ($parkingSpaces as $space) {
            $space->occupied_slots = DB::table('parking_slots')
                ->where('parking_space_id', $space->id)
                ->where('status', 'occupied')
                ->count();
            
            $space->available_slots_count = DB::table('parking_slots')
                ->where('parking_space_id', $space->id)
                ->where('status', 'available')
                ->count();
        }

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ Slot details + Tooltip Data (FIXED)
        |--------------------------------------------------------------------------
        | I removed 'vehicles.expected_exit_time' since your database doesn't have it.
        */
        $slots = DB::table('parking_slots')
            ->leftJoin('vehicles', function($join) {
                $join->on('parking_slots.id', '=', 'vehicles.slot_id')
                     ->where('vehicles.status', '=', 'parked');
            })
            ->whereIn('parking_slots.parking_space_id', $spaceIds)
            ->select(
                'parking_slots.*', 
                'vehicles.vehicle_number', 
                'vehicles.entry_time'
                // Removed 'vehicles.expected_exit_time' to fix the crash
            )
            ->get()
            ->groupBy('parking_space_id');

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ Current parked vehicles
        |--------------------------------------------------------------------------
        */
        $currentVehicles = DB::table('vehicles')
            ->join('parking_spaces', 'vehicles.parking_space_id', '=', 'parking_spaces.id')
            ->join('vehicle_categories', 'vehicles.category_id', '=', 'vehicle_categories.id')
            ->leftJoin('parking_slots', 'vehicles.slot_id', '=', 'parking_slots.id')
            ->whereIn('vehicles.parking_space_id', $spaceIds)
            ->where('vehicles.status', 'parked')
            ->select(
                'vehicles.*',
                'parking_spaces.name as parking_name',
                'vehicle_categories.name as category_name',
                'parking_slots.slot_number'
            )
            ->orderBy('vehicles.entry_time', 'desc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ Vehicle history (search + filter)
        |--------------------------------------------------------------------------
        */
        $historyQuery = DB::table('vehicles')
            ->join('parking_spaces', 'vehicles.parking_space_id', '=', 'parking_spaces.id')
            ->join('vehicle_categories', 'vehicles.category_id', '=', 'vehicle_categories.id')
            ->leftJoin('parking_slots', 'vehicles.slot_id', '=', 'parking_slots.id')
            ->whereIn('vehicles.parking_space_id', $spaceIds)
            ->where('vehicles.status', 'exited');

        if ($request->vehicle_number) {
            $historyQuery->where(
                'vehicles.vehicle_number',
                'like',
                '%' . $request->vehicle_number . '%'
            );
        }

        if ($request->from_date) {
            $historyQuery->whereDate('vehicles.entry_time', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $historyQuery->whereDate('vehicles.entry_time', '<=', $request->to_date);
        }

        $vehicleHistory = $historyQuery
            ->select(
                'vehicles.*',
                'parking_spaces.name as parking_name',
                'vehicle_categories.name as category_name',
                'parking_slots.slot_number'
            )
            ->orderBy('vehicles.entry_time', 'desc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 5️⃣ Revenue (Unified)
        |--------------------------------------------------------------------------
        */
        $todayManualRevenue = DB::table('vehicles')
            ->whereIn('parking_space_id', $spaceIds)
            ->whereDate('exit_time', today())
            ->sum('charge');
            
        $todayBookingRevenue = DB::table('bookings')
            ->whereIn('parking_space_id', $spaceIds)
            ->whereIn('status', ['booked', 'occupied', 'reserved', 'completed'])
            ->whereDate('created_at', today())
            ->sum('amount');
            
        $todayRevenue = $todayManualRevenue + $todayBookingRevenue;

        $totalManualRevenue = DB::table('vehicles')
            ->whereIn('parking_space_id', $spaceIds)
            ->where('status', 'exited')
            ->sum('charge');
            
        $totalBookingRevenue = DB::table('bookings')
            ->whereIn('parking_space_id', $spaceIds)
            ->whereIn('status', ['booked', 'occupied', 'reserved', 'completed'])
            ->sum('amount');
            
        $totalRevenue = $totalManualRevenue + $totalBookingRevenue;

        /*
        |--------------------------------------------------------------------------
        | 6️⃣ Live Counts
        |--------------------------------------------------------------------------
        */
        $totalCapacity = DB::table('parking_slots')
            ->whereIn('parking_space_id', $spaceIds)
            ->count();

        $occupiedCount = DB::table('parking_slots')
            ->whereIn('parking_space_id', $spaceIds)
            ->where('status', 'occupied')
            ->count();

        $availableCount = $totalCapacity - $occupiedCount;


        /*
        |--------------------------------------------------------------------------
        | 7️⃣ Current & Upcoming Bookings
        |--------------------------------------------------------------------------
        */
        $bookings = DB::table('bookings')
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->leftJoin('vehicle_categories', 'bookings.vehicle_category_id', '=', 'vehicle_categories.id')
            ->join('parking_spaces', 'bookings.parking_space_id', '=', 'parking_spaces.id')
            ->leftJoin('parking_slots', 'bookings.slot_id', '=', 'parking_slots.id')
            ->whereIn('bookings.parking_space_id', $spaceIds)
            ->whereNotIn('bookings.status', ['cancelled', 'completed'])
            ->select(
                'bookings.*',
                'users.name as booker_name',
                'vehicle_categories.name as vehicle_category',
                'parking_spaces.name as space_name',
                'parking_slots.slot_number as slot_name'
            )
            ->orderBy('bookings.created_at', 'desc')
            ->get();


        return view('owner.dashboard', compact(
            'parkingSpaces',
            'slots',
            'currentVehicles',
            'vehicleHistory',
            'todayRevenue',
            'totalRevenue',
            'totalCapacity',
            'occupiedCount',
            'availableCount',
            'bookings'
        ));
    }
}