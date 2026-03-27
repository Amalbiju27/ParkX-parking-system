@extends('layouts.app')

@section('title', 'Add Owner')

@section('content')
<div class="w-full min-h-screen py-5" style="background-image: url('{{ asset('images/dashboard-bg.jpg') }}'); background-size: cover; background-position: center; background-attachment: fixed;">
    <div class="container p-4 p-md-5 mx-auto max-w-3xl shadow-lg" style="background-color: rgba(255, 255, 255, 0.92); backdrop-filter: blur(15px); border: 1px solid rgba(0,0,0,0.1); border-radius: 0;">
        
        <!-- Header -->
        <div class="card-minimal border border-dark rounded-0 p-8 lg:p-12 mb-8 animate-slide-up bg-white">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-black flex items-center justify-center overflow-hidden p-3 border-2 border-black">
                    <i class="fas fa-user-tie text-white text-3xl"></i>
                </div>
                <div>
                    <h1 class="text-4xl font-black text-black tracking-tighter uppercase mb-1 font-display">ADD OWNER</h1>
                    <p class="text-sm text-gray-500 font-bold uppercase tracking-widest">ONBOARD A NEW PARKING SPACE ADMINISTRATOR</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-8 p-6 bg-green-50 border-2 border-green-600 flex items-center gap-4 animate-slide-up">
                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                <span class="text-green-800 font-bold uppercase tracking-wider text-sm">{{ session('success') }}</span>
            </div>
        @endif
        
        @if(session('error'))
            <div class="mb-8 p-6 bg-red-50 border-2 border-red-600 flex items-center gap-4 animate-slide-up">
                <i class="fas fa-exclamation-circle text-red-600 text-2xl"></i>
                <span class="text-red-800 font-bold uppercase tracking-wider text-sm">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Form Container -->
        <div class="card-minimal border border-dark rounded-0 p-8 lg:p-12 animate-slide-up bg-white" style="animation-delay: 0.1s;">
            <form method="POST" action="/admin/owners" class="space-y-8">
                @csrf
                
                <div class="grid grid-cols-1 gap-8 shadow-none">
                    
                    <!-- Owner Name -->
                    <div class="space-y-3">
                        <label class="block text-sm font-black text-black uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-user text-black"></i> FULL NAME
                        </label>
                        <input type="text" name="name" placeholder="ENTER OWNER's FULL NAME" value="{{ old('name') }}" required
                               class="w-full h-14 px-5 rounded-0 text-black bg-white border-2 border-black focus:border-black focus:ring-0 transition-all outline-none font-bold uppercase placeholder-gray-400">
                    </div>

                    <!-- Email -->
                    <div class="space-y-3">
                        <label class="block text-sm font-black text-black uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-envelope text-black"></i> EMAIL IDENTITY
                        </label>
                        <input type="email" name="email" placeholder="OWNER@PARKX.COM" value="{{ old('email') }}" required
                               class="w-full h-14 px-5 rounded-0 text-black bg-white border-2 border-black focus:border-black focus:ring-0 transition-all outline-none font-bold uppercase placeholder-gray-400">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Password -->
                        <div class="space-y-3">
                            <label class="block text-sm font-black text-black uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-lock text-black"></i> SECURE PASSWORD
                            </label>
                            <input type="password" name="password" placeholder="••••••••" required
                                   class="w-full h-14 px-5 rounded-0 text-black bg-white border-2 border-black focus:border-black focus:ring-0 transition-all outline-none font-bold placeholder-gray-400 font-mono">
                        </div>

                        <!-- Contact -->
                        <div class="space-y-3">
                            <label class="block text-sm font-black text-black uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-phone text-black"></i> CONTACT NUMBER
                            </label>
                            <input type="text" name="contact" placeholder="+91 00000 00000" value="{{ old('contact') }}" required
                                   class="w-full h-14 px-5 rounded-0 text-black bg-white border-2 border-black focus:border-black focus:ring-0 transition-all outline-none font-bold uppercase placeholder-gray-400 font-mono">
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="space-y-3">
                        <label class="block text-sm font-black text-black uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-black"></i> PHYSICAL ADDRESS
                        </label>
                        <textarea name="address" placeholder="RESIDENTIAL OR BUSINESS ADDRESS..." required
                                  class="w-full h-32 px-5 py-4 rounded-0 text-black bg-white border-2 border-black focus:border-black focus:ring-0 transition-all outline-none resize-none font-bold uppercase placeholder-gray-400"></textarea>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t-2 border-gray-200 mt-8">
                    <button type="submit" 
                            class="flex-[2] h-16 bg-black text-white font-bold text-lg rounded-0 flex items-center justify-center gap-3 uppercase tracking-widest hover:bg-gray-800 transition-colors border-2 border-black">
                        <i class="fas fa-plus-circle text-xl"></i>
                        CONFIRM REGISTRATION
                    </button>
                    
                    <a href="{{ url('/admin') }}" 
                       class="flex-1 h-16 bg-white text-black font-bold text-lg rounded-0 flex items-center justify-center gap-2 uppercase tracking-widest hover:bg-gray-100 transition-colors border-2 border-black">
                        <i class="fas fa-times"></i>
                        CANCEL
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
