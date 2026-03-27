<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed an admin user
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function an_admin_can_create_a_parking_space()
    {
        $this->actingAs($this->admin);

        $response = $this->post('/admin/parking-spaces', [
            'name' => 'Elite Parking',
            'location' => 'Downtown',
            'capacity' => 20,
            'available_slots' => 20,
        ]);

        $this->assertDatabaseHas('parking_spaces', [
            'name' => 'Elite Parking',
            'location' => 'Downtown',
        ]);

        $response->assertRedirect();
    }

    /** @test */
    public function an_admin_can_create_a_vehicle_category()
    {
        $this->actingAs($this->admin);

        $response = $this->post('/admin/vehicle-categories', [
            'name' => 'SUV',
            'base_charge' => 60,
            'hourly_rate' => 80,
        ]);

        $this->assertDatabaseHas('vehicle_categories', [
            'name' => 'SUV',
            'base_charge' => 60,
        ]);

        $response->assertRedirect();
    }

    /** @test */
    public function an_admin_can_onboard_a_new_owner()
    {
        $this->actingAs($this->admin);

        $response = $this->post('/admin/owners', [
            'name' => 'Test Owner',
            'email' => 'newowner@parkx.com',
            'password' => 'password123',
            'contact' => '1234567890',
            'address' => '123 Owner Lane',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newowner@parkx.com',
            'role' => 'owner',
        ]);

        $response->assertRedirect();
    }

    /** @test */
    public function an_admin_can_assign_an_owner_to_a_parking_space()
    {
        $this->actingAs($this->admin);

        $owner = User::factory()->create(['role' => 'owner']);
        $space = DB::table('parking_spaces')->insertGetId([
            'name' => 'Unassigned Space',
            'location' => 'Outskirts',
            'capacity' => 5,
            'available_slots' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->post("/admin/parking-spaces/{$space}/assign-owner", [
            'owner_id' => $owner->id,
        ]);

        $this->assertDatabaseHas('parking_spaces', [
            'id' => $space,
            'owner_id' => $owner->id,
        ]);

        $response->assertRedirect();
    }

    /** @test */
    public function a_regular_user_cannot_access_admin_dashboard()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $response = $this->get('/admin');
        $response->assertStatus(302);
    }
}
