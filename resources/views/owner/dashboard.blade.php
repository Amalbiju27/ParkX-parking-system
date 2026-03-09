@extends('layouts.app')

@section('title', 'Owner Dashboard')

@section('content')
        <div class="card-minimal p-8 lg:p-12 mb-8 animate-slide-up">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 bg-black rounded-full flex items-center justify-center overflow-hidden p-2">
                        <img src="{{ asset('parkx-logo.svg') }}" alt="ParkX Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h1 class="text-5xl font-black text-black tracking-tighter uppercase mb-1">PARKX</h1>
                        <p class="text-lg text-gray-500 font-medium uppercase tracking-widest">OWNER DASHBOARD</p>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ url('/owner/vehicle-entry') }}" 
                       class="btn-primary px-10 py-4 text-sm flex items-center justify-center gap-3">
                        <i class="fas fa-plus"></i> ENTRY
                    </a>
                    <a href="{{ url('/owner/vehicle-exit') }}" 
                       class="btn-secondary px-10 py-4 text-sm flex items-center justify-center gap-3">
                        <i class="fas fa-sign-out-alt"></i> EXIT
                    </a>
                </div>
            </div>
            
            <div class="mt-8 pt-8 border-t border-gray-200">
                <p class="text-2xl font-bold text-black uppercase tracking-tight">WELCOME BACK, <span class="font-display font-black tracking-tighter">{{ auth()->user()->name }}</span></p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 animate-slide-up" style="animation-delay: 0.1s;">
            <div class="card-minimal p-8 transition-colors hover:border-black hover:bg-gray-50">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-black text-white rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-line text-lg"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 uppercase tracking-widest font-bold text-xs mb-1">TODAY</p>
                        <p class="text-black font-black uppercase tracking-tight text-xl">REVENUE</p>
                    </div>
                </div>
                <div class="text-5xl font-black text-black tracking-tighter">₹{{ number_format($todayRevenue, 0) }}</div>
            </div>
            
            <div class="card-minimal p-8 transition-colors hover:border-black hover:bg-gray-50" style="animation-delay: 0.2s;">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-white border-2 border-black text-black rounded-full flex items-center justify-center">
                        <i class="fas fa-wallet text-lg"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 uppercase tracking-widest font-bold text-xs mb-1">TOTAL</p>
                        <p class="text-black font-black uppercase tracking-tight text-xl">REVENUE</p>
                    </div>
                </div>
                <div class="text-5xl font-black text-black tracking-tighter">₹{{ number_format($totalRevenue, 0) }}</div>
            </div>
        </div>

        <div class="card-minimal p-8 lg:p-12 mb-8 animate-slide-up" style="animation-delay: 0.2s;">
            <h2 class="text-3xl font-black text-black mb-8 flex items-center gap-3 uppercase tracking-tighter">
                <i class="fas fa-car text-black text-2xl"></i>
                LIVE SLOT AVAILABILITY
            </h2>
            
            <div class="space-y-6">
                @foreach($parkingSpaces as $space)
                <div class="card-light p-8 border-l-4 border-l-black hover:shadow-sm transition-all">
                    <div class="flex flex-col lg:flex-row lg:items-center gap-6 mb-6 pb-6 border-b border-gray-200">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white border border-gray-300 rounded-full flex items-center justify-center text-black">
                                <i class="fas fa-parking text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-3xl font-black text-black tracking-tighter uppercase">{{ $space->name }}</h3>
                                <p class="text-gray-500 font-medium uppercase tracking-widest text-sm">{{ $space->location }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div class="p-4 bg-white border border-gray-200">
                                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-1">CAPACITY</p>
                                <p class="text-2xl font-black text-black tracking-tighter">{{ $space->capacity }}</p>
                            </div>
                            <div class="p-4 status-available">
                                <p class="text-green-700 text-xs font-bold uppercase tracking-widest mb-1">AVAILABLE</p>
                                <p class="text-2xl font-black tracking-tighter">{{ $space->available_slots_count }}</p>
                            </div>
                            <div class="p-4 status-occupied">
                                <p class="text-red-700 text-xs font-bold uppercase tracking-widest mb-1">OCCUPIED</p>
                                <p class="text-2xl font-black tracking-tighter">{{ $space->occupied_slots }}</p>
                            </div>
                        </div>
                    </div>
                    
                    @if(!empty($slots[$space->id] ?? []))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="font-bold text-green-600 mb-4 flex items-center gap-2 text-xs uppercase tracking-widest">
                                <i class="fas fa-check-circle"></i> AVAILABLE SLOTS
                            </p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($slots[$space->id] ?? [] as $slot)
                                    @if($slot->status === 'available')
                                    <span class="px-3 py-2 bg-white border border-gray-300 text-black text-sm font-bold font-mono tracking-widest hover:border-black transition-all cursor-default">{{ $slot->slot_number }}</span>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <p class="font-bold text-red-600 mb-4 flex items-center gap-2 text-xs uppercase tracking-widest">
                                <i class="fas fa-times-circle"></i> OCCUPIED SLOTS
                            </p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($slots[$space->id] ?? [] as $slot)
                                    @if($slot->status === 'occupied')
                                    
<div class="d-flex flex-wrap gap-3">
    @php
        // 1. Identify the Active Record (Check Booking first, then Manual Entry)
        $activeRecord = null;
        $recordType = null;
        
        // Hunt for an active user booking using DB Facade for safety
        $activeRecord = \Illuminate\Support\Facades\DB::table('bookings')
            ->where('slot_id', $slot->id)
            ->whereIn('status', ['active', 'booked', 'parked', 'occupied'])
            ->latest()
            ->first();
            
        if ($activeRecord) {
            $recordType = 'booking';
        }
        
        // If no user booking, hunt for a manual owner entry
        if (!$activeRecord) {
            $activeRecord = \Illuminate\Support\Facades\DB::table('vehicles')->where('slot_id', $slot->id)
                ->where('status', 'parked') 
                ->latest()->first();
            if ($activeRecord) $recordType = 'manual';
        }
        
        // 2. Extract Data Safely
        $vehicleNo = $activeRecord ? ($activeRecord->vehicle_number ?? 'UNKNOWN') : 'UNKNOWN';
        $videoPath = $activeRecord ? ($activeRecord->vehicle_video ?? null) : null;
        
        if ($recordType === 'manual' && isset($activeRecord->entry_time)) {
             $entryTime = \Carbon\Carbon::parse($activeRecord->entry_time)->format('h:i A');
        } else {
             $entryTime = $activeRecord ? \Carbon\Carbon::parse($activeRecord->created_at)->format('h:i A') : '-- : --';
        }
        
        $recordId = $activeRecord ? $activeRecord->id : null;
    @endphp
    
    <div class="card bg-black text-white rounded-none border-0 shadow-sm mt-3" style="width: 250px; border-left: 4px solid #ef4444 !important;">
        <div class="card-header border-b border-gray-800 py-3 px-4 flex justify-between items-center">
            <span class="font-bold font-mono tracking-widest text-lg">
                {{ str_replace('SLOT ', '', strtoupper($slot->slot_number ?? 'S'.$slot->id)) }}
            </span>
            @if($recordType === 'booking')
                <span class="px-2 py-1 bg-blue-600 text-white text-[10px] font-bold tracking-widest uppercase rounded-sm">ONLINE APP</span>
            @else
                <span class="px-2 py-1 bg-gray-600 text-white text-[10px] font-bold tracking-widest uppercase rounded-sm">GATE ENTRY</span>
            @endif
        </div>
        <div class="card-body py-4 px-4 bg-gray-900 border-x border-gray-800">
            <div class="mb-4">
                <p class="mb-2 text-xs text-gray-400 font-bold tracking-widest flex items-center gap-2"><i class="fas fa-car text-gray-500"></i> VEHICLE DETAILS</p>
                <div class="flex justify-between items-center mb-1">
                    <span class="text-[10px] text-gray-500 uppercase tracking-widest">REG NO:</span>
                    <span class="font-bold text-sm font-mono tracking-wider">{{ $vehicleNo }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] text-gray-500 uppercase tracking-widest">ENTRY:</span>
                    <span class="font-bold text-green-500 text-sm font-mono">{{ $entryTime }}</span>
                </div>
            </div>
            
            @if($videoPath)
                <div class="mt-2 text-center" style="margin-bottom: 8px;">
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($videoPath) }}" 
                       target="_blank" 
                       class="btn btn-sm btn-outline-light w-100" 
                       style="font-size: 0.65rem; border: 1px solid rgba(255,255,255,0.3); color: white; display: inline-block; padding: 6px; letter-spacing: 1px;">
                        <i class="fas fa-video me-1"></i> VIEW CAR VIDEO
                    </a>
                </div>
            @endif
            
            @if($recordId)
                <form action="{{ route('owner.vehicle.exit.process', ['id' => $recordId, 'type' => $recordType]) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white transition-colors duration-200 py-2 text-xs font-bold tracking-widest uppercase">
                        CHECKOUT / EXIT
                    </button>
                </form>
            @else
                <button class="w-full bg-gray-800 text-gray-500 cursor-not-allowed py-2 text-xs font-bold tracking-widest uppercase" disabled>
                    DATA ERROR
                </button>
            @endif
        </div>
        <div class="h-1 w-full bg-gray-800"></div>
    </div>
