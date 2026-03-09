@extends('layouts.app')

@section('title', 'Vehicle Entry')

@section('content')
    <style>
        /* NEW STYLES FOR SLOT GRID */
        .slot-box {
            transition: all 0.2s ease;
        }
        .slot-available:hover {
            background-color: #000;
            color: #fff;
            border-color: #000;
        }
        .slot-selected {
            background-color: #000 !important;
            color: #fff !important;
            border-color: #000 !important;
        }
    </style>

    <div class="relative z-20 max-w-lg mx-auto px-4 py-16">
        
        <div class="card-minimal p-6 lg:p-8 mb-8 animate-slide-up">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-black rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-car-side text-lg"></i>
                </div>
                <div>
                    <h1 class="text-3xl lg:text-4xl font-black text-black tracking-tighter uppercase">VEHICLE ENTRY</h1>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mt-1">QUICK PARKING TERMINAL</p>
                </div>
            </div>
        </div>

        <div class="card-minimal p-8 animate-slide-up" style="animation-delay: 0.1s;">
            
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                    <span class="font-bold text-red-800 text-xs uppercase tracking-widest">{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 flex items-center gap-3">
                    <i class="fas fa-check-circle text-green-600"></i>
                    <span class="font-bold text-green-800 text-xs uppercase tracking-widest">{{ session('success') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('owner.vehicle.store') }}" class="space-y-6">
                @csrf
                
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 flex items-center gap-2">
                        <i class="fas fa-parking"></i> PARKING SPACE
                    </label>
                    <select name="parking_space_id" id="parking_space" required onchange="fetchSlots()"
                            class="w-full h-14 px-4 border border-gray-300 bg-white text-black font-mono focus:border-black transition-all appearance-none rounded-none">
                        <option value="" disabled selected>-- SELECT SPACE --</option>
                        @foreach($parkingSpaces as $space)
                            <option value="{{ $space->id }}">
                                {{ $space->name }} • {{ $space->location }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 flex items-center gap-2">
                        <i class="fas fa-th"></i> SELECT SLOT
                    </label>
                    
                    <input type="hidden" name="slot_id" id="selected_slot_id" required>

                    <div id="slots_grid" class="grid grid-cols-4 sm:grid-cols-5 gap-3 p-4 bg-gray-50 border border-gray-200 max-h-64 overflow-y-auto">
                        <div class="col-span-full text-center text-gray-500 py-4 text-xs font-bold uppercase tracking-widest">
                            SELECT A PARKING SPACE TO VIEW SLOTS
                        </div>
                    </div>
                    
                    <div class="flex gap-4 text-xs font-bold tracking-widest text-gray-500 justify-end pt-2 uppercase">
                        <div class="flex items-center gap-2"><div class="w-3 h-3 bg-white border border-gray-300"></div> AVAILABLE</div>
                        <div class="flex items-center gap-2"><div class="w-3 h-3 bg-gray-200 border border-gray-300"></div> OCCUPIED</div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 flex items-center gap-2">
                        <i class="fas fa-tags"></i> CATEGORY
                    </label>
                    <select name="category_id" required 
                            class="w-full h-14 px-4 border border-gray-300 bg-white text-black font-mono focus:border-black transition-all appearance-none rounded-none">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ strtoupper($cat->name) }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 flex items-center gap-2">
                        <i class="fas fa-clock"></i> DURATION
                    </label>
                    <select name="duration" required 
                            class="w-full h-14 px-4 border border-gray-300 bg-white text-black font-mono focus:border-black transition-all appearance-none rounded-none">
                        <option value="1">1 HOUR</option>
                        <option value="2">2 HOURS</option>
                        <option value="3">3 HOURS</option>
                        <option value="4">4 HOURS</option>
                        <option value="5">5 HOURS</option>
                        <option value="24">24 HOURS (FULL DAY)</option>
                    </select>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 flex items-center gap-2">
                        <i class="fas fa-car"></i> VEHICLE NUMBER
                    </label>
                    <input type="text" 
                           name="vehicle_number" 
                           placeholder="KL-01-AB-1234"
                           required
                           class="w-full h-14 px-4 border border-gray-300 bg-white text-black font-mono focus:border-black transition-all uppercase"
                           autocomplete="off">
                </div>

                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <button type="submit" 
                            class="btn-primary flex-1 h-14 px-6 text-sm flex items-center justify-center gap-2 uppercase w-full">
                        <i class="fas fa-check-circle"></i>
                        PARK VEHICLE
                    </button>
                    
                    <a href="{{ route('owner.dashboard') }}" 
                       class="btn-secondary flex-1 h-14 px-6 text-sm flex items-center justify-center gap-2 uppercase text-center border-gray-300 hover:border-black text-black transition-colors w-full">
                        <i class="fas fa-arrow-left"></i>
                        DASHBOARD
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function fetchSlots() {
            let spaceId = document.getElementById('parking_space').value;
            let gridContainer = document.getElementById('slots_grid');
            let hiddenInput = document.getElementById('selected_slot_id');

            // 1. Show Loading State
            gridContainer.innerHTML = '<div class="col-span-full text-center text-gray-500 py-4 text-xs font-bold uppercase tracking-widest">LOADING SLOTS...</div>';
            hiddenInput.value = ''; // Reset selection

            // 2. Fetch Data
            fetch(`/owner/get-slots/${spaceId}`)
                .then(response => response.json())
                .then(data => {
                    gridContainer.innerHTML = ''; // Clear loading
                    
                    if(data.length === 0) {
                        gridContainer.innerHTML = '<div class="col-span-full text-center text-gray-500 py-4 text-xs font-bold uppercase tracking-widest">NO SLOTS CREATED YET</div>';
                        return;
                    }

                    // 3. Create Grid Boxes
                    data.forEach(slot => {
                        let box = document.createElement('div');
                        
                        // Base styling
                        box.className = 'slot-box p-3 text-center text-xs font-bold font-mono border cursor-pointer select-none';
                        box.innerText = slot.slot_number;

                        if(slot.status === 'occupied') {
                            // OCCUPIED STYLE
                            box.classList.add('bg-gray-200', 'border-gray-300', 'text-gray-400', 'cursor-not-allowed');
                            box.title = "Occupied";
                        } else {
                            // AVAILABLE STYLE
                            box.classList.add('slot-available', 'bg-white', 'border-gray-300', 'text-black');
                            
                            // Click Event
                            box.onclick = function() {
                                // Remove 'selected' style from all siblings
                                Array.from(gridContainer.children).forEach(child => {
                                    child.classList.remove('slot-selected');
                                    // Reset non-selected available slots to default look
                                    if(child.classList.contains('slot-available')) {
                                        child.classList.add('bg-white', 'text-black');
                                    }
                                });

                                // Add 'selected' style to this one
                                box.classList.add('slot-selected');
                                box.classList.remove('bg-white', 'text-black'); // Remove default to allow override

                                // Update Hidden Input
                                hiddenInput.value = slot.id;
                            };
                        }
                        
                        gridContainer.appendChild(box);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    gridContainer.innerHTML = '<div class="col-span-full text-center text-red-600 py-4 text-xs font-bold uppercase tracking-widest">ERROR LOADING SLOTS</div>';
                });
        }
        
        // Auto-load if returning to page with old input
        if(document.getElementById('parking_space').value) {
            fetchSlots();
        }
    </script>
@endsection