<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - KCA University</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar {
            height: 100vh;
            width: 260px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #192C57; /* KCA Navy */
            color: #fff;
            padding-top: 20px;
            z-index: 1000;
            transition: all 0.3s;
        }
        .sidebar-brand {
            padding: 15px 25px;
            font-size: 1.5rem;
            font-weight: bold;
            color: #CEAA0C; /* KCA Gold */
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 25px;
            font-size: 1rem;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }
        .nav-link:hover, .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            color: #fff;
            border-left-color: #CEAA0C;
        }
        .main-content {
            margin-left: 260px;
            padding: 30px;
            transition: all 0.3s;
        }
        .header {
            background: #fff;
            padding: 15px 30px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Emergency fix to prevent the "Dark Ghost" screen */


        /* FORCE HIDE ALL OVERLAYS AND UNLOCK SCREEN */
.modal-backdrop {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
}

body {
    overflow: auto !important;
    padding-right: 0 !important;
    pointer-events: auto !important;
}

/* This targets the specific "frozen" state seen in your screenshot */
.modal-open {
    overflow: auto !important;
}


    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-graduation-cap"></i> KCAU
        </div>
        
        <nav class="nav flex-column mt-4">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home me-2" style="width: 25px;"></i> Dashboard
            </a>

            <a href="{{ route('admin.menus.index') }}" class="nav-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                <i class="fas fa-utensils me-2" style="width: 25px;"></i> Menu Items
            </a>

<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#staffSubmenu" aria-expanded="false">
        <div class="d-flex align-items-center">
            <i class="fas fa-users-cog me-2"></i>
            <span>Staff Management</span>
            <i class="fas fa-chevron-down ms-auto" style="font-size: 0.8rem;"></i>
        </div>
    </a>
    
    <div class="collapse" id="staffSubmenu" style="background: rgba(0,0,0,0.1);">
        <ul class="nav flex-column ps-3">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}" 
                   href="{{ route('admin.departments.index') }}">
                    <i class="fas fa-building me-2"></i> Departments
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}" 
                   href="{{ route('admin.staff.index') }}">
                    <i class="fas fa-id-card me-2"></i> Staff Directory
                </a>
            </li>
        </ul>
    </div>
</li>

            <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fas fa-sliders-h me-2" style="width: 25px;"></i> Settings
            </a>

            <a href="{{ route('admin.reports.daily') }}" class="nav-link">
    <i class="fas fa-file-invoice-dollar me-2" style="width: 25px;"></i> Daily Staff Financial Report
</a>

<form method="POST" action="{{ route('logout') }}" id="admin-logout-form" class="mt-5">
    @csrf
    <a href="#" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();" 
       class="nav-link w-100 text-start bg-transparent border-0 text-danger" 
       style="cursor: pointer;">
        <i class="fas fa-sign-out-alt me-2" style="width: 25px;"></i> Logout
    </a>
</form>
        </nav>
    </div>

    <div class="main-content">
        <div class="header mb-4 rounded-3 shadow-sm">
            <h4 class="mb-0 fw-bold text-dark">@yield('header')</h4>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-light text-dark border">{{ Auth::user()->name ?? 'Admin' }}</span>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Remove the dark background if it's stuck
        const backdrops = document.querySelectorAll('.modal-backdrop');
        if (backdrops.length > 0) {
            backdrops.forEach(backdrop => backdrop.remove());
            console.log("Stuck backdrop removed!");
        }

        // 2. Unlock the scrollbar
        document.body.classList.remove('modal-open');
        document.body.style.overflow = 'auto'; 
    });
</script>
</body>
</html>