<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'THREADLINE')
    </title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .product-card {
            border: 0;
            border-radius: 0;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.08) !important;
        }

        .product-placeholder {
            min-height: 340px;
            background: #f1f1ef;
            color: #777;
        }

        .product-image {
            width: 100%;
            height: 340px;
            object-fit: cover;
        }

        .dashboard-link-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .dashboard-link-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        }

        .brand-wordmark {
            letter-spacing: 0.18em;
        }

        .btn-threadline {
            --bs-btn-color: #fff;
            --bs-btn-bg: #111;
            --bs-btn-border-color: #111;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #333;
            --bs-btn-hover-border-color: #333;
        }
    </style>
</head>

<body class="bg-white">

    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">

        <div class="container">

            <a class="navbar-brand fw-bold brand-wordmark" href="{{ auth()->check() ? route('dashboard') : route('shop.index') }}">
                <i class="bi bi-bag"></i>
                THREADLINE
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-3"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">

                <ul class="navbar-nav me-auto">

                    @guest
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('shop.index') ? 'active' : '' }}"
                                href="{{ route('shop.index') }}">
                                Shop
                            </a>
                        </li>
                    @endguest

                    @auth

                        @if (auth()->user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-speedometer2"></i>
                                    Dashboard
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.items.index') }}">
                                    <i class="bi bi-box-seam"></i>
                                    Products
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
                                    href="{{ route('admin.orders.index') }}">
                                    <i class="bi bi-receipt"></i>
                                    Orders
                                </a>
                            </li>
                        @elseif (auth()->user()->role === 'staff')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('staff.dashboard') }}">
                                    <i class="bi bi-speedometer2"></i>
                                    Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('staff.orders.*') ? 'active' : '' }}"
                                    href="{{ route('staff.orders.index') }}">
                                    <i class="bi bi-receipt"></i>
                                    Orders
                                </a>
                            </li>
                        @elseif (auth()->user()->role === 'customer')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('customer.dashboard') }}">
                                    <i class="bi bi-speedometer2"></i>
                                    Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('customer.shop') ? 'active' : '' }}"
                                    href="{{ route('customer.shop') }}">
                                    <i class="bi bi-shop"></i>
                                    Shop
                                </a>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link {{ request()->routeIs('customer.cart.*') ? 'active' : '' }}"
                                    type="button" data-bs-toggle="offcanvas" data-bs-target="#customerCartOffcanvas"
                                    aria-controls="customerCartOffcanvas">
                                    <i class="bi bi-cart3"></i>
                                    Cart
                                    @if (array_sum(session('cart', [])) > 0)
                                        <span class="badge rounded-pill bg-danger">{{ array_sum(session('cart', [])) }}</span>
                                    @endif
                                </button>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('customer.orders.*') ? 'active' : '' }}"
                                    href="{{ route('customer.orders.index') }}">
                                    <i class="bi bi-receipt"></i>
                                    My Orders
                                </a>
                            </li>
                        @endif

                    @endauth

                </ul>

                @guest
                    <div class="d-flex gap-2">
                        <a href="{{ route('login') }}" class="btn btn-outline-dark btn-sm">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-threadline btn-sm">Sign Up</a>
                    </div>
                @endguest

                @auth

                    <span class="navbar-text me-3">

                        <i class="bi bi-person-circle"></i>

                        {{ auth()->user()->name }}

                        <span class="badge bg-primary">
                            {{ ucfirst(auth()->user()->role) }}
                        </span>

                    </span>

                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-dark btn-sm me-2">
                        <i class="bi bi-person"></i>
                        Profile
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="d-inline">

                        @csrf

                        <button type="submit" class="btn btn-dark btn-sm">
                            <i class="bi bi-box-arrow-right"></i>
                            Logout
                        </button>

                    </form>

                @endauth

            </div>

        </div>

    </nav>

    @auth
        @if (auth()->user()->role === 'customer')
            @include('customer.cart.offcanvas')
        @endif
    @endauth

    <main class="container py-4">

        @yield('content')

    </main>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: @json(session('success')),
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: @json(session('error'))
            });
        </script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @if (session('open_cart_offcanvas'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const cart = document.getElementById('customerCartOffcanvas');

                if (cart) {
                    bootstrap.Offcanvas.getOrCreateInstance(cart).show();
                }
            });
        </script>
    @endif

    @stack('scripts')

</body>

</html>
