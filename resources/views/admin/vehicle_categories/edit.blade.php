@extends('layouts.app')

@section('title', 'Edit Vehicle Category')

@section('content')
<div class="max-w-lg mx-auto py-16">
    <div class="card-minimal p-8 rounded-none animate-slide-up">
        <h2 class="text-3xl font-black text-black mb-8 uppercase tracking-tighter">EDIT VEHICLE CATEGORY</h2>

        <form method="POST" action="/admin/vehicle-categories/{{ $category->id }}/update" class="space-y-6">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">CATEGORY NAME</label>
                <input type="text" name="name" value="{{ $category->name }}" class="w-full h-14 px-4 border border-gray-300 bg-white text-black font-mono focus:border-black transition-all" required>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">BASE CHARGE (₹)</label>
                <input type="number" step="0.01" name="base_charge" value="{{ $category->base_charge }}" class="w-full h-14 px-4 border border-gray-300 bg-white text-black font-mono focus:border-black transition-all" required>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">HOURLY RATE (₹)</label>
                <input type="number" step="0.01" name="hourly_rate" value="{{ $category->hourly_rate }}" class="w-full h-14 px-4 border border-gray-300 bg-white text-black font-mono focus:border-black transition-all" required>
            </div>

            <button type="submit" class="w-full btn-primary py-4 text-sm font-bold tracking-widest uppercase mt-4">
                UPDATE CATEGORY
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="/admin/vehicle-categories" class="text-sm font-bold tracking-widest uppercase text-gray-500 hover:text-black transition-colors">
                <i class="fas fa-arrow-left"></i> BACK
            </a>
        </div>
    </div>
</div>
@endsection
