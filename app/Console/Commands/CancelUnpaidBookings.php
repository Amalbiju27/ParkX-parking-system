<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CancelUnpaidBookings extends Command
{
    protected $signature = 'bookings:cancel-unpaid';
    protected $description = 'Cancel bookings that have not been paid within 15 minutes of creation';

    public function handle()
    {
        $expirationTime = Carbon::now()->subMinutes(15);

        $expiredBookings = DB::table('bookings')
            ->where('payment_status', 'pending')
            ->where('created_at', '<', $expirationTime)
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->get();

        $count = 0;
        foreach ($expiredBookings as $booking) {
            DB::table('bookings')
                ->where('id', $booking->id)
                ->update(['status' => 'cancelled', 'updated_at' => Carbon::now()]);

            if ($booking->slot_id) {
                DB::table('parking_slots')
                    ->where('id', $booking->slot_id)
                    ->update(['status' => 'available', 'updated_at' => Carbon::now()]);
            }
            $count++;
        }

        $this->info("Cancelled {$count} unpaid bookings.");
    }
}
