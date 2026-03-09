@extends('layouts.app')

@section('title', 'Sign In')

@section('content')

<div class="container-fluid p-0 m-0 w-full">
    <div class="d-flex min-vh-100 w-100 flex min-h-screen w-full relative" style="background-image: url('{{ asset('images/login-bg.png') }}'); background-size: cover; background-position: center left; background-repeat: no-repeat;">
        


        <div class="ms-auto col-12 col-md-6 col-lg-4 p-5 d-flex flex-column justify-content-center shadow-lg transform translate-x-0 w-full md:w-1/2 lg:w-1/3 ml-auto flex flex-col justify-center min-h-screen shadow-2xl p-8 sm:p-12" style="background-color: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); min-height: 100vh;">
            
            <div class="mb-5 text-center d-flex flex-column align-items-center">
                <div class="mb-4 mx-auto bg-black rounded-circle d-flex align-items-center justify-content-center p-2" style="width: 4rem; height: 4rem; overflow: hidden;">
                    <img src="{{ asset('parkx-logo.svg') }}" alt="ParkX Logo" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <h2 class="text-uppercase fw-bolder text-4xl font-black text-black uppercase tracking-tighter mb-2" style="font-family: 'Oswald', sans-serif; letter-spacing: 2px;">Sign In</h2>
                <p class="text-muted text-uppercase text-gray-500 font-bold uppercase" style="font-size: 0.8rem;">Access Your Account</p>
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
                    <label for="email" class="form-label text-xs text-gray-400 text-uppercase fw-bold tracking-wider mb-2 block uppercase font-bold">Email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                        class="form-control rounded-0 block w-full py-4 px-4 fw-bold uppercase"
                        style="background-color: #f5f5f5; border: 1px solid #e5e5e5; color: #111111;"
                        placeholder="EMAIL ADDRESS">
                </div>

                <div class="my-4 mt-6">
                    <div class="d-flex justify-content-between align-items-center mb-2 flex justify-between">
                        <label for="password" class="form-label text-xs text-gray-400 text-uppercase fw-bold tracking-wider m-0 block uppercase font-bold">Password</label>
                        <a href="#" class="text-xs fw-bold text-black hover:underline text-uppercase tracking-wider font-bold">Forgot?</a>
                    </div>
                    <input id="password" name="password" type="password" autocomplete="current-password" required 
                        class="form-control rounded-0 block w-full py-4 px-4 fw-bold tracking-widest"
                        style="background-color: #f5f5f5; border: 1px solid #e5e5e5; color: #111111;"
                        placeholder="••••••••">
                </div>

                <div class="form-check my-4 mt-6 d-flex align-items-center flex items-center">
                    <input id="remember" name="remember" type="checkbox" 
                        class="form-check-input rounded-0 border-gray-300 me-3 h-5 w-5 mt-0 cursor-pointer">
                    <label for="remember" class="form-check-label text-sm text-gray-600 fw-bold cursor-pointer text-uppercase ms-3 ml-3 font-bold">REMEMBER ME</label>
                </div>

                <div class="pt-4 mt-8">
                    <button type="submit" class="btn btn-dark w-100 rounded-0 py-4 text-lg fw-bold text-uppercase tracking-widest d-flex justify-content-center align-items-center gap-2 w-full flex justify-center items-center font-bold" style="background-color: #111111; color: #ffffff; border: 2px solid #111111;">
                        SIGN IN <i class="fas fa-arrow-right text-sm ms-2 ml-2"></i>
                    </button>
                </div>
            </form>

            <p class="mt-5 pt-4 mt-8 pt-6 text-center text-sm text-gray-500 fw-bold font-bold">
                NOT A MEMBER? 
                <a href="{{ route('register') }}" class="text-black fw-bold text-uppercase tracking-wider hover:underline ms-1 ml-1 font-bold">JOIN NOW</a>
            </p>

        </div>             
    </div>
</div>
@endsection