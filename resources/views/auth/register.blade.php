<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ParkX</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Oswald:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        sans: ['Inter', 'sans-serif'],
                        display: ['Oswald', 'sans-serif']
                    },
                    colors: {
                        black: '#111111',
                        white: '#ffffff',
                        light: '#f5f5f5',
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #ffffff; color: #111111; }
        
        .form-input {
            background-color: #f5f5f5;
            border: 1px solid #e5e5e5;
            color: #111111;
            transition: all 0.2s ease;
            border-radius: 0;
        }

        .form-input:focus {
            background-color: #ffffff;
            border-color: #111111;
            outline: none;
            box-shadow: none;
        }

        .btn-brand {
            background-color: #111111;
            color: #ffffff;
            border-radius: 9999px; /* pill shape */
            font-weight: 700;
            text-transform: uppercase;
            border: 2px solid #111111;
            transition: all 0.2s ease;
        }

        .btn-brand:hover {
            background-color: #333333;
            border-color: #333333;
            transform: translateY(-1px);
        }

        .glass-overlay {
            background: linear-gradient(to right, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.2));
        }

        .card-minimal {
            background: #ffffff;
            border: 1px solid #e5e5e5;
            box-shadow: none;
        }

        /* Customize scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f5f5f5;
        }
        ::-webkit-scrollbar-thumb {
            background: #e5e5e5;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #111111;
        }
    </style>
</head>
<body class="antialiased h-screen overflow-hidden font-sans">

    <div class="flex h-full w-full">
        <!-- Left Side: Image & Branding -->
        <div class="hidden lg:flex w-1/2 relative bg-black" style="background: linear-gradient(to bottom, rgba(17,17,17,0.3) 0%, rgba(17,17,17,0.85) 100%), url('{{ asset('images/register-bg.jpg') }}'); background-size: cover; background-position: center center; position: relative;">
            
            <div class="absolute inset-0 flex flex-col justify-between p-12 z-10">
                <a href="{{ url('/') }}" class="flex items-center gap-3 font-display font-black text-4xl text-white tracking-tighter position-relative z-10">
                    <img src="{{ asset('parkx-logo.svg') }}" alt="ParkX Logo" class="h-10 w-auto">
                    PARKX
                </a>

                <div>
                    <h2 class="font-display text-8xl font-black text-white uppercase leading-none mb-4 tracking-tighter position-relative z-10">
                        NEVER<br>CIRCLE<br>THE BLOCK.
                    </h2>
                </div>
            </div>
        </div>

        <!-- Right Side: Clean Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white overflow-y-auto">
            
            <div class="w-full max-w-md card-minimal p-8 sm:p-12 my-8">
                
                <!-- Mobile Logo -->
                <div class="lg:hidden mb-10 flex items-center justify-center gap-3 text-center">
                    <img src="{{ asset('parkx-logo.svg') }}" alt="ParkX Logo" class="h-10 w-auto" style="filter: invert(1);">
                    <span class="font-display font-black text-4xl text-black tracking-tighter">PARKX</span>
                </div>

                <div class="text-center lg:text-left mb-8">
                    <h1 class="font-display text-4xl font-black text-black uppercase tracking-tighter mb-2">Join Us</h1>
                    <p class="text-gray-500 text-sm font-medium">CREATE YOUR ACCOUNT</p>
                </div>

                @if(session('error'))
                    <div class="mb-6 p-4 border border-red-500 bg-red-50 text-red-600 text-sm font-medium flex items-center gap-3">
                        <i class="fas fa-exclamation-circle text-xl"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                
                @if(session('success'))
                    <div class="mb-6 p-4 border border-green-500 bg-green-50 text-green-600 text-sm font-medium flex items-center gap-3">
                        <i class="fas fa-check-circle text-xl"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <div>
                        <label for="name" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Full Name</label>
                        <input id="name" name="name" type="text" autocomplete="name" required 
                            class="form-input block w-full py-4 px-4 font-medium"
                            placeholder="JOHN DOE">
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Email Address</label>
                        <input id="email" name="email" type="email" autocomplete="email" required 
                            class="form-input block w-full py-4 px-4 font-medium"
                            placeholder="EMAIL ADDRESS">
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" required 
                            class="form-input block w-full py-4 px-4 font-medium tracking-widest"
                            placeholder="••••••••">
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full btn-brand py-4 text-lg tracking-widest flex justify-center items-center gap-2">
                            CREATE ACCOUNT <i class="fas fa-arrow-right text-sm"></i>
                        </button>
                    </div>
                </form>

                <p class="mt-8 text-center text-sm text-gray-500 font-medium">
                    ALREADY HAVE AN ACCOUNT? 
                    <a href="{{ route('login') }}" class="text-black font-bold uppercase tracking-wider hover:underline ml-1">SIGN IN</a>
                </p>
            </div>
        </div>
    </div>

</body>
</html>
