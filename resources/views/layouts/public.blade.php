  <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ParkX - Smart Parking Management')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif']
                    },
                    colors: {
                        base: {
                            DEFAULT: '#1e1b2e',
                            dark: '#151320',
                            light: '#2a283e'
                        },
                        brand: {
                            DEFAULT: '#7c5cfa',
                            hover: '#6f48eb',
                            glow: 'rgba(124, 92, 250, 0.4)'
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.8s ease-out forwards',
                        'slide-up': 'slideUp 0.8s ease-out forwards',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #151320;
            color: #e0e0e0;
        }

        .glass-nav {
            background: rgba(30, 27, 46, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .btn-brand {
            background-color: #7c5cfa;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(124, 92, 250, 0.3);
        }

        .btn-brand:hover {
            background-color: #6f48eb;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(124, 92, 250, 0.5);
        }

        .btn-outline-brand {
            background-color: transparent;
            border: 2px solid #7c5cfa;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-outline-brand:hover {
            background-color: rgba(124, 92, 250, 0.1);
            border-color: #6f48eb;
            transform: translateY(-2px);
        }

        .nav-link {
            transition: color 0.3s ease;
        }
        
        .nav-link:hover {
            color: #7c5cfa;
        }

        .card-dark {
            background-color: #2a283e;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-dark:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-brand flex items-center justify-center shadow-lg shadow-brand/30">
                        <i class="fas fa-parking text-white text-xl"></i>
                    </div>
                    <a href="{{ route('home') }}" class="font-display font-bold text-2xl text-white tracking-tight">ParkX</a>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="nav-link text-gray-300 font-medium text-sm uppercase tracking-wider">Home</a>
                    <a href="{{ route('about') }}" class="nav-link text-gray-300 font-medium text-sm uppercase tracking-wider">About Us</a>
                    <a href="{{ route('help') }}" class="nav-link text-gray-300 font-medium text-sm uppercase tracking-wider">Need Help</a>
                    <a href="{{ route('contact') }}" class="nav-link text-gray-300 font-medium text-sm uppercase tracking-wider">Contact</a>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ url('/admin') }}" class="btn-brand px-6 py-2.5 rounded-xl font-medium text-sm tracking-wide">Dashboard</a>
                        @elseif(auth()->user()->role === 'owner')
                            <a href="{{ url('/owner') }}" class="btn-brand px-6 py-2.5 rounded-xl font-medium text-sm tracking-wide">Dashboard</a>
                        @else
                            <a href="{{ url('/user') }}" class="btn-brand px-6 py-2.5 rounded-xl font-medium text-sm tracking-wide">Dashboard</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="text-white hover:text-brand font-medium text-sm transition-colors px-4">Login</a>
                        <a href="{{ route('register') }}" class="btn-brand px-6 py-2.5 rounded-xl font-medium text-sm tracking-wide">Register</a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button type="button" class="text-gray-300 hover:text-white focus:outline-none" aria-label="Toggle menu">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow pt-20">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-base-dark border-t border-white/5 py-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-3">
                    <i class="fas fa-parking text-brand text-xl"></i>
                    <span class="font-display font-bold text-xl text-white">ParkX</span>
                </div>
                <div class="text-gray-500 text-sm">
                    &copy; {{ date('Y') }} ParkX. All rights reserved.
                </div>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-500 hover:text-brand transition-colors"><i class="fab fa-twitter text-lg"></i></a>
                    <a href="#" class="text-gray-500 hover:text-brand transition-colors"><i class="fab fa-instagram text-lg"></i></a>
                    <a href="#" class="text-gray-500 hover:text-brand transition-colors"><i class="fab fa-linkedin text-lg"></i></a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
