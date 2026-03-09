<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParkingSpaceController extends Controller
{
    // Show all parking spaces
   public function index()
{
    $spaces = DB::table('parking_spaces')
        ->leftJoin('users', 'parking_spaces.owner_id', '=', 'users.id')
        ->select(
            'parking_spaces.*',
            'users.name as owner_name'
        )
        ->get();

    $owners = DB::table('users')
        ->where('role', 'owner')
        ->where('status', 1)
        ->get();

    return view('admin.parking_spaces.index', compact('spaces', 'owners'));
}


    // Show create form
    public function create()
    {
        return view('admin.parking_spaces.create');
    }

    // Store parking space
   public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'location' => 'required',
        'capacity' => 'required|integer|min:1',
    ]);

    // 1️⃣ Create parking space
   $spaceId = DB::table('parking_spaces')->insertGetId([
    'name' => $request->name,
    'location' => $request->location,
    'capacity' => $request->capacity,
    'available_slots' => $request->capacity, // ✅ REQUIRED
    'status' => 'active',
    'created_at' => now(),
    'updated_at' => now(),
]);


    // 2️⃣ Create slots based on capacity
    for ($i = 1; $i <= $request->capacity; $i++) {
        DB::table('parking_slots')->insert([
            'parking_space_id' => $spaceId,
            'slot_number' => 'S' . $i,   // ✅ USE SLOT NUMBER
            'status' => 'available',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    return redirect('/admin/parking-spaces')
        ->with('success', 'Parking space and slots created successfully');
}

   public function assignOwner(Request $request, $id)
{
    DB::table('parking_spaces')
        ->where('id', $id)
        ->update([
            'owner_id' => $request->owner_id,
            'updated_at' => now()
        ]);

    return back()->with('success', 'Owner assigned successfully');
}


}
