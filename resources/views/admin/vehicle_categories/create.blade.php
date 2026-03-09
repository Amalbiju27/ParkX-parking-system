@extends('layouts.app')

@section('title', 'Add Vehicle Category')

@section('content')
    <div class="relative z-20 max-w-lg mx-auto px-4 py-16">
        
        <!-- Header -->
        <div class="card-minimal p-6 lg:p-8 mb-8 animate-slide-up">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-black rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-tags text-lg"></i>
                </div>
                <div>
                    <h1 class="text-3xl lg:text-4xl font-black text-black tracking-tighter uppercase">ADD VEHICLE CATEGORY</h1>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mt-1">CONFIGURE PRICING FOR VEHICLE TYPES</p>
                </div>
            </div>
        </div>

        <!-- Main Form -->
        <div class="card-minimal p-8 animate-slide-up" style="animation-delay: 0.1s;">
            
            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-red-600 text-lg flex-shrink-0"></i>
                    <div>
                        <h4 class="font-bold text-red-800 text-xs uppercase tracking-widest mb-1">VALIDATION ERRORS</h4>
                        <ul class="text-red-600 text-xs font-bold space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="/admin/vehicle-categories" class="space-y-6">
                @csrf
                
                <!-- Category Name -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 flex items-center gap-2">
                        <i class="fas fa-tag"></i> CATEGORY NAME
                    </label>
                    <input type="text" 
                           name="name" 
                           placeholder="E.G. TWO WHEELER, FOUR WHEELER"
                           value="{{ old('name') }}"
                           required
                           class="w-full h-14 px-4 border border-gray-300 bg-white text-black placeholder-gray-400 font-mono focus:border-black transition-all">
                </div>

                <!-- Base Charge -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 flex items-center gap-2">
                        <i class="fas fa-coins"></i> BASE CHARGE (₹)
                    </label>
                    <input type="number" 
                           name="base_charge" 
                           step="0.01"
                           min="0"
                           placeholder="25.00"
                           value="{{ old('base_charge') }}"
                           required
                           class="w-full h-14 px-4 border border-gray-300 bg-white text-black placeholder-gray-400 font-mono focus:border-black transition-all">
                </div>

                <!-- Hourly Rate -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 flex items-center gap-2">
                        <i class="fas fa-clock"></i> HOURLY RATE (₹)
                    </label>
                    <input type="number" 
                           name="hourly_rate" 
                           step="0.01"
                           min="0"
                           placeholder="5.00"
                           value="{{ old('hourly_rate') }}"
                           required
                           class="w-full h-14 px-4 border border-gray-300 bg-white text-black placeholder-gray-400 font-mono focus:border-black transition-all">
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <button type="submit" 
                            class="btn-primary flex-1 h-14 px-6 text-sm flex items-center justify-center gap-2 uppercase w-full">
                        <i class="fas fa-save"></i>
                        SAVE CATEGORY
                    </button>
                    
                    <a href="/admin/vehicle-categories" 
                       class="btn-secondary flex-1 h-14 px-6 text-sm flex items-center justify-center gap-2 uppercase text-center border-gray-300 hover:border-black text-black transition-colors w-full">
                        <i class="fas fa-arrow-left"></i>
                        BACK
                    </a>
                </div>
            </form>
        </div>
@endsection
