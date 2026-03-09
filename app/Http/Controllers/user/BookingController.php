<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    // Check availability dynamically
    public function checkAvailability(Request $request)
    {
        $spaceId = $request->query('space_id');

        $availableSlots = DB::table('parking_slots')
            ->where('parking_space_id', $spaceId)
            ->where('status', 'available')
            ->count();

        return response()->json([
            'available' => $availableSlots,
            'message' => $availableSlots > 0 ? 'Slots available' : 'No slots available'
        ]);
    }

    // Show booking form
    public function book($id)
    {
        $space = DB::table('parking_spaces')->where('id', $id)->first();

        if (!$space) {
            return redirect('/user')->with('error', 'Parking space not found.');
        }

        $slots = DB::table('parking_slots')
            ->where('parking_space_id', $id)
            ->where('status', 'available')
            ->get();

        $categories = DB::table('vehicle_categories')->get();

        return view('user.book', compact('space', 'slots', 'categories'));
    }
    
    // Explicit view routing alias to match user.book request payload target
    public function show($id) {
        $lot = DB::table('parking_spaces')->where('id', $id)->first();
        
        if (!$lot) {
            return redirect()->route('user.dashboard')->with('error', 'Parking location not found.');
        }
        
        return view('user.book', compact('lot')); 
    }

    // Store booking
    public function store(\Illuminate\Http\Request $request) 
    {
        // 1. Validate inputs strictly
        $request->validate([
            'parking_space_id' => 'required|integer',
            'slot_id' => 'required|integer',
            'vehicle_number' => 'required|string|max:255',
            'vehicle_category_id' => 'required|integer',
            'booking_date' => 'required|date|after_or_equal:today|before_or_equal:+1 day',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'vehicle_video' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo|max:10240',
        ]);
    
        try {
            // 2. Wrap in a database transaction for safety
            return \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
                
                // Handle Video Upload
                $videoPath = null;
                if ($request->hasFile('vehicle_video')) {
                    $videoPath = $request->file('vehicle_video')->store('vehicle_videos', 'public');
                }
                
                // 3. Conflict Checking (Prevent Double Booking)
                $conflict = \Illuminate\Support\Facades\DB::table('bookings')
                    ->where('parking_space_id', $request->parking_space_id)
                    ->where('slot_id', $request->slot_id)
                    ->where('booking_date', $request->booking_date)
                    ->whereIn('status', ['active', 'booked', 'parked'])
                    ->where(function ($query) use ($request) {
                        $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                              ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                              ->orWhere(function ($q) use ($request) {
                                  $q->where('start_time', '<=', $request->start_time)
                                    ->where('end_time', '>=', $request->end_time);
                              });
                    })->exists();
                    
                if ($conflict) {
                    throw new \Exception('This slot is already booked during your selected time. Please choose another slot or time.');
                }
                
                // 4. Calculate actual duration and cost
                $start = \Carbon\Carbon::parse($request->booking_date . ' ' . $request->start_time);
                $end = \Carbon\Carbon::parse($request->booking_date . ' ' . $request->end_time);
                
                $durationMinutes = $start->diffInMinutes($end);
                $durationHours = ceil($durationMinutes / 60); 
                if ($durationHours < 1) { $durationHours = 1; }
                
                $category = \Illuminate\Support\Facades\DB::table('vehicle_categories')->where('id', $request->vehicle_category_id)->first();
                $ratePerHour = $category ? $category->hourly_rate : 50; 
                $baseCharge = $category ? $category->base_charge : 40;
                
                $calculatedFee = $durationHours * $ratePerHour;
                $totalAmount = max($calculatedFee, $baseCharge);
                
                // 5. Create the Booking entirely using DB table to bypass model classes
                $bookingId = \Illuminate\Support\Facades\DB::table('bookings')->insertGetId([
                    'user_id' => auth()->id(),
                    'parking_space_id' => $request->parking_space_id,
                    'slot_id' => $request->slot_id,
                    'vehicle_number' => $request->vehicle_number,
                    'vehicle_category_id' => $request->vehicle_category_id,
                    'vehicle_video' => $videoPath,
                    'amount' => $totalAmount,
                    'duration_hours' => $durationHours,
                    'booking_date' => $request->booking_date,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'status' => 'booked',
                    'payment_status' => 'pending',
                    'expires_at' => now()->addMinutes(15),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                // 6. Update slot status unconditionally
                \Illuminate\Support\Facades\DB::table('parking_slots')->where('id', $request->slot_id)->update(['status' => 'occupied']);
                
                return redirect('/user/booking/' . $bookingId . '/ticket')->with('success', 'Space reserved! Total: ₹' . $totalAmount);
            });
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // Show mock payment page
    public function payCheckout($id)
    {
        $booking = DB::table('bookings')
            ->where('id', $id)
            ->where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->first();

        if (!$booking || $booking->payment_status === 'paid') {
            return redirect('/user')->with('error', 'Invalid or already paid booking.');
        }

        return view('user.payment', compact('booking'));
    }

    // Process mock payment
    public function processPayment(Request $request, $id)
    {
        $booking = DB::table('bookings')
            ->where('id', $id)
            ->where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->first();

        if (!$booking || $booking->payment_status === 'paid') {
            return redirect('/user')->with('error', 'Invalid or already paid booking.');
        }

        // Mock payment logic - just mark as paid
        DB::table('bookings')->where('id', $id)->update([
            'payment_status' => 'paid',
            // Update expires_at to be active for the booked duration instead of the 15m grace period
            'expires_at' => now()->addHours($booking->duration_hours),
        ]);

        return redirect('/user')->with('success', 'Payment successful!');
    }

    // Show QR code ticket
    public function ticket($id)
    {
        $booking = DB::table('bookings')
            ->join('parking_spaces', 'bookings.parking_space_id', '=', 'parking_spaces.id')
            ->select('bookings.*', 'parking_spaces.name as space_name', 'parking_spaces.location as location')
            ->where('bookings.id', $id)
            ->where('bookings.user_id', \Illuminate\Support\Facades\Auth::id())
            ->first();

        if (!$booking) {
            return redirect('/user')->with('error', 'Booking not found.');
        }

        return view('user.ticket', compact('booking'));
    }

    // Cancel Booking
    public function cancel($id)
    {
        $booking = DB::table('bookings')
            ->where('id', $id)
            ->where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->whereIn('status', ['booked', 'reserved', 'occupied'])
            ->first();

        if (!$booking) {
            return redirect('/user')->with('error', 'Booking cannot be cancelled.');
        }

        // Cancel the booking
        DB::table('bookings')->where('id', $id)->update(['status' => 'cancelled']);

        // Free up the slot
        DB::table('parking_slots')->where('id', $booking->slot_id)->update(['status' => 'available']);

        return redirect('/user')->with('success', 'Booking cancelled successfully.');
    }

    // Show extend booking form
    public function extendForm($id)
    {
        $booking = DB::table('bookings')
            ->join('parking_spaces', 'bookings.parking_space_id', '=', 'parking_spaces.id')
            ->select('bookings.*', 'parking_spaces.name as space_name')
            ->where('bookings.id', $id)
            ->where('bookings.user_id', \Illuminate\Support\Facades\Auth::id())
            ->whereIn('bookings.status', ['booked', 'reserved', 'occupied'])
            ->first();

        if (!$booking) {
            return redirect('/user')->with('error', 'Booking cannot be extended.');
        }

        return view('user.extend', compact('booking'));
    }

    // Extend booking duration
    public function extend(Request $request, $id)
    {
        $request->validate([
            'extra_hours' => 'required|integer|min:1|max:24',
        ]);

        $booking = DB::table('bookings')
            ->where('id', $id)
            ->where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->whereIn('status', ['booked', 'reserved', 'occupied'])
            ->first();

        if (!$booking) {
            return redirect('/user')->with('error', 'Booking cannot be extended.');
        }

        // Update expires_at and duration
        $newExpiresAt = \Carbon\Carbon::parse($booking->expires_at)->addHours($request->integer('extra_hours'));
        $newDuration = $booking->duration_hours + $request->integer('extra_hours');

        DB::table('bookings')->where('id', $id)->update([
            'expires_at' => $newExpiresAt,
            'duration_hours' => $newDuration,
        ]);

        return redirect('/user')->with('success', 'Booking extended successfully by ' . $request->integer('extra_hours') . ' hour(s).');
    }
}