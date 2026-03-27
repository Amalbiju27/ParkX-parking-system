<?php

namespace App\Services;

use Carbon\Carbon;

class PricingService
{
    /**
     * Calculate the parking fee based on duration, rate, and base charge.
     */
    public function calculateFee(int $hours, float $ratePerHour, float $baseCharge): float
    {
        $calculated = $hours * $ratePerHour;
        return (float) max($calculated, $baseCharge);
    }

    /**
     * Calculate duration in hours between two times on a specific date.
     * Minimum 1 hour is enforced.
     */
    public function calculateDurationInHours(string $date, string $startTime, string $endTime): int
    {
        $start = Carbon::parse($date . ' ' . $startTime);
        $end = Carbon::parse($date . ' ' . $endTime);

        if ($end->lessThanOrEqualTo($start)) {
            return 0; // Or handle as exception
        }

        $durationMinutes = $start->diffInMinutes($end);
        $durationHours = (int) ceil($durationMinutes / 60);

        return $durationHours < 1 ? 1 : $durationHours;
    }

    /**
     * Calculate extension cost.
     */
    public function calculateExtensionCost(int $extraHours, float $ratePerHour): float
    {
        return (float) ($extraHours * $ratePerHour);
    }

    /**
     * Calculate penalty based on overdue minutes.
     */
    public function calculatePenalty(\Carbon\Carbon $expectedExit, \Carbon\Carbon $actualExit, float $penaltyRatePerMinute): float
    {
        if ($actualExit->lessThanOrEqualTo($expectedExit)) {
            return 0.0;
        }

        $overdueMinutes = $expectedExit->diffInMinutes($actualExit);
        return (float) ($overdueMinutes * $penaltyRatePerMinute);
    }

    /**
     * Calculate final total.
     */
    public function calculateTotalWithPenalty(float $standardFee, float $penalty): float
    {
        return (float) ($standardFee + $penalty);
    }
}
