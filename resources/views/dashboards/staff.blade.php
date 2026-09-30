@extends('layouts.main')

@section('title', 'Staff Dashboard')

@section('content')

    <div class="mb-4">

        <h2>Staff Dashboard</h2>

        <p class="text-muted">
            Welcome, {{ auth()->user()->name }}.
        </p>

    </div>

    <div class="row g-4">

        <div class="col-md-6">

            <a href="{{ route('staff.orders.index') }}"
                class="card dashboard-link-card shadow-sm h-100 text-decoration-none text-body">

                <div class="card-body">

                    <h5>
                        <i class="bi bi-receipt"></i>
                        Orders
                    </h5>

                    <p class="text-muted">
                        View and process customer orders.
                    </p>

                </div>

            </a>

        </div>

        <div class="col-md-6">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h5>
                        <i class="bi bi-box-seam"></i>
                        Available Products
                    </h5>

                    <p class="text-muted">
                        View available products and stock.
                    </p>

                </div>

            </div>

        </div>

    </div>

@endsection
