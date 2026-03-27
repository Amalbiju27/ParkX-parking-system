<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OwnerFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup Owner and Parking Space
        $this->owner = User::factory()->create(['role' => 'owner']);
        
        $this->category = DB::table('vehicle_categories')->insertGetId([
            'name' => 'Car',
            'base_charge' => 40,
            'hourly_rate' => 50,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->space = DB::table('parking_spaces')->insertGetId([
            'owner_id' => $this->owner->id,
            'name' => 'Owner Space',
            'location' => 'Business District',
            'capacity' => 10,
            'available_slots' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->slot = DB::table('parking_slots')->insertGetId([
            'parking_space_id' => $this->space,
            'slot_number' => 'O1',
            'status' => 'available',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /** @test */
    public function an_owner_can_record_a_manual_vehicle_entry()
    {
        $this->actingAs($this->owner);

        $response = $this->post('/owner/vehicle-entry', [
            'vehicle_number' => 'OWN-777',
            'category_id' => $this->category,
            'parking_space_id' => $this->space,
            'slot_id' => $this->slot,
            'start_time' => now()->toDateTimeString(),
            'end_time' => now()->addHours(2)->toDateTimeString(),
            'total_amount' => 100,
        ]);

        $this->assertDatabaseHas('vehicles', [
            'vehicle_number' => 'OWN-777',
            'status' => 'parked',
        ]);
        
        $response->assertRedirect();
    }

    /** @test */
    public function an_owner_can_process_vehicle_exit()
    {
        $this->actingAs($this->owner);

        // First enter
        $vehicleId = DB::table('vehicles')->insertGetId([
            'vehicle_number' => 'EXIT-123',
            'category_id' => $this->category,
            'parking_space_id' => $this->space,
            'slot_id' => $this->slot,
            'entry_time' => now()->subHours(2),
            'expected_exit_time' => now()->subHours(1),
            'status' => 'parked',
            'created_at' => now()->subHours(2),
        ]);

        $response = $this->post("/owner/vehicle-exit/{$vehicleId}/manual");

        $response->assertSessionMissing('error');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicleId,
            'status' => 'exited',
        ]);

        $response->assertRedirect();
    }

    /** @test */
    public function an_owner_can_view_the_receipt_printing_page()
    {
        $this->actingAs($this->owner);

        $vehicleId = DB::table('vehicles')->insertGetId([
            'vehicle_number' => 'RCP-123',
            'category_id' => $this->category,
            'parking_space_id' => $this->space,
            'slot_id' => $this->slot,
            'entry_time' => now(),
            'status' => 'parked',
            'created_at' => now(),
        ]);

        $response = $this->get("/owner/print-receipt/manual/{$vehicleId}");

        $response->assertStatus(200);
        $response->assertViewIs('owner.receipt.thermal');
        $response->assertSee('RCP-123');
    }

    /** @test */
    public function owner_dashboard_calculates_available_slots_correctly()
    {
        $this->actingAs($this->owner);

        // Current occupied: 0, Capacity: 10
        $response = $this->get('/owner');
        $response->assertSee('10'); // Total capacity
        $response->assertSee('10'); // Available slots

        // Occupy one slot
        DB::table('parking_slots')->where('id', $this->slot)->update(['status' => 'occupied']);

        $response = $this->get('/owner');
        // Available should be 9
        $response->assertSee('9');
    }

    /** @test */
    public function a_regular_user_cannot_access_owner_dashboard()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $response = $this->get('/owner');
        $response->assertStatus(302);
    }
}