</div>

                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <div class="card-minimal p-8 lg:p-12 mb-8 animate-slide-up" style="animation-delay: 0.3s;">
            <h2 class="text-3xl font-black text-black mb-8 flex items-center gap-3 uppercase tracking-tighter">
                <i class="fas fa-table-cells text-black text-2xl"></i>
                PARKING SUMMARY
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b-2 border-black">
                            <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light">SPACE</th>
                            <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-center">CAPACITY</th>
                            <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-center">AVAILABLE</th>
                            <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-center">OCCUPIED</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($parkingSpaces as $space)
                        <tr class="hover:bg-gray-50 transition-colors h-16">
                            <td class="py-4 px-6 font-bold text-black text-lg">{{ $space->name }}</td>
                            <td class="py-4 px-6 text-center text-gray-800 font-bold text-lg">{{ $space->capacity }}</td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex px-4 py-1 border border-green-200 text-green-700 bg-green-50 rounded-full font-bold text-sm tracking-widest">
                                    {{ $space->available_slots_count }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex px-4 py-1 border border-red-200 text-red-700 bg-red-50 rounded-full font-bold text-sm tracking-widest">
                                    {{ $space->occupied_slots }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-minimal p-8 lg:p-12 mb-8 animate-slide-up" style="animation-delay: 0.35s;">
            <h2 class="text-3xl font-black text-black mb-8 flex items-center gap-3 uppercase tracking-tighter">
                <i class="fas fa-calendar-check text-black text-2xl"></i>
                CURRENT & UPCOMING BOOKINGS
            </h2>
            
            @if(isset($bookings) && $bookings->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b-2 border-black">
                            <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light">BOOKER NAME</th>
                            <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light">VEHICLE / SLOT</th>
                            <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light">START & EXPIRE TIME</th>
                            <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-center">STATUS</th>
                            <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-right">PENALTY</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($bookings as $b)
                            @php
                                $penalty = 0;
                                $now = \Carbon\Carbon::now();
                                $expiresAt = \Carbon\Carbon::parse($b->expires_at);
                                
                                // Only calculate penalty if overstaying and not yet completed checkout
                                if ($now->greaterThan($expiresAt) && in_array($b->status, ['booked', 'reserved', 'occupied'])) {
                                    $hoursOver = $expiresAt->diffInHours($now) + 1; // Round up for partial hour
                                    $penalty = $hoursOver * 50; // ₹50 per hour
                                }
                            @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-6 font-bold text-black uppercase">{{ $b->booker_name }}</td>
                            <td class="py-4 px-6 text-black">
                                <div class="font-bold tracking-widest text-sm uppercase">{{ $b->vehicle_category }}</div>
                                <div class="text-xs font-bold text-gray-500 mt-1 uppercase">{{ $b->space_name }} • {{ $b->slot_name ?? 'PENDING' }}</div>
                            </td>
                            <td class="py-4 px-6 text-black">
                                <div class="font-bold text-sm tracking-wider uppercase">{{ \Carbon\Carbon::parse($b->created_at)->format('M d, H:i') }}</div>
                                <div class="text-xs font-bold mt-1 tracking-wider uppercase {{ $now->greaterThan($expiresAt) ? 'text-red-600' : 'text-gray-500' }}">
                                    EXP: {{ $expiresAt->format('M d, H:i') }}
                                </div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="uppercase text-xs font-bold px-3 py-1 border tracking-widest
                                    {{ $b->status === 'occupied' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-green-50 text-green-700 border-green-200' }}">
                                    {{ $b->status }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right font-mono font-black text-lg {{ $penalty > 0 ? 'text-red-600' : 'text-black' }}">
                                ₹{{ number_format($penalty, 0) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-12 border-2 border-dashed border-gray-200 bg-gray-50">
                <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500 text-sm font-bold uppercase tracking-widest">NO ACTIVE OR UPCOMING BOOKINGS</p>
            </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8 animate-slide-up" style="animation-delay: 0.4s;">
            <div class="card-minimal p-8">
                <h3 class="text-2xl font-black text-black mb-8 flex items-center gap-3 uppercase tracking-tighter">
                    <i class="fas fa-search text-black text-xl"></i>
                    SEARCH VEHICLES
                </h3>
                <form method="GET" action="{{ url('/owner') }}" class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">VEHICLE NUMBER</label>
                        <input type="text" name="vehicle_number" 
                               placeholder="KL-01-AB-1234" value="{{ request('vehicle_number') }}"
                               class="w-full h-14 px-4 border border-gray-300 bg-white text-black placeholder-gray-400 font-mono focus:border-black transition-all">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">FROM</label>
                            <input type="date" name="from_date" value="{{ request('from_date') }}"
                                   class="w-full h-14 px-4 border border-gray-300 bg-white text-black font-mono focus:border-black transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">TO</label>
                            <input type="date" name="to_date" value="{{ request('to_date') }}"
                                   class="w-full h-14 px-4 border border-gray-300 bg-white text-black font-mono focus:border-black transition-all">
                        </div>
                    </div>
                    <button type="submit" class="w-full btn-primary py-4 text-sm font-bold tracking-widest mt-2">
                        SEARCH RECORDS
                    </button>
                </form>
            </div>

            <div class="card-minimal p-8 bg-gray-50">
                <h3 class="text-2xl font-black text-black mb-8 flex items-center gap-3 uppercase tracking-tighter">
                    <i class="fas fa-history text-black text-xl"></i>
                    RECENT HISTORY
                </h3>
                @if($vehicleHistory->isEmpty())
                    <div class="text-center py-12">
                        <i class="fas fa-car text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 font-bold uppercase tracking-widest text-sm">NO RECORDS FOUND</p>
                    </div>
                @else
                    <div class="space-y-3 max-h-80 overflow-y-auto pr-2">
                        @foreach($vehicleHistory->take(5) as $v)
                        <div class="bg-white border border-gray-200 p-4 hover:border-black transition-colors flex items-center gap-4">
                            <div class="w-12 h-12 bg-black rounded-full flex items-center justify-center flex-shrink-0 text-white">
                                <i class="fas fa-car text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-mono font-black text-black tracking-widest text-lg truncate">{{ $v->vehicle_number }}</div>
                                <div class="text-gray-500 text-xs font-bold uppercase tracking-wider mt-1">{{ $v->parking_name }} • {{ $v->slot_number ?? '-' }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-mono font-black text-black text-xl">₹{{ $v->charge ?? 0 }}</div>
                                <div class="text-xs font-bold tracking-widest uppercase mt-1 {{ $v->status === 'completed' ? 'text-green-600' : 'text-gray-500' }}">{{ ucfirst($v->status) }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

@endsection