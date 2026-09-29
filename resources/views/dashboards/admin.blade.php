@extends('layouts.main')

@section('title', 'Admin Dashboard')

@section('content')

    <div class="mb-4">

        <h2>Admin Dashboard</h2>

        <p class="text-muted">
            Welcome, {{ auth()->user()->name }}.
        </p>

    </div>

    <div class="row g-4">

        <div class="col-md-4">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h5>
                        <i class="bi bi-box-seam"></i>
                        Items
                    </h5>

                    <p class="text-muted">
                        Manage products, inventory, prices, and availability.
                    </p>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h5>
                        <i class="bi bi-receipt"></i>
                        Orders
                    </h5>

                    <p class="text-muted">
                        Review and process customer orders.
                    </p>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h5>
                        <i class="bi bi-people"></i>
                        Users
                    </h5>

                    <p class="text-muted">
                        Manage staff and customer accounts.
                    </p>

                </div>

            </div>

        </div>

    </div>

@endsection
