@extends('layouts.main')

@section('title', 'Customer Dashboard')

@section('content')

    <div class="mb-4">

        <h2>Customer Dashboard</h2>

        <p class="text-muted">
            Welcome, {{ auth()->user()->name }}.
        </p>

    </div>

    <div class="row g-4">

        <div class="col-md-4">

            <a href="{{ route('customer.shop') }}"
                class="card dashboard-link-card shadow-sm h-100 text-decoration-none text-body">

                <div class="card-body">

                    <h5>
                        <i class="bi bi-shop"></i>
                        Shop
                    </h5>

                    <p class="text-muted">
                        Browse the latest THREADLINE apparel.
                    </p>

                </div>

            </a>

        </div>

        <div class="col-md-4">

            <a href="{{ route('customer.cart.index') }}"
                class="card dashboard-link-card shadow-sm h-100 text-decoration-none text-body">

                <div class="card-body">

                    <h5>
                        <i class="bi bi-cart"></i>
                        Cart
                    </h5>

                    <p class="text-muted">
                        Review items before checkout.
                    </p>

                </div>

            </a>

        </div>

        <div class="col-md-4">

            <a href="{{ route('customer.orders.index') }}"
                class="card dashboard-link-card shadow-sm h-100 text-decoration-none text-body">

                <div class="card-body">

                    <h5>
                        <i class="bi bi-clock-history"></i>
                        My Orders
                    </h5>

                    <p class="text-muted">
                        View your order history.
                    </p>

                </div>

            </a>

        </div>

    </div>

@endsection
