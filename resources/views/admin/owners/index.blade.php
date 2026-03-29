@extends('layouts.app')

@section('title', 'Manage Owners')

@section('content')
<div class="p-6 sm:p-12 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-12">
        <div>
            <h1 class="text-4xl font-black tracking-tighter mb-2">OWNERS MANAGEMENT</h1>
            <p class="text-slate-500 font-medium">View and manage all registered parking space owners.</p>
        </div>
        <div>
            <a href="/admin/owners/create" class="inline-flex items-center gap-2 bg-black text-white px-6 py-3 rounded-full font-bold text-sm tracking-wide hover:bg-slate-800 transition-all shadow-lg active:scale-95">
                <i class="fas fa-plus"></i>
                REGISTER NEW OWNER
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-xl mb-8 flex items-center gap-3 animate-slide-up">
        <i class="fas fa-check-circle"></i>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    <div class="card-minimal overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-bottom border-e5e5e5">
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Owner Name</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Email Address</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Contact Number</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($owners as $owner)
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold">
                                    {{ strtoupper(substr($owner->name, 0, 1)) }}
                                </div>
                                <span class="font-bold text-slate-800">{{ $owner->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-600 font-medium">{{ $owner->email }}</td>
                        <td class="px-6 py-4 text-slate-600 font-medium">{{ $owner->contact ?? 'Not Provided' }}</td>
                        <td class="px-6 py-4">
                            @if($owner->status == 1)
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-full border border-green-100">
                                <span class="w-1 h-1 bg-green-500 rounded-full"></span>
                                ACTIVE
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-slate-50 text-slate-500 text-xs font-bold rounded-full border border-slate-200">
                                <span class="w-1 h-1 bg-slate-400 rounded-full"></span>
                                INACTIVE
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="/admin/owners/{{ $owner->id }}/edit" class="w-8 h-8 flex items-center justify-center rounded-full text-slate-400 hover:text-black hover:bg-slate-100 transition-all" title="Edit Owner">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {{-- Add other actions if needed --}}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 font-medium italic">
                            No owners registered in the system yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Footer Actions --}}
    <div class="mt-12 flex justify-center">
        <a href="/admin" class="text-slate-400 font-bold text-sm tracking-widest hover:text-black transition-colors flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            BACK TO DASHBOARD
        </a>
    </div>
</div>
@endsection
