@extends('layouts.app')

@section('title', 'Sign In')

@section('content')

<div class="w-full">
    <div class="flex min-h-screen w-full relative" style="background-image: url('{{ asset('images/login-bg.png') }}'); background-size: cover; background-position: center left; background-repeat: no-repeat;">
        <div class="w-full md:w-1/2 lg:w-1/3 ml-auto flex flex-col justify-center min-h-screen shadow-2xl p-8 sm:p-12" style="background-color: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); min-height: 100vh;">
            
            <div class="mb-8 text-center flex flex-col items-center">
                <div class="mb-4 mx-auto bg-black rounded-full flex items-center justify-center p-2" style="width: 4rem; height: 4rem; overflow: hidden;">
                    <img src="{{ asset('parkx-logo.svg') }}" alt="ParkX Logo" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <h2 class="text-4xl font-black text-black uppercase tracking-tighter mb-2" style="font-family: 'Oswald', sans-serif; letter-spacing: 2px;">Sign In</h2>
                <p class="text-gray-500 font-bold uppercase" style="font-size: 0.8rem;">Access Your Account</p>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 border border-red-500 bg-red-50 text-red-600 text-sm font-bold flex items-center gap-3 text-uppercase fw-bold">
                    <i class="fas fa-exclamation-circle text-xl"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="mb-4">
                    <label for="email" class="text-xs text-gray-400 font-bold tracking-wider mb-2 block uppercase">Email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                        class="block w-full py-4 px-4 font-bold uppercase transition-all"
                        style="background-color: #f5f5f5; border: 1px solid #e5e5e5; color: #111111; outline: none;"
                        placeholder="EMAIL ADDRESS" onfocus="this.style.borderColor='#111111';" onblur="this.style.borderColor='#e5e5e5';">
                </div>

                <div class="mt-6 mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <label for="password" class="text-xs text-gray-400 font-bold tracking-wider m-0 block uppercase">Password</label>
                        <a href="#" class="text-xs font-bold text-black hover:underline tracking-wider uppercase">Forgot?</a>
                    </div>
                    <input id="password" name="password" type="password" autocomplete="current-password" required 
                        class="block w-full py-4 px-4 font-bold tracking-widest transition-all"
                        style="background-color: #f5f5f5; border: 1px solid #e5e5e5; color: #111111; outline: none;"
                        placeholder="••••••••" onfocus="this.style.borderColor='#111111';" onblur="this.style.borderColor='#e5e5e5';">
                </div>

                <div class="my-6 flex items-center">
                    <input id="remember" name="remember" type="checkbox" 
                        class="h-5 w-5 mt-0 cursor-pointer border-gray-300">
                    <label for="remember" class="text-sm text-gray-600 font-bold cursor-pointer uppercase ml-3">REMEMBER ME</label>
                </div>

                <div class="mt-8 pt-4">
                    <button type="submit" class="w-full py-4 text-lg font-bold uppercase tracking-widest flex justify-center items-center gap-2 transition-all" style="background-color: #111111; color: #ffffff; border: 2px solid #111111;" onmouseover="this.style.backgroundColor='#333333'; this.style.borderColor='#333333';" onmouseout="this.style.backgroundColor='#111111'; this.style.borderColor='#111111';">
                        SIGN IN <i class="fas fa-arrow-right text-sm ml-2"></i>
                    </button>
                </div>
            </form>

            <p class="mt-8 pt-6 text-center text-sm text-gray-500 font-bold uppercase">
                NOT A MEMBER? 
                <a href="{{ route('register') }}" class="text-black font-bold tracking-wider hover:underline ml-1">JOIN NOW</a>
            </p>

        </div>             
    </div>
</div>
@endsection