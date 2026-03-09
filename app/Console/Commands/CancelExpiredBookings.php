<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CancelExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel bookings that are not paid or occupied within 15 minutes of creation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredBookings = \Illuminate\Support\Facades\DB::table('bookings')
            ->whereIn('status', ['booked', 'reserved'])
            ->where('payment_status', 'pending')
            ->where('created_at', '<', now()->subMinutes(15))
            ->get();

        foreach ($expiredBookings as $booking) {
            \Illuminate\Support\Facades\DB::table('bookings')
                ->where('id', $booking->id)
                ->update(['status' => 'cancelled']);

            \Illuminate\Support\Facades\DB::table('parking_slots')
                ->where('id', $booking->slot_id)
                ->update(['status' => 'available']);
        }

        $this->info(count($expiredBookings) . ' expired bookings cancelled.');
    }
}
