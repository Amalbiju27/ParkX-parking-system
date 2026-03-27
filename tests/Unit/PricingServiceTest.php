<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\PricingService;

class PricingServiceTest extends TestCase
{
    private $pricingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pricingService = new PricingService();
    }

    /** @test */
    public function it_calculates_fee_correctly_based_on_hours_and_rate()
    {
        $fee = $this->pricingService->calculateFee(2, 50.0, 40.0);
        $this->assertEquals(100.0, $fee);
    }

    /** @test */
    public function it_enforces_base_charge_if_calculated_fee_is_lower()
    {
        $fee = $this->pricingService->calculateFee(0, 50.0, 40.0);
        $this->assertEquals(40.0, $fee);
    }

    /** @test */
    public function it_calculates_duration_in_hours_correctly()
    {
        // 2 hours 30 mins -> should be 3 hours (ceil)
        $hours = $this->pricingService->calculateDurationInHours('2026-03-27', '10:00', '12:30');
        $this->assertEquals(3, $hours);
    }

    /** @test */
    public function it_enforces_minimum_one_hour_duration()
    {
        // 10 mins -> should be 1 hour
        $hours = $this->pricingService->calculateDurationInHours('2026-03-27', '10:00', '10:10');
        $this->assertEquals(1, $hours);
    }

    /** @test */
    public function it_calculates_extension_cost_correctly()
    {
        $cost = $this->pricingService->calculateExtensionCost(3, 60.0);
        $this->assertEquals(180.0, $cost);
    }

    /** @test */
    public function it_calculates_penalty_for_overdue_exit()
    {
        $expected = \Carbon\Carbon::parse('2026-03-27 12:00:00');
        $actual = \Carbon\Carbon::parse('2026-03-27 12:15:00'); // 15 mins late
        
        $penalty = $this->pricingService->calculatePenalty($expected, $actual, 2.0);
        $this->assertEquals(30.0, $penalty);
    }

    /** @test */
    public function it_returns_zero_penalty_for_on_time_exit()
    {
        $expected = \Carbon\Carbon::parse('2026-03-27 12:00:00');
        $actual = \Carbon\Carbon::parse('2026-03-27 11:55:00');
        
        $penalty = $this->pricingService->calculatePenalty($expected, $actual, 2.0);
        $this->assertEquals(0.0, $penalty);
    }

    /** @test */
    public function it_calculates_total_with_penalty()
    {
        $total = $this->pricingService->calculateTotalWithPenalty(100.0, 30.0);
        $this->assertEquals(130.0, $total);
    }
}
