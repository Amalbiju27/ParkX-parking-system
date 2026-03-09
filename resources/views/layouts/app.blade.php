<!DOCTYPE html>
<html lang="en" class="min-h-screen bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - ParkX</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Import Inter and Oswald from Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Oswald:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        sans: ['Inter', 'Helvetica Neue', 'sans-serif'],
                        display: ['Oswald', 'sans-serif']
                    },
                    colors: {
                        black: '#111111',
                        white: '#ffffff',
                        light: '#f5f5f5',
                        brand: {
                            DEFAULT: '#111111',
                            hover: '#333333'
                        }
                    },
                    animation: {
                        'slide-up': 'slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                        'fade-in': 'fadeIn 0.4s ease-out',
                    },
                    keyframes: {
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Global Typography for "UNBEATABLE" feel */
        h1, h2, h3, h4, h5, h6, .card-title, .nav-heading {
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        /* Minimalist Components */
        .card-minimal {
            background: #ffffff;
            border: 1px solid #e5e5e5;
            border-radius: 0;
            box-shadow: none;
            transition: border-color 0.2s ease;
        }
        
        .card-minimal:hover {
            border-color: #111111;
        }

        .card-light {
            background: #f5f5f5;
            border: none;
            border-radius: 0;
        }

        /* Pill-shaped solid buttons */
        .btn-primary {
            background: #111111;
            color: #ffffff;
            border-radius: 9999px; /* pill shape */
            font-weight: 700;
            transition: all 0.2s ease;
            text-transform: uppercase;
            border: 2px solid #111111;
        }

        .btn-primary:hover {
            background: #333333;
            border-color: #333333;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #ffffff;
            color: #111111;
            border: 2px solid #111111;
            border-radius: 9999px; /* pill shape */
            font-weight: 700;
            transition: all 0.2s ease;
            text-transform: uppercase;
        }

        .btn-secondary:hover {
            background: #f5f5f5;
            transform: translateY(-1px);
        }

        .btn-brand {
            background: #111111;
            color: #ffffff;
            border-radius: 9999px;
            font-weight: 700;
            text-transform: uppercase;
            transition: all 0.2s ease;
            border: 2px solid #111111;
        }

        .btn-brand:hover {
            background: #333333;
            border-color: #333333;
        }

        /* Input styling */
        input, select {
            background: #f5f5f5 !important;
            border: 1px solid #e5e5e5 !important;
            color: #111111 !important;
            border-radius: 0 !important;
            font-family: 'Inter', sans-serif;
        }

        input:focus, select:focus {
            border-color: #111111 !important;
            box-shadow: none !important;
            outline: none !important;
        }

        /* Status colors flattened */
        .status-available { 
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .status-occupied { 
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .table-row-hover:hover {
            background: #f9f9f9;
        }

        /* Navbar minimalism */
        .navbar-minimal {
            background: #ffffff;
            border-bottom: 1px solid #e5e5e5;
        }
        
        .nav-link {
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            color: #111111;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 0.05em;
            transition: color 0.2s;
            padding: 0.5rem 0;
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: #111111;
            transition: width 0.2s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

    </style>
</head>
<body class="font-sans text-black min-h-screen bg-white">

    <!-- Hyper-minimalist Top Navbar -->
    @auth
    <nav class="navbar-minimal sticky top-0 z-50 py-4 px-6 sm:px-12 flex items-center justify-between">
        <div class="flex items-center gap-8">
            <a href="/" class="flex items-center gap-2 nav-heading text-3xl font-black tracking-tighter">
                <img src="{{ asset('parkx-logo.svg') }}" alt="ParkX Logo" class="h-8 w-auto">
                PARKX
            </a>
            <div class="hidden md:flex items-center gap-6">
                <!-- Check roles for links -->
                @if(auth()->user()->role == 'admin')
                    <a href="/admin" class="nav-link">Dashboard</a>
                    <a href="/admin/parking-spaces" class="nav-link">Spaces</a>
                    <a href="/admin/vehicle-categories" class="nav-link">Categories</a>
                @elseif(auth()->user()->role == 'owner')
                    <a href="/owner" class="nav-link">Dashboard</a>
                    <a href="/owner/vehicle-entry" class="nav-link">Entry</a>
                    <a href="/owner/vehicle-exit" class="nav-link">Exit</a>
                @elseif(auth()->user()->role == 'user')
                    <a href="/user" class="nav-link">Dashboard</a>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-6">
            <div class="hidden sm:flex items-center bg-light px-4 py-2 rounded-full border border-e5e5e5">
                <i class="fas fa-search text-slate-400 text-sm"></i>
                <input type="text" placeholder="Search" class="bg-transparent border-none focus:ring-0 text-sm ml-2 h-auto py-0">
            </div>
            
            <form method="POST" action="/logout" class="inline">
                @csrf
                <button type="submit" class="font-bold text-sm tracking-wide hover:underline cursor-pointer">
                    LOGOUT
                </button>
            </form>
        </div>
    </nav>
    @endauth

    <main class="w-100 w-full">
        @yield('content')
    </main>
</body>
</html>
