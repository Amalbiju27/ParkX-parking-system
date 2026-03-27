@extends('layouts.app')

@section('title', 'User Dashboard')

@section('content')
<div class="w-full min-h-screen py-5" style="background-image: url('{{ asset('images/dashboard-bg.jpg') }}'); background-size: cover; background-position: center; background-attachment: fixed;">
    <div class="container p-4 p-md-5 mx-auto max-w-7xl shadow-lg" style="background-color: rgba(255, 255, 255, 0.92); backdrop-filter: blur(15px); border: 1px solid rgba(0,0,0,0.1); border-radius: 0;">
        <!-- Header -->
        <div class="card-minimal border border-dark rounded-0 p-8 lg:p-12 mb-8 animate-slide-up bg-white">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 bg-black rounded-full flex items-center justify-center overflow-hidden p-2">
                        <img src="{{ asset('parkx-logo.svg') }}" alt="ParkX Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h1 class="text-5xl font-black text-black tracking-tighter uppercase mb-1">PARKX</h1>
                        <p class="text-lg text-gray-500 font-medium uppercase tracking-widest">USER DASHBOARD</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 pt-8 border-t border-gray-200">
                <h2 class="text-2xl font-black text-black uppercase tracking-tight font-display mb-0">WELCOME BACK, <span class="font-display font-black tracking-tighter">{{ auth()->user()->name ?? 'USER' }}</span></h2>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-8 p-6 bg-green-50 border border-green-200 flex items-center gap-4 animate-slide-up">
                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                <span class="text-green-800 font-bold uppercase tracking-wider text-sm">{{ session('success') }}</span>
            </div>
        @endif
        
        @if(session('error'))
            <div class="mb-8 p-6 bg-red-50 border border-red-200 flex items-center gap-4 animate-slide-up">
                <i class="fas fa-exclamation-circle text-red-600 text-2xl"></i>
                <span class="text-red-800 font-bold uppercase tracking-wider text-sm">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Find Parking -->
        <div class="card-minimal border border-dark rounded-0 p-8 lg:p-12 mb-8 animate-slide-up bg-white" style="animation-delay: 0.1s;">
            <div class="flex justify-between items-end mb-10">
                <div>
                    <h2 class="text-3xl font-black text-black flex items-center gap-3 uppercase tracking-tighter font-display">
                        <i class="fas fa-search-location text-black text-2xl"></i>
                        FIND PARKING
                    </h2>
                    <p class="text-gray-500 mt-2 font-bold uppercase tracking-widest text-sm">DISCOVER AND BOOK AVAILABLE SPACES NEAR YOU.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($parkingSpaces as $space)
                    <div class="card-light border border-dark rounded-0 p-8 flex flex-col hover:border-black transition-all border-t-4 border-t-black bg-white">
                        <h3 class="text-3xl font-black text-black tracking-tighter uppercase mb-2 font-display">{{ $space->name }}</h3>
                        <p class="flex items-center gap-2 text-gray-500 mb-6 font-bold uppercase tracking-widest text-xs">
                            <i class="fas fa-map-marker-alt text-black"></i>
                            {{ $space->location ?? 'DOWNTOWN DISTRICT' }}
                        </p>
                        
                        <div class="mb-6">
                            <span class="inline-block px-4 py-2 bg-gray-100 border border-gray-200 font-mono text-black font-bold text-sm tracking-widest uppercase">STARTS AT ₹50/HR</span>
                        </div>
                        
                        <div class="mb-8">
                            <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-2">AVAILABILITY</p>
                            @if($space->available_slots_count > 0)
                                <p class="text-green-700 font-black tracking-tighter text-2xl">{{ $space->available_slots_count }} <span class="text-gray-500 font-bold tracking-widest text-sm">/ {{ $space->capacity }} FREE</span></p>
                            @else
                                <p class="text-red-600 font-black tracking-tighter text-2xl uppercase">LOT IS FULL</p>
                            @endif
                        </div>
                        
                        <div class="mt-auto">
                            @if($space->available_slots_count > 0)
                                <div class="mt-4 pt-2">
                                    <a href="{{ route('user.book', $space->id) }}" class="btn btn-dark w-100 rounded-pill py-2 fw-bolder text-uppercase d-block" style="background-color: #000000 !important; color: #ffffff !important; border: none; letter-spacing: 2px;">
                                        SELECT SPACE
                                    </a>
                                </div>
                            @else
                                <button class="bg-gray-200 text-gray-500 block text-center py-4 font-bold text-sm w-full uppercase tracking-widest cursor-not-allowed">CURRENTLY FULL</button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center border-2 border-dashed border-gray-200 bg-gray-50">
                        <i class="fas fa-map-pin text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 font-bold uppercase tracking-widest text-sm">NO PARKING SPACES ARE CURRENTLY ACTIVE.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Active Bookings -->
        <div class="card-minimal border border-dark rounded-0 p-8 lg:p-12 mb-8 animate-slide-up bg-white" style="animation-delay: 0.2s;">
            <h2 class="text-3xl font-black text-black mb-8 flex items-center gap-3 uppercase tracking-tighter font-display">
                <i class="fas fa-car text-black text-2xl"></i>
                MY ACTIVE BOOKINGS
            </h2>
            
            @if(isset($activeBookings) && $activeBookings->isEmpty())
                <div class="text-center py-16 border-2 border-dashed border-gray-200 bg-gray-50">
                    <i class="fas fa-car-side text-5xl text-gray-300 mb-6 block"></i>
                    <p class="text-gray-500 font-bold uppercase tracking-widest text-sm">NO ACTIVE BOOKINGS RIGHT NOW. READY TO PARK?</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b-2 border-black">
                                <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-sm">SPACE & VEHICLE</th>
                                <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-sm">STATUS & TIME</th>
                                <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-sm">AMOUNT</th>
                                <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-sm">PAYMENT</th>
                                <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-sm text-right">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($activeBookings as $b)
                                @php
                                    $expires = \Carbon\Carbon::parse($b->expires_at);
                                    $now = \Carbon\Carbon::now();
                                    $isExpired = $expires->isPast();
                                    $diffStr = $isExpired ? 'EXPIRED' : 'EXPIRES IN ' . strtoupper($now->diffForHumans($expires, true));
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-6 px-6">
                                        <div class="font-bold text-black text-lg tracking-widest uppercase">{{ $b->space_name }}</div>
                                        <div class="text-gray-500 text-xs font-bold uppercase tracking-widest mt-1">{{ $b->vehicle_category_name ?? 'VEHICLE' }} • {{ $b->vehicle_number }} • {{ $b->duration_hours }} HR(S)</div>
                                    </td>
                                    <td class="py-6 px-6">
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold border tracking-widest {{ $b->status == 'cancelled' ? 'border-red-200 text-red-700 bg-red-50' : 'border-green-200 text-green-700 bg-green-50' }} mb-2 uppercase">
                                            {{ $b->status }}
                                        </span>
                                        @if(is_null($b->scanned_at))
                                            <div class="font-mono text-xs tracking-widest {{ $isExpired ? 'text-red-600 font-black' : 'text-gray-600 font-bold' }}">
                                                {{ $diffStr }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-6 px-6">
                                        <div class="font-mono text-xl font-black text-black">₹{{ number_format($b->amount, 0) }}</div>
                                    </td>
                                    <td class="py-6 px-6">
                                        @if($b->payment_status === 'pending' && is_null($b->scanned_at))
                                            <a href="/user/booking/{{ $b->id }}/pay" class="inline-block px-6 py-2 bg-black hover:bg-gray-800 text-white font-bold text-xs tracking-widest uppercase transition-colors">PAY NOW</a>
                                        @elseif($b->payment_status === 'paid')
                                            <span class="inline-flex items-center gap-2 text-green-700 font-bold text-xs tracking-widest uppercase">
                                                <i class="fas fa-check-circle"></i> PAID
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-6 px-6 text-right">
                                        <div class="flex justify-end gap-2 flex-wrap">
                                            @if($b->location)
                                                <a target="_blank" href="https://www.google.com/maps/search/?api=1&query={{ urlencode($b->location) }}" class="px-4 py-2 border border-black text-black hover:bg-black hover:text-white font-bold text-xs uppercase tracking-widest transition-all">MAP</a>
                                            @endif
                                            <a href="/user/booking/{{ $b->id }}/ticket" class="px-4 py-2 border border-gray-300 text-black hover:bg-gray-100 font-bold text-xs uppercase tracking-widest transition-all">QR</a>
                                            @if(!$b->scanned_at)
                                                <form action="{{ route('user.booking.grace-period', $b->id) }}" method="POST" class="inline-block m-0">
                                                    @csrf
                                                    <button type="submit" class="px-4 py-2 border border-orange-300 text-orange-600 bg-orange-50 hover:bg-orange-600 hover:text-white font-bold text-xs uppercase tracking-widest transition-all cursor-pointer">RUNNING LATE? (+10 MINS)</button>
                                                </form>
                                            @else
                                                <form action="{{ route('user.booking.extend-duration', $b->id) }}" method="POST" class="inline-flex items-center m-0 gap-2">
                                                    @csrf
                                                    <select name="extra_hours" class="h-full px-2 py-2 border border-gray-300 text-xs font-bold font-mono outline-none">
                                                        <option value="1">+1 HR</option>
                                                        <option value="2">+2 HRS</option>
                                                        <option value="3">+3 HRS</option>
                                                    </select>
                                                    <button type="submit" class="px-4 py-2 border border-blue-300 text-blue-600 bg-blue-50 hover:bg-blue-600 hover:text-white font-bold text-xs uppercase tracking-widest transition-all cursor-pointer">EXTEND</button>
                                                </form>
                                            @endif
                                            @if(is_null($b->scanned_at))
                                                <form action="{{ route('user.booking.cancel', $b->id) }}" method="POST" class="inline-block m-0">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="px-4 py-2 border border-red-200 text-red-600 bg-red-50 hover:bg-red-600 hover:text-white font-bold text-xs uppercase tracking-widest transition-all cursor-pointer">CANCEL</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Booking History -->
        <div class="card-minimal border border-dark rounded-0 p-8 lg:p-12 animate-slide-up bg-white" style="animation-delay: 0.3s;">
            <h2 class="text-3xl font-black text-black mb-8 flex items-center gap-3 uppercase tracking-tighter font-display">
                <i class="fas fa-history text-black text-2xl"></i>
                RECENT BOOKINGS
            </h2>
            
            @if(isset($pastBookings) && $pastBookings->isEmpty())
                <div class="text-center py-16 border-2 border-dashed border-gray-200 bg-gray-50">
                    <i class="fas fa-calendar-times text-5xl text-gray-300 mb-6 block"></i>
                    <p class="text-gray-500 font-bold uppercase tracking-widest text-sm">LOOKS LIKE YOU HAVEN'T PARKED WITH US YET.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b-2 border-black">
                                <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-sm">SPACE</th>
                                <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-sm">DATE</th>
                                <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-sm text-center">DURATION</th>
                                <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-sm">PAID</th>
                                <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-sm text-right">STATUS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($pastBookings as $b)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-black uppercase text-lg">{{ $b->space_name }}</div>
                                        <div class="text-gray-400 text-xs font-bold uppercase tracking-widest">{{ $b->vehicle_number }}</div>
                                    </td>
                                    <td class="py-4 px-6 text-gray-500 text-xs font-bold uppercase tracking-widest">{{ \Carbon\Carbon::parse($b->created_at)->format('M d, Y H:i') }}</td>
                                    <td class="py-4 px-6 text-center text-black font-mono font-bold tracking-widest">{{ $b->duration_hours }} HR(S)</td>
                                    <td class="py-4 px-6 font-mono font-black text-black text-lg">₹{{ number_format($b->amount, 0) }}</td>
                                    <td class="py-4 px-6 text-right">
                                        @if($b->status === 'cancelled')
                                            <span class="inline-block px-3 py-1 text-xs font-bold border border-red-200 text-red-700 bg-red-50 tracking-widest uppercase">CANCELLED</span>
                                        @else
                                            <span class="inline-block px-3 py-1 text-xs font-bold border border-gray-300 text-gray-600 bg-gray-100 tracking-widest uppercase">COMPLETED</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection