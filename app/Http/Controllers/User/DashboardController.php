<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Make sure user is authenticated
        $userId = Auth::id();

        if (!$userId) {
            return redirect('/login');
        }

        // Auto-cancellation is now handled by the scheduled command: 'bookings:cancel-expired'

        /*
        |--------------------------------------------------------------------------
        | Parking Spaces
        |--------------------------------------------------------------------------
        */
        $parkingSpaces = DB::table('parking_spaces')->get();
        foreach ($parkingSpaces as $space) {
            $space->available_slots_count = DB::table('parking_slots')
                ->where('parking_space_id', $space->id)
                ->where('status', 'available')
                ->count();
        }

        /*
        |--------------------------------------------------------------------------
        | User Bookings
        |--------------------------------------------------------------------------
        */
        $bookings = DB::table('bookings')
            ->join('parking_spaces', 'bookings.parking_space_id', '=', 'parking_spaces.id')
            ->leftJoin('vehicle_categories', 'bookings.vehicle_category_id', '=', 'vehicle_categories.id')
            ->select('bookings.*', 'parking_spaces.name as space_name', 'parking_spaces.location as location', 'vehicle_categories.name as vehicle_category_name')
            ->where('bookings.user_id', $userId)
            ->orderBy('bookings.created_at', 'desc')
            ->get();

        $activeBookings = $bookings->filter(fn($b) => in_array($b->status, ['booked', 'reserved', 'occupied']));
        $pastBookings = $bookings->filter(fn($b) => !in_array($b->status, ['booked', 'reserved', 'occupied']));

        $categories = DB::table('vehicle_categories')->get();

        return view('user.dashboard', compact('parkingSpaces', 'activeBookings', 'pastBookings', 'categories'));
    }
}