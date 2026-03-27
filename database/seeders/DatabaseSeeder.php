<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create a Default Admin
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@parkx.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

        // 2. Create a Default User
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        // 2. Create Vehicle Categories
        \Illuminate\Support\Facades\DB::table('vehicle_categories')->insert([
            ['name' => 'Car', 'base_charge' => 40, 'hourly_rate' => 50, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bike', 'base_charge' => 20, 'hourly_rate' => 25, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. Create a Parking Space
        $spaceId = \Illuminate\Support\Facades\DB::table('parking_spaces')->insertGetId([
            'name' => 'ParkX Central Hub',
            'location' => 'Sector 15, Near Metro',
            'capacity' => 10,
            'available_slots' => 10,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Create Parking Slots
        \Illuminate\Support\Facades\DB::table('parking_slots')->insert([
            ['parking_space_id' => $spaceId, 'slot_number' => 'A1', 'slot_type' => 'car', 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['parking_space_id' => $spaceId, 'slot_number' => 'A2', 'slot_type' => 'car', 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
