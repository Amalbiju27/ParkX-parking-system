<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleCategoryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | List Categories
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $categories = DB::table('vehicle_categories')->get();
        return view('admin.vehicle_categories.index', compact('categories'));
    }

    /*
    |--------------------------------------------------------------------------
    | Create Category
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        return view('admin.vehicle_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'base_charge' => 'required|numeric',
            'hourly_rate' => 'required|numeric',
        ]);

        DB::table('vehicle_categories')->insert([
            'name' => $request->name,
            'base_charge' => $request->base_charge,
            'hourly_rate' => $request->hourly_rate,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/admin/vehicle-categories')
            ->with('success', 'Vehicle category added');
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Category
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $category = DB::table('vehicle_categories')
            ->where('id', $id)
            ->first();

        return view('admin.vehicle_categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'base_charge' => 'required|numeric',
            'hourly_rate' => 'required|numeric'
        ]);

        DB::table('vehicle_categories')->where('id', $id)->update([
            'name' => $request->name,
            'base_charge' => $request->base_charge,
            'hourly_rate' => $request->hourly_rate,
            'updated_at' => now()
        ]);

        return redirect('/admin/vehicle-categories')
            ->with('success', 'Category updated');
    }
}
