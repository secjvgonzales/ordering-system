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

            <div class="card shadow-sm">

                <div class="card-body">

                    <h5>
                        <i class="bi bi-shop"></i>
                        Shop
                    </h5>

                    <p class="text-muted">
                        Browse available items.
                    </p>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h5>
                        <i class="bi bi-cart"></i>
                        Cart
                    </h5>

                    <p class="text-muted">
                        Review items before checkout.
                    </p>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h5>
                        <i class="bi bi-clock-history"></i>
                        My Orders
                    </h5>

                    <p class="text-muted">
                        View your order history.
                    </p>

                </div>

            </div>

        </div>

    </div>

@endsection
