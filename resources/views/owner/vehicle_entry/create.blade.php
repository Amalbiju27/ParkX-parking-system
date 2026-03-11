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

                <div class="space-y-4">
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 flex items-center gap-2">
                        <i class="fas fa-th"></i> SELECT SLOT
                    </label>
                    
                    <input type="hidden" name="slot_id" id="selected_slot_id" required>

                    <div class="border border-slate-200 rounded-xl p-6 bg-[#f8fafc] w-full relative">
                        <!-- Entrance Gate -->
                        <div class="bg-[#111] text-white py-3 rounded text-center tracking-[0.3em] font-bold text-sm mb-8">ENTRANCE GATE</div>

                        <!-- Driveway Flex Container -->
                        <div class="flex justify-between items-stretch relative min-h-[150px]" id="slots_grid">
                            
                            <!-- Central Driveway Divider -->
                            <div class="absolute left-1/2 top-0 bottom-0 -translate-x-1/2 w-12 border-x-2 border-dashed border-gray-300 flex items-center justify-center">
                                <span class="rotate-90 tracking-[0.3em] text-gray-400 font-bold text-xs uppercase text-nowrap">Driveway</span>
                            </div>

                            <!-- Left Sector -->
                            <div class="w-[40%]">
                                <div class="text-xs font-bold text-slate-500 uppercase mb-3 text-center">SECTOR A</div>
                                <div id="sector-left" class="grid grid-cols-3 gap-3"></div>
                            </div>

                            <!-- Right Sector -->
                            <div class="w-[40%]">
                                <div class="text-xs font-bold text-slate-500 uppercase mb-3 text-center">SECTOR B</div>
                                <div id="sector-right" class="grid grid-cols-3 gap-3"></div>
                            </div>
                            
                            <!-- Loading State Overlay (Hidden by Default) -->
                            <div id="slots-loading" class="absolute inset-0 bg-[#f8fafc] flex items-center justify-center z-10">
                                <span class="text-gray-500 py-4 text-xs font-bold uppercase tracking-widest">SELECT A PARKING SPACE...</span>
                            </div>

                        </div>

                        <!-- Exit Gate -->
                        <div class="bg-[#111] text-white py-3 rounded text-center tracking-[0.3em] font-bold text-sm mt-8">EXIT GATE</div>
                    </div>
                    
                    <div class="flex gap-4 text-xs font-bold tracking-widest text-gray-500 justify-end pt-2 uppercase">
                        <div class="flex items-center gap-2"><div class="w-3 h-3 bg-white border border-slate-300 rounded-sm"></div> AVAILABLE</div>
                        <div class="flex items-center gap-2"><div class="w-3 h-3 bg-[#111] border border-[#111] rounded-sm"></div> OCCUPIED</div>
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
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-500 uppercase tracking-wide mb-2 flex items-center gap-2">
                            <i class="fas fa-clock"></i> START TIME
                        </label>
                        <input type="time" name="display_start_time" id="displayStartTime" required 
                               class="bg-gray-50 border border-gray-200 text-gray-700 rounded p-3 w-full outline-none focus:ring-2 focus:ring-blue-500 uppercase tracking-widest font-mono text-sm">
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-500 uppercase tracking-wide mb-2 flex items-center gap-2">
                            <i class="fas fa-hourglass-start"></i> DURATION
                        </label>
                        <select id="durationSelect" class="bg-gray-50 border border-gray-200 text-gray-800 rounded p-3 w-full outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm uppercase tracking-widest h-[46px]">
                            <option value="1">1 Hour</option>
                            <option value="2">2 Hours</option>
                            <option value="3">3 Hours</option>
                            <option value="4">4 Hours</option>
                            <option value="5">5 Hours</option>
                            <option value="6">6 Hours</option>
                            <option value="7">7 Hours</option>
                            <option value="8">8 Hours</option>
                            <option value="9">9 Hours</option>
                            <option value="10">10 Hours</option>
                            <option value="11">11 Hours</option>
                            <option value="12">12 Hours</option>
                            <option value="24">24 Hours (Full Day)</option>
                        </select>
                    </div>
                </div>
                
                <div class="space-y-2 mb-6">
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

                <div class="space-y-2 mb-6">
                    <label class="flex items-center text-sm font-bold text-gray-500 uppercase tracking-wide mb-2">
                        <i class="fas fa-wallet mr-2"></i> AMOUNT TO BE PAID
                    </label>
                    
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex justify-between items-center">
                        <span class="text-green-700 font-bold uppercase text-xs tracking-wider">Total Fee</span>
                        <span class="text-3xl font-extrabold text-green-700" id="totalAmountDisplay">₹ 0.00</span>
                    </div>
                    
                    <input type="hidden" name="total_amount" id="totalAmountInput" value="0">
                    <input type="hidden" name="start_time" id="realStartTime">
                    <input type="hidden" name="end_time" id="realEndTime">
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
            let loadingOverlay = document.getElementById('slots-loading');
            let leftSector = document.getElementById('sector-left');
            let rightSector = document.getElementById('sector-right');
            let hiddenInput = document.getElementById('selected_slot_id');

            // 1. Show Loading State
            loadingOverlay.style.display = 'flex';
            loadingOverlay.innerHTML = '<span class="text-gray-500 py-4 text-xs font-bold uppercase tracking-widest">LOADING SLOTS...</span>';
            hiddenInput.value = ''; // Reset selection
            leftSector.innerHTML = '';
            rightSector.innerHTML = '';

            // 2. Fetch Data
            fetch(`/owner/get-slots/${spaceId}`)
                .then(response => response.json())
                .then(data => {
                    loadingOverlay.style.display = 'none'; // Clear loading
                    
                    if(data.length === 0) {
                        loadingOverlay.style.display = 'flex';
                        loadingOverlay.innerHTML = '<span class="text-red-500 py-4 text-xs font-bold uppercase tracking-widest">NO SLOTS CREATED YET</span>';
                        return;
                    }

                    // 3. Create Grid Boxes & Split them
                    let totalSlots = data.length;
                    let midPoint = Math.ceil(totalSlots / 2);

                    data.forEach((slot, index) => {
                        let box = document.createElement('div');
                        
                        box.innerText = slot.slot_number.replace('SLOT ', '');

                        if(slot.status === 'occupied') {
                            // OCCUPIED STYLE
                            box.className = 'bg-[#111] border border-[#111] text-white font-bold py-2 rounded text-center cursor-not-allowed uppercase tracking-widest text-xs';
                            box.title = "Occupied";
                        } else {
                            // AVAILABLE STYLE
                            box.className = 'slot-available bg-white border border-slate-300 text-slate-700 font-bold py-2 rounded text-center shadow-sm cursor-pointer hover:border-slate-500 uppercase tracking-widest text-xs transition-all';
                            
                            // Click Event
                            box.onclick = function() {
                                // Remove 'selected' style from all siblings
                                document.querySelectorAll('.slot-available').forEach(child => {
                                    child.classList.remove('slot-selected');
                                    // Reset non-selected available slots to default look
                                    if(child.classList.contains('slot-available')) {
                                        child.className = 'slot-available bg-white border border-slate-300 text-slate-700 font-bold py-2 rounded text-center shadow-sm cursor-pointer hover:border-slate-500 uppercase tracking-widest text-xs transition-all';
                                    }
                                });

                                // Add 'selected' style to this one
                                box.className = 'slot-available slot-selected bg-black border border-black text-white font-bold py-2 rounded text-center shadow-sm cursor-pointer uppercase tracking-widest text-xs transition-all';

                                // Update Hidden Input
                                hiddenInput.value = slot.id;
                            };
                        }
                        
                        // Append to Left or Right Sector
                        if (index < midPoint) {
                            leftSector.appendChild(box);
                        } else {
                            rightSector.appendChild(box);
                        }
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    loadingOverlay.style.display = 'flex';
                    loadingOverlay.innerHTML = '<span class="text-red-600 py-4 text-xs font-bold uppercase tracking-widest">ERROR LOADING SLOTS</span>';
                });
        }
        
        // Auto-load if returning to page with old input
        if(document.getElementById('parking_space').value) {
            fetchSlots();
        }

        document.addEventListener("DOMContentLoaded", function() {
            // 1. Configuration
            const HOURLY_RATE = 50; 
            
            // 2. Grab elements
            const displayStartTime = document.getElementById('displayStartTime');
            const durationSelect = document.getElementById('durationSelect');
            
            const realStartTime = document.getElementById('realStartTime');
            const realEndTime = document.getElementById('realEndTime');
            
            const displayAmount = document.getElementById('totalAmountDisplay');
            const hiddenAmountInput = document.getElementById('totalAmountInput');

            // 3. Set the time input to the CURRENT time by default
            const now = new Date();
            const currentHours = String(now.getHours()).padStart(2, '0');
            const currentMinutes = String(now.getMinutes()).padStart(2, '0');
            displayStartTime.value = `${currentHours}:${currentMinutes}`;

            // 4. Calculation Logic
            function calculateBooking() {
                if (!displayStartTime.value || !durationSelect.value) return;

                // Create a Date object for TODAY with the selected Start Time
                const [hours, minutes] = displayStartTime.value.split(':');
                const startDate = new Date();
                startDate.setHours(parseInt(hours), parseInt(minutes), 0, 0);

                // Calculate End Date based on duration
                const durationHours = parseInt(durationSelect.value);
                const endDate = new Date(startDate.getTime() + (durationHours * 60 * 60 * 1000));

                // Format dates perfectly for Laravel's database (YYYY-MM-DD HH:MM:SS)
                const formatForDB = (date) => {
                    const tzOffset = date.getTimezoneOffset() * 60000; // offset in milliseconds
                    const localISOTime = (new Date(date - tzOffset)).toISOString().slice(0, 19).replace('T', ' ');
                    return localISOTime;
                };

                // Update Hidden Inputs for the Backend
                realStartTime.value = formatForDB(startDate);
                realEndTime.value = formatForDB(endDate);

                // Update the Price Display
                const totalAmount = durationHours * HOURLY_RATE;
                displayAmount.innerText = `₹ ${totalAmount.toFixed(2)}`;
                hiddenAmountInput.value = totalAmount;
            }

            // 5. Listen for changes AND run once on load
            displayStartTime.addEventListener('input', calculateBooking);
            durationSelect.addEventListener('change', calculateBooking);
            
            // Run initially to set the price for "1 Hour" from the current time
            calculateBooking();
        });
    </script>
@endsection