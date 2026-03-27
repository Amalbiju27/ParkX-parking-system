<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\AnalyticsService;
use Illuminate\Support\Collection;

class AnalyticsServiceTest extends TestCase
{
    private $analyticsService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->analyticsService = new AnalyticsService();
    }

    /** @test */
    public function it_aggregates_monthly_revenue_correctly()
    {
        $bookings = collect([
            (object)['amount' => 100.0, 'created_at' => '2026-01-15 10:00:00'],
            (object)['fee' => 50.0, 'created_at' => '2026-01-20 12:00:00'],
            (object)['amount' => 200.0, 'created_at' => '2026-02-05 08:00:00'],
        ]);

        $manuals = collect([
            (object)['charge' => 60.0, 'created_at' => '2026-01-10 09:00:00'],
            (object)['amount' => 30.0, 'created_at' => '2026-03-12 15:00:00'],
        ]);

        $revenue = $this->analyticsService->aggregateMonthlyRevenue($bookings, $manuals, 2026);

        $this->assertEquals(210.0, $revenue[0]); // Jan: 100 + 50 + 60
        $this->assertEquals(200.0, $revenue[1]); // Feb: 200
        $this->assertEquals(30.0, $revenue[2]);  // Mar: 30
        $this->assertEquals(0.0, $revenue[3]);   // Apr: 0
    }

    /** @test */
    public function it_ranks_parking_spaces_by_revenue()
    {
        $data = [
            ['name' => 'Space A', 'revenue' => 500.0],
            ['name' => 'Space B', 'revenue' => 1000.0],
            ['name' => 'Space C', 'revenue' => 750.0],
        ];

        $ranked = $this->analyticsService->rankParkingSpaces($data);

        $this->assertEquals('Space B', $ranked[0]['name']);
        $this->assertEquals('Space C', $ranked[1]['name']);
        $this->assertEquals('Space A', $ranked[2]['name']);
    }
}
