@extends('layouts.app')

@section('title', 'Parking Spaces')

@section('content')
    <div class="relative z-20 max-w-6xl mx-auto px-4 py-12 space-y-8">
        
        <!-- Header & Action Buttons -->
        <div class="card-minimal p-8 lg:p-12 animate-slide-up">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8">
                <div>
                    <h1 class="text-4xl lg:text-5xl font-black text-black tracking-tighter uppercase mb-2">PARKING SPACES</h1>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-500">MANAGE ALL PARKING FACILITIES & OWNER ASSIGNMENTS</p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ url('/admin/parking-spaces/create') }}" 
                       class="btn-primary px-10 py-5 text-sm flex items-center gap-3 h-14 uppercase tracking-widest flex-1 justify-center">
                        <i class="fas fa-plus"></i>
                        ADD NEW SPACE
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

        <!-- Parking Spaces Table -->
        <div class="card-minimal p-8 lg:p-12 animate-slide-up" style="animation-delay: 0.1s;">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b-2 border-black">
                            <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-sm">NAME</th>
                            <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-sm">LOCATION</th>
                            <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-sm text-center">OWNER</th>
                            <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-sm text-center">ASSIGN OWNER</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($spaces as $space)
                        <tr class="hover:bg-gray-50 transition-colors h-20">
                            <td class="py-5 px-6 font-bold text-black uppercase">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-black rounded-full flex items-center justify-center text-white">
                                        <i class="fas fa-parking text-sm"></i>
                                    </div>
                                    {{ $space->name }}
                                </div>
                            </td>
                            <td class="py-5 px-6 text-gray-500 text-xs font-bold uppercase tracking-widest">{{ $space->location }}</td>
                            <td class="py-5 px-6 text-center">
                                @if($space->owner_name)
                                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 border border-green-200 text-green-700 font-bold text-xs uppercase tracking-widest">
                                        <i class="fas fa-user-check"></i>
                                        {{ $space->owner_name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 border border-red-200 text-red-700 font-bold text-xs uppercase tracking-widest">
                                        <i class="fas fa-user-times"></i>
                                        NOT ASSIGNED
                                    </span>
                                @endif
                            </td>
                            <td class="py-5 px-6 text-center">
                                <form method="POST" action="/admin/parking-spaces/{{ $space->id }}/assign-owner" class="inline-flex items-center gap-2 m-0">
                                    @csrf
                                    <select name="owner_id" class="h-12 px-4 border border-gray-300 bg-white text-black font-mono focus:border-black transition-all appearance-none rounded-none text-sm min-w-[180px]">
                                        <option value="">SELECT OWNER</option>
                                        @foreach($owners as $owner)
                                            <option value="{{ $owner->id }}"
                                                    @if($space->owner_id == $owner->id) selected @endif>
                                                {{ $owner->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" 
                                            class="btn-primary h-12 px-6 text-sm flex items-center gap-2 uppercase tracking-widest m-0 rounded-none">
                                        <i class="fas fa-user-plus"></i>
                                        ASSIGN
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
@endsection
