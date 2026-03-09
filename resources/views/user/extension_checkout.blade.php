@extends('layouts.app')

@section('title', 'Extend Parking Duration')

@section('content')
<div class="max-w-3xl mx-auto py-12 px-4 sm:px-6 lg:px-8 animate-slide-up">
    
    <div class="mb-8">
        <a href="{{ url('/user') }}" class="text-sm font-bold text-gray-500 hover:text-black uppercase tracking-widest flex items-center gap-2 transition-colors">
            <i class="fas fa-arrow-left"></i> BACK TO DASHBOARD
        </a>
    </div>

    <div class="card-minimal p-8 md:p-12">
        <div class="flex items-center gap-4 mb-8 pb-8 border-b border-gray-200">
            <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center border-2 border-blue-200">
                <i class="fas fa-hourglass-half text-2xl"></i>
            </div>
            <div>
                <h1 class="text-4xl font-black text-black tracking-tighter uppercase mb-1">EXTEND DURATION</h1>
                <p class="text-sm font-bold text-gray-500 uppercase tracking-widest">PAYMENT CHECKOUT</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
            <!-- Current Details -->
            <div class="bg-gray-50 p-6 border border-gray-200">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">CURRENT BOOKING</h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase tracking-widest">VEHICLE</p>
                        <p class="font-mono font-bold text-black text-lg">{{ $booking->vehicle_number }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase tracking-widest">SCHEDULED EXIT</p>
                        <p class="font-bold text-red-600 text-lg">{{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</p>
                        <p class="text-xs text-gray-400 font-medium">{{ \Carbon\Carbon::parse($booking->end_time)->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Extension Details -->
            <div class="bg-blue-50 p-6 border-2 border-blue-200 relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 opacity-5">
                    <i class="fas fa-clock text-9xl"></i>
                </div>
                
                <h3 class="text-xs font-bold text-blue-400 uppercase tracking-widest mb-4 relative z-10">EXTENSION REQUEST</h3>
                
                <div class="space-y-4 relative z-10">
                    <div>
                        <p class="text-[10px] text-blue-500 uppercase tracking-widest">ADDED TIME</p>
                        <p class="font-black text-blue-800 text-3xl tracking-tighter">+{{ $extensionData['hours'] }} HOUR(S)</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-blue-500 uppercase tracking-widest">NEW EXIT TIME</p>
                        <p class="font-bold text-blue-700 text-xl">{{ \Carbon\Carbon::parse($booking->end_time)->addHours($extensionData['hours'])->format('h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Details & Final Checkout -->
        <div class="bg-black text-white p-8 mb-8">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-2">EXTENSION FEE</h3>
                    <p class="text-xs text-gray-500">Based on standard hourly rate</p>
                </div>
                <div class="text-right">
                    <p class="text-5xl font-black font-mono tracking-tighter text-white">₹{{ number_format($extensionData['cost'], 0) }}</p>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8 mt-4">
                <form id="payment-form" action="{{ route('user.extension.process-payment') }}" method="POST">
                    @csrf
                    
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">PAYMENT SECURE CHECKOUT</h4>
                    
                    <div class="space-y-4 mb-8">
                        <div>
                            <label class="block text-[10px] text-gray-500 uppercase tracking-widest mb-1">NAME ON CARD</label>
                            <input type="text" required class="w-full bg-gray-900 border border-gray-700 text-white px-4 py-3 font-mono text-sm focus:border-blue-500 focus:outline-none transition-colors" placeholder="YOUR NAME">
                        </div>
                        
                        <div>
                            <label class="block text-[10px] text-gray-500 uppercase tracking-widest mb-1">CARD NUMBER</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"><i class="far fa-credit-card"></i></span>
                                <input type="text" required pattern="\d{16}" maxlength="16" class="w-full bg-gray-900 border border-gray-700 text-white pl-10 pr-4 py-3 font-mono text-sm tracking-widest focus:border-blue-500 focus:outline-none transition-colors" placeholder="0000 0000 0000 0000">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] text-gray-500 uppercase tracking-widest mb-1">EXPIRATION (MM/YY)</label>
                                <input type="text" required pattern="\d{2}/\d{2}" maxlength="5" class="w-full bg-gray-900 border border-gray-700 text-white px-4 py-3 font-mono text-sm tracking-widest focus:border-blue-500 focus:outline-none transition-colors" placeholder="MM/YY">
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 uppercase tracking-widest mb-1">CVV</label>
                                <input type="password" required pattern="\d{3}" maxlength="3" class="w-full bg-gray-900 border border-gray-700 text-white px-4 py-3 font-mono text-sm tracking-widest focus:border-blue-500 focus:outline-none transition-colors" placeholder="•••">
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="pay-button" class="w-full bg-blue-600 hover:bg-blue-500 text-white transition-colors duration-200 py-5 text-sm font-black tracking-widest uppercase flex items-center justify-center gap-3 relative overflow-hidden group">
                        <span id="button-text" class="relative z-10 flex items-center gap-2">
                            <i class="fas fa-lock"></i> PAY NOW SECURELY
                        </span>
                        <div class="absolute inset-0 h-full w-0 bg-white/20 group-hover:w-full transition-all duration-300 ease-out z-0"></div>
                    </button>
                    
                    <div id="loading-state" class="hidden w-full bg-black border border-gray-800 text-white py-5 flex items-center justify-center gap-3">
                        <i class="fas fa-circle-notch fa-spin text-blue-500 text-xl"></i>
                        <span class="text-sm font-black tracking-widest uppercase">AUTHORIZING PAYMENT...</span>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
</div>

<script>
    document.getElementById('payment-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('pay-button');
        const loading = document.getElementById('loading-state');
        
        btn.classList.add('hidden');
        loading.classList.remove('hidden');
        
        // Mock gateway delay of 2.5 seconds
        setTimeout(() => {
            this.submit();
        }, 2500);
    });
</script>
@endsection
