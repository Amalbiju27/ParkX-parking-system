@extends('layouts.app')

@section('title', 'Vehicle Categories')

@section('content')
    <div class="relative z-20 max-w-4xl mx-auto px-4 py-12 space-y-8">
        
        <!-- Header & Action Buttons -->
        <div class="card-minimal p-8 lg:p-12 animate-slide-up">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8">
                <div>
                    <h1 class="text-4xl lg:text-5xl font-black text-black tracking-tighter uppercase mb-2">VEHICLE CATEGORIES</h1>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-500">PRICING CONFIGURATION FOR ALL VEHICLE TYPES</p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="/admin/vehicle-categories/create" 
                       class="btn-primary px-10 py-5 text-sm flex items-center gap-3 h-14 uppercase tracking-widest flex-1 justify-center">
                        <i class="fas fa-plus"></i>
                        ADD CATEGORY
                    </a>
                    
                    <a href="{{ url('/admin') }}" 
                       class="btn-secondary px-10 py-5 text-sm flex items-center gap-3 h-14 uppercase tracking-widest flex-1 justify-center">
                        <i class="fas fa-arrow-left"></i>
                        BACK TO DASHBOARD
                    </a>
                </div>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="p-6 bg-green-50 border border-green-200 flex items-center gap-4">
                    <i class="fas fa-check-circle text-green-600 text-2xl flex-shrink-0"></i>
                    <span class="text-sm font-bold uppercase tracking-widest text-green-800">{{ session('success') }}</span>
                </div>
            @endif
        </div>

        <!-- Categories Table -->
        <div class="card-minimal p-8 lg:p-12 animate-slide-up" style="animation-delay: 0.1s;">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b-2 border-black">
                            <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-sm">CATEGORY NAME</th>
                            <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-sm text-center">BASE CHARGE</th>
                            <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-sm text-center">HOURLY RATE</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($categories as $cat)
                        <tr class="hover:bg-gray-50 transition-colors h-20">
                            <td class="py-5 px-6 font-bold text-black uppercase">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-black rounded-full flex items-center justify-center text-white">
                                        <i class="fas fa-tags text-sm"></i>
                                    </div>
                                    {{ $cat->name }}
                                </div>
                            </td>
                            <td class="py-5 px-6 text-center">
                                <span class="font-mono font-black text-2xl text-black">₹{{ number_format($cat->base_charge, 2) }}</span>
                            </td>
                            <td class="py-5 px-6 text-center">
                                <span class="font-mono font-black text-2xl text-black">₹{{ number_format($cat->hourly_rate, 2) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-16 text-center">
                                <div class="flex flex-col items-center space-y-4">
                                    <i class="fas fa-tags text-6xl text-gray-200"></i>
                                    <h3 class="text-xl font-bold text-gray-400 uppercase tracking-widest">NO CATEGORIES FOUND</h3>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">CREATE YOUR FIRST VEHICLE CATEGORY TO GET STARTED</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
@endsection
