@extends('layouts.app')

@section('title', 'Parking Ticket & QR')

@section('content')

<div class="max-w-md mx-auto relative z-20 px-4 py-16">
    <div class="card-minimal border-black border-2 animate-slide-up relative bg-white overflow-hidden">
        <!-- Top accent line -->
        <div class="h-4 w-full bg-black"></div>
        
        <div class="p-8">
            <div class="text-center mb-8 flex flex-col items-center">
                <div class="w-16 h-16 bg-black rounded-full flex items-center justify-center overflow-hidden p-2 mb-4">
                    <img src="{{ asset('parkx-logo.svg') }}" alt="ParkX Logo" class="w-full h-full object-contain">
                </div>
                <h4 class="text-2xl font-black text-black tracking-tighter uppercase whitespace-nowrap">PARKX TICKET</h4>
            </div>
            
            <div class="text-center mb-8 border-b-2 border-black pb-6">
                <h5 class="font-black text-xl text-black uppercase">{{ $booking->space_name }}</h5>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mt-1">{{ $booking->location ?? 'DOWNTOWN DISTRICT' }}</p>
            </div>
            
            <div class="flex justify-center mb-8 p-4 bg-gray-50 border border-t-0 border-x-0 border-b-2 border-black border-dashed pb-8">
                <!-- simple-qrcode blade syntax -->
                {!! QrCode::size(220)->color(0, 0, 0)->margin(0)->generate(route('ticket.show', $booking->id)) !!}
            </div>

            <div class="bg-gray-50 p-6 border border-gray-200 mb-8">
                <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-200">
                    <span class="text-gray-500 text-xs uppercase tracking-widest font-bold">BOOKING REF</span>
                    <span class="font-black font-mono text-black text-lg">#{{ $booking->id }}</span>
                </div>
                <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-200">
                    <span class="text-gray-500 text-xs uppercase tracking-widest font-bold">DURATION</span>
                    <span class="font-black text-black">{{ $booking->duration_hours }} HR(S)</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 text-xs uppercase tracking-widest font-bold">EXPIRES AT</span>
                    <span class="font-black text-black">{{ \Carbon\Carbon::parse($booking->expires_at)->format('h:i A') }}</span>
                </div>
            </div>

            <a href="/user" class="btn-primary w-full h-14 text-sm flex items-center justify-center gap-2 uppercase">
                BACK TO DASHBOARD
            </a>
        </div>
        
        <!-- Ticket cutouts -->
        <div class="absolute top-[320px] -left-4 w-8 h-8 bg-[#f5f5f5] rounded-full border border-black border-l-0"></div>
        <div class="absolute top-[320px] -right-4 w-8 h-8 bg-[#f5f5f5] rounded-full border border-black border-r-0"></div>
        
    </div>
</div>
@endsection
