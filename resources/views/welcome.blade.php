<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ParkX | The New Standard in Urban Parking</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #000; color: #fff; margin: 0; padding: 0; }
        h1, h2, h3, .oswald-text { font-family: 'Oswald', sans-serif; }
        .hero-section {
            background: linear-gradient(to bottom, rgba(17,17,17,0.1) 0%, rgba(17,17,17,0.9) 100%), url('{{ asset('images/marketing-bg.png') }}');
            background-size: cover; background-position: center;
        }
        .nav-link-custom { transition: opacity 0.3s ease; }
        .nav-link-custom:hover { opacity: 0.6; }
        .stat-divider { width: 2px; height: 40px; background-color: rgba(255,255,255,0.2); }
        /* Scroll Animation Classes */
        .fade-up-element {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.8s cubic-bezier(0.25, 1, 0.5, 1), transform 0.8s cubic-bezier(0.25, 1, 0.5, 1);
        }
        .fade-up-element.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        .delay-1 { transition-delay: 0.1s; }
        .delay-2 { transition-delay: 0.3s; }
        .delay-3 { transition-delay: 0.5s; }
    </style>
</head>
<body class="antialiased d-flex flex-column min-vh-100">
    <nav class="navbar position-absolute w-100 p-4" style="z-index: 10;">
        <div class="container-fluid px-md-5">
            <a class="navbar-brand d-flex align-items-center gap-2 text-white fw-bolder oswald-text" style="font-size: 2.5rem; letter-spacing: 2px;" href="{{ route('home') }}">
                <img src="{{ asset('parkx-logo.svg') }}" alt="ParkX Logo" style="height: 3rem; width: auto;">
                PARKX
            </a>
            <div class="d-flex gap-3">
                <a href="{{ route('login') }}" class="btn btn-outline-light rounded-0 fw-bold px-4 py-2 oswald-text" style="letter-spacing: 1px;">SIGN IN</a>
                <a href="{{ route('register') }}" class="btn btn-light text-dark rounded-0 fw-bold px-4 py-2 oswald-text" style="letter-spacing: 1px;">JOIN NOW</a>
            </div>
        </div>
    </nav>
    <main class="hero-section d-flex flex-column justify-content-center position-relative w-100" style="min-height: 85vh; padding-top: 100px;">
        <div class="container px-4 text-end" style="z-index: 2;">
            <div class="ms-auto" style="max-width: 800px;">
                <h1 class="display-1 fw-bolder text-white text-uppercase mb-3" style="font-size: clamp(2.5rem, 7vw, 6rem); letter-spacing: -2px; text-shadow: 0 10px 30px rgba(0,0,0,0.8);">
                    Parking, Simplified.
                </h1>
                <p class="lead text-white-50 fw-bold mb-5 ms-auto" style="max-width: 600px; font-size: clamp(0.9rem, 1.8vw, 1.15rem); line-height: 1.6;">
                    Stop circling. Start arriving. Experience the city's most advanced vehicle management system designed for the modern driver.
                </p>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('register') }}" class="btn btn-light text-dark rounded-0 fw-bolder px-5 py-3 oswald-text shadow-lg" style="font-size: 1.1rem; letter-spacing: 1px; transition: transform 0.2s;">
                        RESERVE NOW
                    </a>
                </div>
            </div>
        </div>
    </main>
    <div class="bg-black border-top border-bottom py-4" style="border-color: #222 !important;">
        <div class="container d-flex flex-column flex-md-row justify-content-around align-items-center text-center gap-4 gap-md-0">
            <div>
                <div class="fs-2 fw-bolder oswald-text">50+</div>
                <div class="text-uppercase text-white fw-bold" style="font-size: 0.8rem; letter-spacing: 2px;">Premium Lots</div>
            </div>
            <div class="stat-divider d-none d-md-block"></div>
            <div>
                <div class="fs-2 fw-bolder oswald-text">12k+</div>
                <div class="text-uppercase text-white fw-bold" style="font-size: 0.8rem; letter-spacing: 2px;">Active Drivers</div>
            </div>
            <div class="stat-divider d-none d-md-block"></div>
            <div>
                <div class="fs-2 fw-bolder oswald-text">0</div>
                <div class="text-uppercase text-white fw-bold" style="font-size: 0.8rem; letter-spacing: 2px;">Minutes Wasted Searching</div>
            </div>
        </div>
    </div>
    <section class="bg-black text-white py-5 position-relative">
        <div class="container px-4 py-5 my-md-4">
            <div class="row g-5">
                <div class="col-12 col-md-4 text-center text-md-start pe-md-4 fade-up-element delay-1">
                    <h3 class="fw-bolder oswald-text mb-3 text-uppercase fs-4" style="letter-spacing: 1px;">Precision Tracking</h3>
                    <p class="text-white fw-light" style="font-size: 0.95rem; line-height: 1.6;">
                        Real-time sensors tell you exactly which bays are open before you turn the corner.
                    </p>
                </div>
                <div class="col-12 col-md-4 text-center text-md-start pe-md-4 fade-up-element delay-2" style="border-left: 1px solid #222; padding-left: 2rem;">
                    <h3 class="fw-bolder oswald-text mb-3 text-uppercase fs-4" style="letter-spacing: 1px;">Seamless Payment</h3>
                    <p class="text-white fw-light" style="font-size: 0.95rem; line-height: 1.6;">
                        One-tap checkout through our secure Laravel-powered gateway. No tickets, no hassle.
                    </p>
                </div>
                <div class="col-12 col-md-4 text-center text-md-start pe-md-4 fade-up-element delay-3" style="border-left: 1px solid #222; padding-left: 2rem;">
                    <h3 class="fw-bolder oswald-text mb-3 text-uppercase fs-4" style="letter-spacing: 1px;">VIP Experience</h3>
                    <p class="text-white fw-light" style="font-size: 0.95rem; line-height: 1.6;">
                        Save your favorite spots and get priority access to premium city-level parking.
                    </p>
                </div>
            </div>
        </div>
    </section>
    <footer class="bg-black text-white py-5 border-top" style="border-color: #333 !important;">
        <div class="container px-md-5">
            <div class="row align-items-center gap-4 gap-md-0">
                <div class="col-12 col-md-4 text-center text-md-start">
                    <div class="fw-bolder oswald-text text-white fs-5" style="letter-spacing: 2px;">
                        PARKX &copy; {{ date('Y') }}
                    </div>
                </div>
                
                <div class="col-12 col-md-4 text-center">
                    <div class="text-uppercase fw-bold text-white" style="font-size: 0.85rem; letter-spacing: 1px;">
                        <div class="mb-1">SUPPORT@PARKX.COM</div>
                        <div>+91 1800-PARKX-00</div>
                    </div>
                </div>
                
                <div class="col-12 col-md-4 d-flex justify-content-center justify-content-md-end gap-4 text-uppercase fw-bold" style="font-size: 0.85rem; letter-spacing: 1px;">
                    <a href="#about" class="text-white text-decoration-none nav-link-custom">ABOUT US</a>
                    <a href="#help" class="text-white text-decoration-none nav-link-custom">HELP HUB</a>
                </div>
            </div>
        </div>
    </footer>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target); // Only animate once
                    }
                });
            }, {
                threshold: 0.2 // Triggers when 20% of the element is visible
            });
            const hiddenElements = document.querySelectorAll('.fade-up-element');
            hiddenElements.forEach((el) => observer.observe(el));
        });
    </script>
</body>
</html>
