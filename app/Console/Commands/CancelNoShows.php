<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CancelNoShows extends Command
{
    protected $signature = 'bookings:cancel-no-shows';
    protected $description = 'Cancel bookings where the user did not show up on time';

    public function handle()
    {
        $now = Carbon::now();
        
        $bookings = DB::table('bookings')
            ->whereNull('scanned_at')
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->get();

        $count = 0;
        foreach ($bookings as $booking) {
            if (!$booking->start_time) continue;
            
            $effectiveStartTime = Carbon::parse($booking->start_time)->addMinutes($booking->extended_minutes ?? 0);
            
            if ($now->greaterThan($effectiveStartTime)) {
                DB::table('bookings')
                    ->where('id', $booking->id)
                    ->update(['status' => 'cancelled', 'updated_at' => $now]);

                if ($booking->slot_id) {
                    DB::table('parking_slots')
                        ->where('id', $booking->slot_id)
                        ->update(['status' => 'available', 'updated_at' => $now]);
                }
                $count++;
            }
        }

        $this->info("Cancelled {$count} no-show bookings.");
    }
}
