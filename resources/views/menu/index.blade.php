<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Full Menu | Peaks Hotel Cafeteria</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .hero-small {
            background: linear-gradient(rgba(25, 44, 87, 0.9), rgba(25, 44, 87, 0.8)), url('https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-1.2.1&auto=format&fit=crop&w=1074&q=80');
            background-size: cover; padding: 60px 0; color: white; text-align: center;
        }
        .filter-btn {
            border: 2px solid #192C57; color: #192C57; font-weight: bold; padding: 8px 20px; border-radius: 30px; margin: 5px; transition: 0.3s;
        }
        .filter-btn:hover, .filter-btn.active { background-color: #192C57; color: #CEAA0C; }
        
        .menu-card {
            border: none; border-radius: 15px; overflow: hidden; transition: transform 0.3s; background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .menu-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .price-tag {
            background-color: #CEAA0C; color: #192C57; font-weight: bold; padding: 5px 15px; border-radius: 20px;
        }
        .btn-add {
            background-color: #192C57; color: white; border-radius: 50px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; transition: 0.3s; border: none;
        }
        .btn-add:hover { background-color: #CEAA0C; color: #192C57; transform: rotate(90deg); }
        
        /* Toast Notification */
        #toast-box {
            position: fixed; bottom: 30px; right: 30px; z-index: 9999;
            background: #192C57; color: #CEAA0C; padding: 15px 25px; border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3); display: none; animation: slideIn 0.3s;
        }
        @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #192C57;">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}" style="color: #CEAA0C;">
                <i class="fas fa-graduation-cap"></i> KCAU
            </a>
            <div class="d-flex gap-3">
                <a href="{{ route('home') }}" class="text-white text-decoration-none">Home</a>
                <a href="{{ route('menu.all') }}" class="text-white text-decoration-none fw-bold border-bottom border-warning">Menu</a>
                <a href="{{ route('cart.index') }}" class="text-white text-decoration-none position-relative">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="cart-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                        {{ count(session('cart', [])) }}
                    </span>
                </a>
            </div>
        </div>
    </nav>

    <div class="hero-small">
        <h1 class="fw-bold">Our Menu</h1>
        <p class="opacity-75">Fresh, Affordable, Delicious.</p>
    </div>

    <div class="container mt-5">
        <div class="row justify-content-center mb-4">
            <div class="col-md-6">
                <form action="{{ route('menu.all') }}" method="GET" class="input-group">
                    <input type="text" name="search" class="form-control rounded-start-pill ps-4" placeholder="Search for food..." value="{{ request('search') }}">
                    <button class="btn btn-dark rounded-end-pill px-4" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>

        <div class="text-center mb-5">
            <a href="{{ route('menu.all') }}" class="btn filter-btn {{ !request('category') ? 'active' : '' }}">All</a>
            @foreach($categories as $cat)
                <a href="{{ route('menu.all', ['category' => $cat]) }}" class="btn filter-btn {{ request('category') == $cat ? 'active' : '' }}">
                    {{ ucfirst($cat) }}
                </a>
            @endforeach
        </div>

        <div class="row g-4">
            @forelse($menuItems as $item)
            <div class="col-md-3 col-6">
                <div class="card menu-card h-100">
                    <div style="height: 150px; background-color: #eee; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-utensils fa-3x text-muted"></i>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold mb-0 text-dark">{{ $item->name }}</h6>
                            <span class="price-tag small">KES {{ $item->price }}</span>
                        </div>
                        <p class="small text-muted mb-3">{{ Str::limit($item->description, 40) }}</p>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted fw-bold">
    {{ $item->category->name ?? 'General' }}
</small>
                            <button onclick="addToCart({{ $item->id }})" class="btn-add shadow-sm">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No items found matching your search.</h4>
                <a href="{{ route('menu.all') }}" class="btn btn-outline-dark mt-2">Clear Filters</a>
            </div>
            @endforelse
        </div>
    </div>

    <div id="toast-box">
        <i class="fas fa-check-circle me-2"></i> Added to Cart!
    </div>

    @include('layouts.footer')

    <script>
        function addToCart(id) {
            // Prevent reload, use Fetch API
            fetch('/add-to-cart/' + id, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => {
                // Show Toast
                const toast = document.getElementById('toast-box');
                toast.style.display = 'block';
                setTimeout(() => { toast.style.display = 'none'; }, 2000);
                
                // Update Badge (Simulated for speed, refreshes on next load or you can return JSON to be exact)
                const badge = document.getElementById('cart-badge');
                let current = parseInt(badge.innerText);
                badge.innerText = current + 1;
            })
            .catch(error => console.error('Error:', error));
        }
    </script>

</body>
</html>