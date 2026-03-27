<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BookingFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed necessary data
        DB::table('vehicle_categories')->insert([
            'id' => 1,
            'name' => 'Car',
            'base_charge' => 40,
            'hourly_rate' => 50,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create an owner user
        DB::table('users')->insert([
            'id' => 2,
            'name' => 'Owner User',
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
            'role' => 'owner',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('parking_spaces')->insert([
            'id' => 1,
            'owner_id' => 2,
            'name' => 'Main Street Parking',
            'location' => '123 Main St',
            'capacity' => 10,
            'available_slots' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('parking_slots')->insert([
            'id' => 1,
            'parking_space_id' => 1,
            'slot_number' => 'A1',
            'slot_type' => 'car',
            'status' => 'available',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /** @test */
    public function a_user_can_book_a_parking_slot()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $response = $this->post('/user/book', [
            'parking_space_id' => 1,
            'slot_id' => 1,
            'vehicle_number' => 'ABC-1234',
            'vehicle_category_id' => 1,
            'booking_date' => now()->format('Y-m-d'),
            'start_time' => '10:00',
            'end_time' => '12:00',
        ]);

        // Assert booking exists
        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'slot_id' => 1,
            'vehicle_number' => 'ABC-1234',
        ]);

        // Assert slot is now occupied
        $this->assertDatabaseHas('parking_slots', [
            'id' => 1,
            'status' => 'occupied',
        ]);

        $response->assertRedirect();
    }

    /** @test */
    public function a_regular_user_cannot_access_owner_dashboard()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        // Assuming /owner is the prefix for owner dashboard
        $response = $this->get('/owner');
        
        // Assert redirected or forbidden (depending on your middleware)
        $response->assertStatus(302); // Laravel usually redirects unauthorized roles
    }

    /** @test */
    public function a_user_can_search_for_parking_spaces()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        // The dashboard shows all spaces by default
        $response = $this->get('/user');

        $response->assertStatus(200);
        $response->assertSee('Main Street Parking');
    }

    /** @test */
    public function a_user_can_view_their_booking_history()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        // Create a booking with correct columns
        DB::table('bookings')->insert([
            'user_id' => $user->id,
            'parking_space_id' => 1,
            'slot_id' => 1,
            'vehicle_number' => 'HIST-999',
            'status' => 'confirmed',
            'booking_date' => now()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'amount' => 50,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get('/user');

        $response->assertStatus(200);
        $response->assertSee('HIST-999');
    }
}
