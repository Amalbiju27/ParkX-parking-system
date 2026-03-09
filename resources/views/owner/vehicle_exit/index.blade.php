@extends('layouts.app')

@section('title', 'Vehicle Exit')

@section('content')
    <div class="relative z-20 max-w-4xl mx-auto px-4 py-16">
        
        <div class="card-minimal p-6 lg:p-8 mb-8 animate-slide-up">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-black rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-sign-out-alt text-lg"></i>
                </div>
                <div>
                    <h1 class="text-3xl lg:text-4xl font-black text-black tracking-tighter uppercase">VEHICLE EXIT</h1>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mt-1">PROCESS VEHICLE CHECKOUT</p>
                </div>
            </div>
        </div>

        <div class="card-minimal p-8 lg:p-12 animate-slide-up" style="animation-delay: 0.1s;">
            
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

            <div class="mb-8">
                <h3 class="text-xl font-black text-black mb-6 uppercase tracking-tighter border-b-2 border-black pb-2">
                    PARKED VEHICLES
                </h3>
                
                @forelse($vehicles as $v)
                    <div class="card-minimal p-6 mb-4 hover:shadow-lg transition-shadow">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                            <div class="flex items-center gap-4 flex-1 min-w-0">
                                <div class="w-12 h-12 bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-car text-black"></i>
                                </div>
                                <div>
                                    <div class="font-black text-2xl text-black uppercase">{{ $v->vehicle_number }}</div>
                                    <div class="text-gray-500 text-xs font-bold uppercase tracking-widest mt-1">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ \Carbon\Carbon::parse($v->entry_time)->format('M d, Y • h:i A') }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <form method="POST" action="{{ route('owner.vehicle.exit') }}" class="inline-block m-0">
                                    @csrf
                                    <input type="hidden" name="vehicle_number" value="{{ $v->vehicle_number }}">
                                    
                                    <button type="submit" 
                                            class="bg-black text-white px-8 h-12 text-sm font-bold flex items-center gap-2 uppercase tracking-widest hover:bg-gray-800 transition-colors">
                                        <i class="fas fa-sign-out-alt"></i>
                                        EXIT VEHICLE
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16">
                        <i class="fas fa-parking text-5xl text-gray-300 mb-6"></i>
                        <h3 class="text-xl font-black text-gray-500 mb-2 uppercase tracking-widest">NO PARKED VEHICLES</h3>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">ALL PARKING SLOTS ARE CURRENTLY EMPTY</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="text-center mt-12 animate-slide-up" style="animation-delay: 0.2s;">
            <a href="{{ route('owner.dashboard') }}" 
               class="btn-secondary inline-flex items-center gap-3 px-12 h-14 text-sm uppercase tracking-widest border-gray-300 hover:border-black transition-colors w-full sm:w-auto justify-center">
                <i class="fas fa-arrow-left"></i>
                BACK TO DASHBOARD
            </a>
        </div>
    </div>
@endsection