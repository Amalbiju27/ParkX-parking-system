<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OwnerController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Create Owner
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        return view('admin.owners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'contact' => 'required',
        ]);

        // Create owner user
        $userId = DB::table('users')->insertGetId([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'owner',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Owner profile
        DB::table('parking_space_owners')->insert([
            'user_id' => $userId,
            'contact' => $request->contact,
            'address' => $request->address,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/admin/parking-spaces')
            ->with('success', 'Owner created successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Owner
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $owner = DB::table('users')
            ->where('id', $id)
            ->where('role', 'owner')
            ->first();

        return view('admin.owners.edit', compact('owner'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'status' => 'required'
        ]);

        DB::table('users')->where('id', $id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->status,
            'updated_at' => now()
        ]);

        return redirect('/admin/parking-spaces')
            ->with('success', 'Owner updated successfully');
    }
}
