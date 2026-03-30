<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $ownerId = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | 1ï¸âƒ£ Parking spaces with LIVE availability
        |--------------------------------------------------------------------------
        */
        $parkingSpaces = DB::table('parking_spaces')
            ->where('owner_id', $ownerId)
            ->get();

        $spaceIds = $parkingSpaces->pluck('id');

        foreach ($parkingSpaces as $space) {
            $space->available_slots_count = DB::table('parking_slots')
                ->where('parking_space_id', $space->id)
                ->where('status', 'available')
                ->count();
            
            // Wait to calculate this until we count physically occupied below.
        }

        /*
        |--------------------------------------------------------------------------
        | 2ï¸âƒ£ Slot details + Tooltip Data (FIXED)
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
        | 3ï¸âƒ£ Current parked vehicles
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
        | 4ï¸âƒ£ Vehicle history (search + filter)
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
        | 5ï¸âƒ£ Revenue (Unified)
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
        | 6ï¸âƒ£ Live Counts - Refined for "Physically Occupied"
        |--------------------------------------------------------------------------
        */
        $totalCapacity = DB::table('parking_slots')
            ->whereIn('parking_space_id', $spaceIds)
            ->count();

        $availableCount = DB::table('parking_slots')
            ->whereIn('parking_space_id', $spaceIds)
            ->where('status', 'available')
            ->count();

        // Count manual gate entries
        $manualOccupied = DB::table('vehicles')
            ->whereIn('parking_space_id', $spaceIds)
            ->where('status', 'parked')
            ->count();
            
        // Count online bookings that have physically arrived
        $bookingOccupied = DB::table('bookings')
            ->whereIn('parking_space_id', $spaceIds)
            ->whereNotNull('scanned_at')
            ->whereIn('status', ['booked', 'occupied', 'reserved'])
            ->count();
            
        $occupiedCount = $manualOccupied + $bookingOccupied;
        
        // Re-assign per space for the UI 
        foreach ($parkingSpaces as $space) {
            $spaceManual = DB::table('vehicles')->where('parking_space_id', $space->id)->where('status', 'parked')->count();
            $spaceBooked = DB::table('bookings')->where('parking_space_id', $space->id)->whereNotNull('scanned_at')->whereIn('status', ['booked', 'occupied', 'reserved'])->count();
            $space->occupied_slots = $spaceManual + $spaceBooked;
            
            // FIX: Available = Capacity - Occupied
            $space->available_slots_count = max(0, $space->capacity - $space->occupied_slots);
        }


        /*
        |--------------------------------------------------------------------------
        | 7ï¸âƒ£ Current & Upcoming Bookings Separation
        |--------------------------------------------------------------------------
        */
        // All active bookings for these spaces
        $allActiveBookings = DB::table('bookings')
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
            ->orderBy('bookings.start_time', 'asc') // Sort by when they should arrive
            ->get();
            
        $physicallyOccupiedBookings = collect([]);
        $upcomingBookings = collect([]);
        
        foreach ($allActiveBookings as $b) {
            if ($b->scanned_at !== null) {
                // They have checked in at the gate
                $physicallyOccupiedBookings->push($b);
            } else {
                // They have booked but not yet arrived
                $upcomingBookings->push($b);
            }
        }


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
            'physicallyOccupiedBookings',
            'upcomingBookings'
        ));
    }

    public function checkIn($id)
    {
        $ownerId = Auth::id();
        $spaceIds = DB::table('parking_spaces')->where('owner_id', $ownerId)->pluck('id')->toArray();

        $booking = DB::table('bookings')
            ->where('id', $id)
            ->whereIn('parking_space_id', $spaceIds)
            ->first();

        if (!$booking) {
            return response()->json(['status' => 'error', 'message' => 'Booking not found or unauthorised.']);
        }

        if (in_array($booking->status, ['cancelled', 'completed'])) {
            return response()->json(['status' => 'error', 'message' => 'Booking is already ' . $booking->status . '.']);
        }

        if (now()->lessThan(Carbon::parse($booking->start_time)->subMinutes(15))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Too early! This ticket is valid from ' . Carbon::parse($booking->start_time)->format('h:i A on d M Y')
            ]);
        }

        if (now()->greaterThan(Carbon::parse($booking->end_time))) {
            return response()->json([
                'status' => 'error',
                'message' => 'This ticket has already expired.'
            ]);
        }

        if ($booking->scanned_at !== null) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Already scanned at ' . Carbon::parse($booking->scanned_at)->format('h:i A'), 
                'payment_status' => $booking->payment_status
            ]);
        }

        DB::table('bookings')->where('id', $id)->update([
            'scanned_at' => now(),
            'status' => 'occupied',
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => 'success', 
            'message' => 'Vehicle successfully checked in!',
            'payment_status' => $booking->payment_status
        ]);
    }

    public function manualCheckIn($id)
    {
        $ownerId = Auth::id();
        $spaceIds = DB::table('parking_spaces')->where('owner_id', $ownerId)->pluck('id')->toArray();

        $booking = DB::table('bookings')
            ->where('id', $id)
            ->whereIn('parking_space_id', $spaceIds)
            ->first();

        if (!$booking) {
            return back()->with('error', 'Booking not found or unauthorised.');
        }

        if (in_array($booking->status, ['cancelled', 'completed'])) {
            return back()->with('error', 'Booking is already ' . $booking->status . '.');
        }

        if (now()->lessThan(Carbon::parse($booking->start_time)->subMinutes(15))) {
            return back()->with('error', 'Too early! This ticket is valid from ' . Carbon::parse($booking->start_time)->format('h:i A on d M Y'));
        }

        if (now()->greaterThan(Carbon::parse($booking->end_time))) {
            return back()->with('error', 'This ticket has already expired.');
        }

        if ($booking->scanned_at !== null) {
            return back()->with('error', 'Already scanned at ' . Carbon::parse($booking->scanned_at)->format('h:i A'));
        }

        DB::table('bookings')->where('id', $id)->update([
            'scanned_at' => now(),
            'status' => 'occupied',
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Vehicle manually checked in successfully!');
    }

    public function checkInManualByPin(Request $request)
    {
        $request->validate([
            'ticket_number' => 'required|string|size:6',
        ]);

        $ownerId = Auth::id();
        $spaceIds = DB::table('parking_spaces')->where('owner_id', $ownerId)->pluck('id')->toArray();

        $booking = DB::table('bookings')
            ->where('ticket_number', strtoupper($request->ticket_number))
            ->whereIn('parking_space_id', $spaceIds)
            ->first();

        if (!$booking) {
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Invalid Ticket PIN.']);
            }
            return back()->with('error', 'Invalid Ticket PIN.');
        }

        if (in_array($booking->status, ['cancelled', 'completed'])) {
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Booking is already ' . $booking->status . '.']);
            }
            return back()->with('error', 'Booking is already ' . $booking->status . '.');
        }

        if (now()->lessThan(Carbon::parse($booking->start_time)->subMinutes(15))) {
            $msg = 'Too early! This ticket is valid from ' . Carbon::parse($booking->start_time)->format('h:i A on d M Y');
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => $msg]);
            }
            return back()->with('error', $msg);
        }

        if (now()->greaterThan(Carbon::parse($booking->end_time))) {
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => 'This ticket has already expired.']);
            }
            return back()->with('error', 'This ticket has already expired.');
        }

        if ($booking->scanned_at !== null) {
            $msg = 'Already scanned at ' . Carbon::parse($booking->scanned_at)->format('h:i A');
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => $msg, 'payment_status' => $booking->payment_status]);
            }
            return back()->with('error', $msg);
        }

        DB::table('bookings')->where('id', $booking->id)->update([
            'scanned_at' => now(),
            'status' => 'occupied',
            'updated_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success', 
                'message' => 'Vehicle successfully checked in!',
                'payment_status' => $booking->payment_status
            ]);
        }

        return back()->with('success', 'Vehicle manually checked in successfully!');
    }

    /**
     * Print Thermal Receipt (80mm)
     */
    public function printReceipt($type, $id)
    {
        $ownerId = Auth::id();
        $spaceIds = DB::table('parking_spaces')->where('owner_id', $ownerId)->pluck('id')->toArray();

        $data = null;
        $title = "PARKX RECEIPT";

        if ($type === 'manual') {
            $data = DB::table('vehicles')
                ->join('parking_spaces', 'vehicles.parking_space_id', '=', 'parking_spaces.id')
                ->leftJoin('parking_slots', 'vehicles.slot_id', '=', 'parking_slots.id')
                ->where('vehicles.id', $id)
                ->whereIn('vehicles.parking_space_id', $spaceIds)
                ->select(
                    'vehicles.vehicle_number',
                    'vehicles.charge as amount',
                    'vehicles.entry_time as date',
                    'parking_spaces.name as space_name',
                    'parking_slots.slot_number'
                )
                ->first();
        } else {
            $data = DB::table('bookings')
                ->join('parking_spaces', 'bookings.parking_space_id', '=', 'parking_spaces.id')
                ->leftJoin('parking_slots', 'bookings.slot_id', '=', 'parking_slots.id')
                ->where('bookings.id', $id)
                ->whereIn('bookings.parking_space_id', $spaceIds)
                ->select(
                    'bookings.vehicle_number',
                    'bookings.amount',
                    'bookings.created_at as date', // or start_time
                    'parking_spaces.name as space_name',
                    'parking_slots.slot_number'
                )
                ->first();
        }

        if (!$data) {
            abort(404, "Receipt not found or unauthorised.");
        }

        return view('owner.receipt.thermal', compact('data', 'type', 'id'));
    }
}
