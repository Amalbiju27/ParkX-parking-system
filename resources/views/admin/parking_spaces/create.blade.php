@extends('layouts.app')

@section('title', 'Add Parking Space')

@section('content')
    <div class="relative z-20 max-w-lg mx-auto px-4 py-16">
        
        <!-- Header -->
        <div class="card-minimal p-6 lg:p-8 mb-8 animate-slide-up">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-black rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-parking text-lg"></i>
                </div>
                <div>
                    <h1 class="text-3xl lg:text-4xl font-black text-black tracking-tighter uppercase">ADD PARKING SPACE</h1>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mt-1">CREATE NEW PARKING FACILITY</p>
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
            <form method="POST" action="/admin/parking-spaces" class="space-y-6">
                @csrf
                
                <!-- Parking Name -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 flex items-center gap-2">
                        <i class="fas fa-sign-hanging"></i> PARKING NAME
                    </label>
                    <input type="text" 
                           name="name" 
                           placeholder="E.G. PREMIUM PARKING LOT A"
                           value="{{ old('name') }}"
                           class="w-full h-14 px-4 border border-gray-300 bg-white text-black placeholder-gray-400 font-mono focus:border-black transition-all">
                </div>

                <!-- Location -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt"></i> LOCATION
                    </label>
                    <input type="text" 
                           name="location" 
                           placeholder="E.G. GATE 2, SECTOR 5, KOCHI"
                           value="{{ old('location') }}"
                           class="w-full h-14 px-4 border border-gray-300 bg-white text-black placeholder-gray-400 font-mono focus:border-black transition-all">
                </div>

                <!-- Capacity -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 flex items-center gap-2">
                        <i class="fas fa-hashtag"></i> TOTAL CAPACITY
                    </label>
                    <input type="number" 
                           name="capacity" 
                           placeholder="50"
                           min="1"
                           value="{{ old('capacity') }}"
                           class="w-full h-14 px-4 border border-gray-300 bg-white text-black placeholder-gray-400 font-mono focus:border-black transition-all">
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <button type="submit" 
                            class="btn-primary flex-1 h-14 px-6 text-sm flex items-center justify-center gap-2 uppercase w-full">
                        <i class="fas fa-save"></i>
                        SAVE SPACE
                    </button>
                    
                    <a href="/admin/parking-spaces" 
                       class="btn-secondary flex-1 h-14 px-6 text-sm flex items-center justify-center gap-2 uppercase text-center border-gray-300 hover:border-black text-black transition-colors w-full">
                        <i class="fas fa-arrow-left"></i>
                        BACK
                    </a>
                </div>
            </form>
        </div>
@endsection
