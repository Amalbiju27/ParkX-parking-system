<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class AnalyticsService
{
    /**
     * Aggregate monthly revenue for a given year.
     * Returns an array of 12 decimals.
     */
    public function aggregateMonthlyRevenue(Collection $bookings, Collection $manuals, int $year): array
    {
        $monthlyRevenue = array_fill(0, 12, 0.0);

        foreach ($bookings as $b) {
            $date = Carbon::parse($b->created_at);
            if ($date->year === $year) {
                $monthIndex = $date->month - 1;
                $amount = (float) ($b->total_amount ?? $b->amount ?? $b->fee ?? $b->price ?? $b->charge ?? 0);
                $monthlyRevenue[$monthIndex] += $amount;
            }
        }

        foreach ($manuals as $m) {
            $date = Carbon::parse($m->created_at);
            if ($date->year === $year) {
                $monthIndex = $date->month - 1;
                $amount = (float) ($m->total_amount ?? $m->amount ?? $m->fee ?? $m->price ?? $m->charge ?? 0);
                $monthlyRevenue[$monthIndex] += $amount;
            }
        }

        return $monthlyRevenue;
    }

    /**
     * Rank parking spaces by revenue.
     */
    public function rankParkingSpaces(array $processedData): array
    {
        usort($processedData, function ($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });

        return $processedData;
    }
}
