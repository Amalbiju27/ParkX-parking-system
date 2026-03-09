@extends('layouts.app')

@section('title', 'Complete Payment')

@section('content')
<div class="max-w-lg mx-auto py-16">
    <div class="card-minimal p-8 animate-slide-up">
        <div class="text-center mb-8 border-b-2 border-black pb-4">
            <h2 class="text-3xl font-black text-black mb-2 uppercase tracking-tighter">SECURE CHECKOUT</h2>
            <p class="text-xs font-bold uppercase tracking-widest text-gray-500">BOOKING REF: #{{ $booking->id }} • DURATION: {{ $booking->duration_hours }} HOURS</p>
        </div>
        
        <div class="mb-8 p-6 bg-gray-50 border border-gray-200 flex flex-col items-center justify-center text-center">
            <span class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">AMOUNT DUE</span>
            <span class="font-black text-4xl text-black font-mono">₹{{ number_format($booking->amount, 2) }}</span>
        </div>

        <form action="{{ route('user.booking.pay.process', $booking->id) }}" method="POST" class="space-y-6">
            @csrf
            <h4 class="text-lg font-black text-black mb-4 uppercase tracking-tighter border-b-2 border-black pb-2">PAYMENT DETAILS (MOCK)</h4>
            
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">CARD NUMBER</label>
                <input type="text" value="4242 4242 4242 4242" required class="w-full h-14 px-4 border border-gray-300 bg-white text-black font-mono focus:border-black transition-all">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">EXPIRY DATE</label>
                    <input type="text" value="12/28" required class="w-full h-14 px-4 border border-gray-300 bg-white text-black font-mono focus:border-black transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">CVC</label>
                    <input type="text" value="123" required class="w-full h-14 px-4 border border-gray-300 bg-white text-black font-mono focus:border-black transition-all">
                </div>
            </div>
            
            <div class="pt-6 mt-6 flex flex-col sm:flex-row gap-4 border-t-2 border-black">
                <button type="submit" class="btn-primary flex-1 h-14 px-6 text-sm flex items-center justify-center gap-2 uppercase w-full">
                    PAY ₹{{ number_format($booking->amount, 2) }}
                </button>
                <a href="/user" class="btn-secondary flex-1 h-14 px-6 text-sm flex items-center justify-center gap-2 uppercase text-center border-gray-300 hover:border-black text-black transition-colors w-full sm:w-auto">
                    CANCEL
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
