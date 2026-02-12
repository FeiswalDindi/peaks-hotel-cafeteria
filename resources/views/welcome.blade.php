<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peaks Hotel | KCA University</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <style>
        html { scroll-behavior: smooth; }
        body { font-family: 'Figtree', sans-serif; background-color: #f8f9fa; }
        
        /* Enlarged & Animated Hero */
        .hero-section { 
            background-color: #192C57; 
            color: white; 
            padding: 160px 0; 
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        /* The White-to-Gold Button */
        .btn-explore {
            background-color: white;
            color: #192C57;
            font-weight: 800;
            padding: 15px 40px;
            border-radius: 50px;
            border: 2px solid white;
            transition: 0.4s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            z-index: 10;
        }

        .btn-explore:hover {
            background-color: #CEAA0C;
            border-color: #CEAA0C;
            color: #192C57;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(206, 170, 12, 0.4);
        }

        /* Text Animation */
        #flipping-text {
            color: #CEAA0C;
            font-weight: 800;
            display: inline-block;
            min-width: 180px;
            transition: opacity 0.5s ease;
        }

        .food-card { transition: transform 0.3s ease, box-shadow 0.3s ease; border: none; border-radius: 15px; overflow: hidden; }
        .food-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .price-tag { color: #192C57; font-weight: 800; font-size: 1.25rem; }
        .btn-add { background-color: #CEAA0C; color: #192C57; font-weight: bold; border: none; transition: 0.3s; }
        .btn-add:hover { background-color: #192C57; color: white; }
        .badge-stock { position: absolute; top: 15px; right: 15px; z-index: 5; padding: 8px 12px; border-radius: 50px; }

        /* Floating Icons Background */
        .floating-icons {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .floating-icons i {
            position: absolute;
            color: rgba(255, 255, 255, 0.1);
            font-size: 2.5rem;
            animation: floatAround 12s linear infinite;
            opacity: 0;
        }

        /* Positions */
        .icon-1 { top: 15%; left: 10%; animation-delay: 0s; }
        .icon-2 { top: 60%; left: 20%; animation-delay: 2s; }
        .icon-3 { top: 25%; left: 80%; animation-delay: 4s; }
        .icon-4 { top: 75%; left: 70%; animation-delay: 6s; }
        .icon-5 { top: 45%; left: 45%; animation-delay: 1s; }

        @keyframes floatAround {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            20% { opacity: 0.6; }
            80% { opacity: 0.6; }
            100% { transform: translateY(-100px) rotate(360deg); opacity: 0; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}" style="color: #192C57; font-size: 1.5rem;">
            <i class="fas fa-graduation-cap" style="color: #CEAA0C;"></i> KCA<span style="color: #CEAA0C;">U</span>
        </a>
        
        <div class="d-flex align-items-center gap-3">
            
            <a href="{{ route('menu.all') }}" class="text-decoration-none fw-bold me-2" style="color: #192C57;">
                 MENU
            </a>

            <a href="{{ route('cart.index') }}" class="btn btn-light position-relative rounded-pill px-3 border shadow-sm">
                <i class="fas fa-shopping-cart" style="color: #192C57;"></i>
                @php $cartCount = count(session('cart', [])); @endphp
                @if($cartCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $cartCount }}
                </span>
                @endif
            </a>

            @if(Auth::check())
                @if(isset($showWallet) && $showWallet)
                <div class="d-none d-md-flex align-items-center bg-light px-3 py-2 rounded-pill border border-primary">
                    <i class="fas fa-wallet text-primary me-2"></i>
                    <span class="fw-bold text-navy">KES {{ number_format($walletBalance, 2) }}</span>
                </div>
                @endif
                
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-outline-danger rounded-pill px-4 fw-bold">
                    LOGOUT
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" style="background-color: #192C57;">
                    LOGIN
                </a>
            @endif
        </div>
    </div>
</nav>

<div class="hero-section text-center">
    <div class="floating-icons">
        <i class="fas fa-hamburger icon-1"></i>
        <i class="fas fa-pizza-slice icon-2"></i>
        <i class="fas fa-ice-cream icon-3"></i>
        <i class="fas fa-coffee icon-4"></i>
        <i class="fas fa-utensils icon-5"></i>
    </div>

    <div class="container" style="position: relative; z-index: 1;">
        <h1 class="display-2 fw-bold mb-3">Peaks Hotel Cafeteria</h1>
        <p class="h3 mb-5 fw-light">
            Quality, Affordable Meals for <span id="flipping-text">Everyone</span>
        </p>
        <a href="#menu-start" class="btn btn-explore shadow-lg btn-lg">
            View Today's Menu <i class="fas fa-arrow-down ms-2"></i>
        </a>
    </div>
</div>

<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="https://students.kca.ac.ke/assets/images/kca-logo.png" alt="KCA Logo" class="img-fluid mb-3" style="max-width: 150px;">
                <h2 class="fw-bold mb-3" style="color: #192C57;">Excellence in Every Meal</h2>
                <div style="width: 60px; height: 4px; background: #CEAA0C; margin-bottom: 20px;"></div>
                <p class="text-muted lead">
                    The Peaks Hotel Cafeteria is dedicated to serving the students, staff, and visitors of KCA University. 
                    We believe that a good meal fuels the mind for academic excellence.
                </p>
                <p class="text-muted">
                    <strong>Note to Staff:</strong> You can login via the Staff Portal to access your daily meal allowances. 
                    Students and visitors can order directly using M-Pesa without logging in.
                </p>
                <a href="{{ route('login') }}" class="btn btn-outline-dark rounded-pill px-4 mt-3">
                    <i class="fas fa-user-lock me-2"></i> Staff Portal Login
                </a>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-4 rounded-4 text-center shadow-sm" style="background-color: #f8f9fa;">
                            <i class="fas fa-leaf fa-2x mb-3 text-success"></i>
                            <h6 class="fw-bold">Fresh Ingredients</h6>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-4 rounded-4 text-center shadow-sm" style="background-color: #f8f9fa;">
                            <i class="fas fa-wallet fa-2x mb-3 text-primary"></i>
                            <h6 class="fw-bold">Affordable Prices</h6>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-4 rounded-4 text-center shadow-sm" style="background-color: #f8f9fa;">
                            <i class="fas fa-mobile-alt fa-2x mb-3 text-warning"></i>
                            <h6 class="fw-bold">M-Pesa Integration</h6>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-4 rounded-4 text-center shadow-sm" style="background-color: #f8f9fa;">
                            <i class="fas fa-users fa-2x mb-3 text-danger"></i>
                            <h6 class="fw-bold">Community Focused</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<div class="container my-5" id="menu-start">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="fw-bold kca-navy">Today's Top Picks</h2>
                <div style="width: 50px; height: 3px; background: #CEAA0C;"></div>
            </div>
            <a href="{{ route('menu.all') }}" class="btn btn-outline-dark rounded-pill px-4">
                View Full Menu <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>

        <div class="row g-4">
            @foreach($featuredItems as $item)
            <div class="col-md-3 col-6">
                <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                    <div style="height: 140px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-hamburger fa-3x text-muted opacity-50"></i>
                    </div>
                    
                    <div class="card-body">
                        <h6 class="fw-bold mb-1">{{ $item->name }}</h6>
                        <p class="text-muted small mb-2">{{ Str::limit($item->description, 30) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-primary fw-bold">KES {{ $item->price }}</span>
                            <button onclick="addToCart({{ $item->id }})" class="btn btn-sm btn-dark rounded-circle" style="width: 35px; height: 35px;">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-5">
            <a href="{{ route('menu.all') }}" class="btn btn-primary px-5 py-3 rounded-pill fw-bold shadow" style="background-color: #192C57;">
                EXPLORE ALL CATEGORIES
            </a>
        </div>
    </div>

    <div id="toast-box" style="display:none; position: fixed; bottom: 30px; right: 30px; z-index: 9999; background: #192C57; color: #CEAA0C; padding: 15px 25px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); animation: slideIn 0.3s;">
        <i class="fas fa-check-circle me-2"></i> Added to Cart!
    </div>

    <script>
        function addToCart(id) {
            fetch('/add-to-cart/' + id).then(() => {
                document.getElementById('toast-box').style.display = 'block';
                setTimeout(() => { document.getElementById('toast-box').style.display = 'none'; }, 2000);
            });
        }
    </script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const phrases = ["Everyone", "Staff", "Students", "Visitors", "Parents"];
    let i = 0;
    const el = document.getElementById('flipping-text');

    setInterval(() => {
        el.style.opacity = 0;
        setTimeout(() => {
            i = (i + 1) % phrases.length;
            el.innerText = phrases[i];
            el.style.opacity = 1;
        }, 500);
    }, 2500);

    @if(session('status') == 'logged-out')
        Swal.fire({
            title: 'Logged Out!',
            text: 'Come back soon!',
            icon: 'success',
            confirmButtonColor: '#192C57'
        });
    @endif
</script>
@include('layouts.footer')
</body>
</html>