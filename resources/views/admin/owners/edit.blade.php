@extends('layouts.app')

@section('title', 'Edit Owner')

@section('content')
<div class="max-w-lg mx-auto py-16">
    <div class="card-minimal p-8 rounded-none animate-slide-up">
        <h2 class="text-3xl font-black text-black mb-8 uppercase tracking-tighter">EDIT OWNER</h2>

        <form method="POST" action="/admin/owners/{{ $owner->id }}/update" class="space-y-6">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">NAME</label>
                <input type="text" name="name" value="{{ $owner->name }}" class="w-full h-14 px-4 border border-gray-300 bg-white text-black font-mono focus:border-black transition-all" required>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">EMAIL</label>
                <input type="email" name="email" value="{{ $owner->email }}" class="w-full h-14 px-4 border border-gray-300 bg-white text-black font-mono focus:border-black transition-all" required>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">STATUS</label>
                <select name="status" class="w-full h-14 px-4 border border-gray-300 bg-white text-black font-mono focus:border-black transition-all appearance-none rounded-none" required>
                    <option value="1" {{ $owner->status == 1 ? 'selected' : '' }}>ACTIVE</option>
                    <option value="0" {{ $owner->status == 0 ? 'selected' : '' }}>INACTIVE</option>
                </select>
            </div>

            <button type="submit" class="w-full btn-primary py-4 text-sm font-bold tracking-widest uppercase mt-4">
                UPDATE OWNER
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="/admin/parking-spaces" class="text-sm font-bold tracking-widest uppercase text-gray-500 hover:text-black transition-colors">
                <i class="fas fa-arrow-left"></i> BACK
            </a>
        </div>
    </div>
</div>
@endsection
