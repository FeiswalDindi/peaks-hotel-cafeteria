<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Full Menu | Peaks Hotel Cafeteria</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    
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
                    @php $cartCount = count(session('cart', [])); @endphp
                    @if($cartCount > 0)
                    <span id="cart-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                        {{ $cartCount }}
                    </span>
                    @endif
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
                <div class="card menu-card h-100 position-relative border-0 shadow-sm">
                    
                    <span id="counter-{{ $item->id }}" 
                          class="badge bg-warning text-dark position-absolute top-50 start-50 translate-middle shadow-lg" 
                          style="display:none; transition: opacity 0.5s ease; z-index: 20; font-size: 1.5rem; border-radius: 50px; padding: 10px 20px;">
                    </span>

                    <div style="height: 150px; background-color: #eee; display: flex; align-items: center; justify-content: center;">
                        @if($item->image)
                            <img src="{{ asset('storage/'.$item->image) }}" class="w-100 h-100 object-fit-cover">
                        @else
                            <i class="fas fa-utensils fa-3x text-muted opacity-50"></i>
                        @endif
                    </div>

                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold mb-0 text-dark">{{ $item->name }}</h6>
                            <span class="price-tag small badge bg-light text-dark border">KES {{ number_format($item->price) }}</span>
                        </div>
                        
                        <p class="small text-muted mb-3">{{ Str::limit($item->description, 40) }}</p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <small class="text-muted fw-bold">
                                {{ $item->category->name ?? 'General' }}
                            </small>
                            
                            <button onclick="addToCart({{ $item->id }})" class="btn btn-sm btn-primary rounded-circle shadow-sm" 
                                    style="width: 35px; height: 35px; background-color: #192C57; border: none; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-plus text-white"></i>
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
        let fadeTimers = {};

        function addToCart(id) {
            fetch('/add-to-cart/' + id, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 1. Show Toast
                    const toast = document.getElementById('toast-box');
                    if (toast) {
                        toast.style.display = 'block';
                        setTimeout(() => { toast.style.display = 'none'; }, 2000);
                    }

                    // 2. Update Navbar Badge
                    let badge = document.querySelector('.badge.rounded-pill.bg-danger');
                    if (!badge) { 
                         // Check multiple possible locations for the cart icon
                        const cartLink = document.querySelector('a[href*="cart"]');
                        if(cartLink) {
                            // If user had 0 items, the span might be missing. We create it.
                             cartLink.insertAdjacentHTML('beforeend', 
                                `<span id="cart-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">${data.cart_count}</span>`
                            );
                        }
                    } else { 
                        badge.innerText = data.cart_count; 
                    }

                    // 3. Animate the Card Badge (x1, x2)
                    const itemCounter = document.getElementById('counter-' + id);
                    if (itemCounter) {
                        if (fadeTimers[id]) clearTimeout(fadeTimers[id]);

                        itemCounter.innerText = "x" + data.item_quantity;
                        itemCounter.style.display = 'block';
                        
                        // Small delay to allow 'display:block' to apply before fading
                        setTimeout(() => { itemCounter.style.opacity = '1'; }, 10);

                        fadeTimers[id] = setTimeout(() => {
                            itemCounter.style.opacity = '0';
                            setTimeout(() => { itemCounter.style.display = 'none'; }, 500);
                        }, 1000);
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>