@extends('layouts.app')

@section('title', 'Add Owner')

@section('content')
    <style>
        .glass-panel {
            background: rgba(15, 15, 20, 0.9);
            backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.7);
        }

        .btn-primary {
            background: linear-gradient(135deg, #00f2ff 0%, #0099cc 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #00f2ff 0%, #00d4e6 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(0, 242, 255, 0.3);
        }

        .btn-secondary {
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #00f2ff;
            box-shadow: 0 8px 20px rgba(0, 242, 255, 0.2);
        }

        input, textarea, select {
            background: rgba(20, 20, 25, 0.9) !important;
            border: 2px solid rgba(255, 255, 255, 0.15) !important;
            backdrop-filter: blur(10px) !important;
            color: white !important;
        }

        input:focus, textarea:focus, select:focus {
            border-color: #00f2ff !important;
            box-shadow: 0 0 0 3px rgba(0, 242, 255, 0.2) !important;
            outline: none !important;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }
    </style>

    <div class="relative z-20 max-w-lg mx-auto px-4 py-16">
        
        <!-- Header -->
        <div class="glass-panel rounded-2xl p-6 lg:p-8 mb-8 animate-slide-up">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-accent-glow to-accent-dark rounded-xl flex items-center justify-center shadow-xl animate-pulse-glow">
                    <i class="fas fa-user-plus text-lg text-black"></i>
                </div>
                <div>
                    <h1 class="font-tech text-3xl lg:text-4xl font-bold text-white tracking-tight">ADD OWNER</h1>
                    <p class="text-sm text-slate-400 mt-1">Create new parking space owner account</p>
                </div>
            </div>
        </div>

        <!-- Main Form -->
        <div class="glass-panel rounded-2xl p-8 animate-slide-up" style="animation-delay: 0.1s;">
            
            <!-- Form -->
            <form method="POST" action="/admin/owners" class="space-y-6">
                @csrf
                
                <!-- Owner Name -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-white font-tech uppercase tracking-wider flex items-center gap-2 text-accent-glow">
                        <i class="fas fa-user"></i> Full Name
                    </label>
                    <input type="text" 
                           name="name" 
                           placeholder="John Doe"
                           value="{{ old('name') }}"
                           required
                           class="w-full h-12 px-4 rounded-xl text-lg font-medium placeholder-slate-500 focus:ring-2 focus:ring-accent-glow/30 transition-all">
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-white font-tech uppercase tracking-wider flex items-center gap-2 text-accent-glow">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input type="email" 
                           name="email" 
                           placeholder="john.doe@example.com"
                           value="{{ old('email') }}"
                           required
                           class="w-full h-12 px-4 rounded-xl text-lg font-medium placeholder-slate-500 focus:ring-2 focus:ring-accent-glow/30 transition-all">
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-white font-tech uppercase tracking-wider flex items-center gap-2 text-accent-glow">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input type="password" 
                           name="password" 
                           placeholder="••••••••"
                           required
                           class="w-full h-12 px-4 rounded-xl text-lg font-medium placeholder-slate-500 focus:ring-2 focus:ring-accent-glow/30 transition-all">
                </div>

                <!-- Contact -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-white font-tech uppercase tracking-wider flex items-center gap-2 text-accent-glow">
                        <i class="fas fa-phone"></i> Contact Number
                    </label>
                    <input type="text" 
                           name="contact" 
                           placeholder="+91 98765 43210"
                           value="{{ old('contact') }}"
                           class="w-full h-12 px-4 rounded-xl text-lg font-medium placeholder-slate-500 focus:ring-2 focus:ring-accent-glow/30 transition-all">
                </div>

                <!-- Address -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-white font-tech uppercase tracking-wider flex items-center gap-2 text-accent-glow">
                        <i class="fas fa-map-marker-alt"></i> Address
                    </label>
                    <textarea name="address" 
                              placeholder="Enter complete address..."
                              class="w-full px-4 py-4 rounded-xl text-lg font-medium placeholder-slate-500 focus:ring-2 focus:ring-accent-glow/30 transition-all">{{ old('address') }}</textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-2">
                    <button type="submit" 
                            class="btn-primary flex-1 h-14 px-6 text-black font-tech font-bold text-lg rounded-2xl flex items-center justify-center gap-2 uppercase tracking-wide shadow-xl hover:shadow-2xl transition-all">
                        <i class="fas fa-user-plus"></i>
                        Create Owner
                    </button>
                    
                    <a href="{{ url('/admin') }}" 
                       class="btn-secondary flex-1 h-14 px-6 text-white font-tech font-bold text-lg rounded-2xl flex items-center justify-center gap-2 shadow-xl">
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
                    </a>
                </div>
            </form>
        </div>
@endsection
