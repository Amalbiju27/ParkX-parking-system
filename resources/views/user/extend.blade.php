@extends('layouts.app')

@section('title', 'Extend Booking')

@section('content')
<div class="max-w-lg mx-auto py-16">
    <div class="card-minimal p-8 animate-slide-up">
        <div class="text-center mb-8 border-b-2 border-black pb-4">
            <h2 class="text-3xl font-black text-black mb-2 uppercase tracking-tighter">EXTEND BOOKING</h2>
            <p class="text-xs font-bold uppercase tracking-widest text-gray-500">{{ $booking->space_name }}</p>
        </div>
        
        <div class="mb-8 p-6 bg-gray-50 border border-gray-200 flex flex-col items-center justify-center text-center">
            <span class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">CURRENT EXPIRY</span>
            <span class="font-black text-2xl uppercase border-b-2 border-black pb-1">{{ \Carbon\Carbon::parse($booking->expires_at)->format('M d, Y h:i A') }}</span>
        </div>

        <form action="{{ route('user.booking.extend.process', $booking->id) }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">ADD ADDITIONAL TIME</label>
                <select name="extra_hours" class="w-full h-14 px-4 border border-gray-300 bg-white text-black font-mono focus:border-black transition-all appearance-none rounded-none" required>
                    <option value="1">1 HOUR</option>
                    <option value="2">2 HOURS</option>
                    <option value="3">3 HOURS</option>
                    <option value="4">4 HOURS</option>
                    <option value="5">5 HOURS</option>
                </select>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4 pt-4">
                <button type="submit" class="btn-primary flex-1 h-14 px-6 text-sm flex items-center justify-center gap-2 uppercase w-full">
                    CONFIRM
                </button>
                <a href="/user" class="btn-secondary flex-1 h-14 px-6 text-sm flex items-center justify-center gap-2 uppercase text-center border-gray-300 hover:border-black text-black transition-colors w-full">
                    GO BACK
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
