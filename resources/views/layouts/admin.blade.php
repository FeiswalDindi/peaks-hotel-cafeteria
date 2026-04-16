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
            text-decoration: none;
            display: block;
            margin-bottom: 20px;
            text-align: center;
        }
        .sidebar a {
            padding: 12px 25px;
            text-decoration: none;
            font-size: 1.1rem;
            color: #c2c7d0;
            display: block;
            transition: 0.3s;
            border-left: 4px solid transparent;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: rgba(206, 170, 12, 0.1);
            color: #CEAA0C;
            border-left: 4px solid #CEAA0C;
        }
        .main-content {
            margin-left: 260px;
            padding: 30px;
            transition: all 0.3s;
        }
        .header {
            background-color: #fff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Responsive adjustments for Mobile & Tablets */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            .header {
                padding: 15px;
            }
            .table-responsive {
                font-size: 0.875rem;
            }
            .table-responsive .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
            .card-body {
                padding: 1rem !important;
            }
            .card-body.p-3 {
                padding: 1rem !important;
            }
            .card-body.p-md-4 {
                padding: 1rem !important;
            }
            .card-body.p-lg-5 {
                padding: 1rem !important;
            }
        }

        /* Extra small screens */
        @media (max-width: 576px) {
            .main-content {
                padding: 10px;
            }
            .header {
                padding: 10px;
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .table-responsive {
                font-size: 0.8rem;
            }
            .btn-group-sm .btn {
                padding: 0.2rem 0.4rem;
            }
        }
    </style>
</head>
<body>

    <div class="sidebar-overlay d-md-none" id="sidebarOverlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 999;"></div>

    <div class="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <i class="fas fa-graduation-cap me-2"></i> KCAU
        </a>
        <nav class="nav flex-column mt-4">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
            
            <a href="#ordersMenu" data-bs-toggle="collapse" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="fas fa-receipt me-2"></i> Order Management
                <i class="fas fa-chevron-down float-end mt-1" style="font-size: 0.8rem;"></i>
            </a>
            <div class="collapse {{ request()->routeIs('admin.orders.*') ? 'show' : '' }}" id="ordersMenu">
                <a href="{{ route('admin.orders.index') }}" class="ps-5 py-2 {{ request()->routeIs('admin.orders.index') ? 'text-white' : '' }}" style="font-size: 0.95rem;">
                    <i class="fas fa-shopping-bag me-2"></i> General Orders
                </a>
                <a href="{{ route('admin.orders.ledger') }}" class="ps-5 py-2 {{ request()->routeIs('admin.orders.ledger') ? 'text-white' : '' }}" style="font-size: 0.95rem;">
                    <i class="fas fa-wallet me-2"></i> Staff Ledger
                </a>
            </div>

            <a href="#menuManagement" data-bs-toggle="collapse" class="{{ request()->routeIs('admin.menus.*', 'admin.categories.*') ? 'active' : '' }}">
                <i class="fas fa-utensils me-2"></i> Menu Management
                <i class="fas fa-chevron-down float-end mt-1" style="font-size: 0.8rem;"></i>
            </a>
            <div class="collapse {{ request()->routeIs('admin.menus.*', 'admin.categories.*') ? 'show' : '' }}" id="menuManagement">
                <a href="{{ route('admin.categories.index') }}" class="ps-5 py-2 {{ request()->routeIs('admin.categories.*') ? 'text-white' : '' }}" style="font-size: 0.95rem;">
                    <i class="fas fa-tags me-2"></i> Categories
                </a>
                <a href="{{ route('admin.menus.index') }}" class="ps-5 py-2 {{ request()->routeIs('admin.menus.*') ? 'text-white' : '' }}" style="font-size: 0.95rem;">
                    <i class="fas fa-utensils me-2"></i> Menu Items
                </a>
            </div>
            <a href="{{ route('admin.reports.daily') }}" class="{{ request()->routeIs('admin.reports.daily') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar me-2"></i> Daily Financial Report
            </a>
            
            <a href="#staffMenu" data-bs-toggle="collapse" class="{{ request()->routeIs('admin.staff.*', 'admin.departments.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog me-2"></i> Staff Management
                <i class="fas fa-chevron-down float-end mt-1" style="font-size: 0.8rem;"></i>
            </a>
            <div class="collapse {{ request()->routeIs('admin.staff.*', 'admin.departments.*') ? 'show' : '' }}" id="staffMenu">
                <a href="{{ route('admin.staff.index') }}" class="ps-5 py-2 {{ request()->routeIs('admin.staff.index') ? 'text-white' : '' }}" style="font-size: 0.95rem;">
                    <i class="fas fa-user-tie me-2"></i> View Staff
                </a>
                <a href="{{ route('admin.departments.index') }}" class="ps-5 py-2 {{ request()->routeIs('admin.departments.*') ? 'text-white' : '' }}" style="font-size: 0.95rem;">
                    <i class="fas fa-building me-2"></i> Departments
                </a>
                <a href="{{ route('admin.staff.create') }}" class="ps-5 py-2 {{ request()->routeIs('admin.staff.create') ? 'text-white' : '' }}" style="font-size: 0.95rem;">
                    <i class="fas fa-user-plus me-2"></i> Add Staff
                </a>
            </div>

            <a href="{{ route('admin.feedback.index') }}" class="{{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}">
                <i class="fas fa-comments me-2"></i> Reviews & Feedback
            </a>

            <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fas fa-cogs me-2"></i> Settings
            </a>
            
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <a href="#" onclick="event.preventDefault(); this.closest('form').submit();" class="text-danger">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </form>
            
        </nav>
    </div>

    <div class="main-content">
        <div class="header mb-4 rounded-3 shadow-sm">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-light border d-md-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h4 class="mb-0 fw-bold text-dark">@yield('header')</h4>
            </div>
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
        document.body.style.paddingRight = '0px';

        // 3. Mobile Sidebar Toggle Logic
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                sidebarOverlay.style.display = sidebar.classList.contains('active') ? 'block' : 'none';
            });
        }
        
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('active');
                sidebarOverlay.style.display = 'none';
            });
        }
    });
    </script>

    @yield('scripts')
</body>
</html>