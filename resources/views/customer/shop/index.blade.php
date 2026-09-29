@extends('layouts.main')

@section('title', 'Customer Shop')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2>Shop</h2>

            <p class="text-muted mb-0">
                Browse available items and add them to your cart.
            </p>
        </div>

    </div>

    <div class="row g-4">

        @forelse ($items as $item)
            <div class="col-md-4">

                <div class="card h-100 shadow-sm">

                    <div class="card-body text-center">

                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mx-auto mb-3"
                            style="width: 100px; height: 100px; font-size: 36px;">
                            {{ strtoupper(substr($item->name, 0, 1)) }}
                        </div>

                        <h5 class="card-title">
                            {{ $item->name }}
                        </h5>

                        <p class="text-muted">
                            {{ $item->description ?: 'No description available.' }}
                        </p>

                        <h5 class="text-success">
                            ₱{{ number_format($item->price, 2) }}
                        </h5>

                        <p>
                            Stock:
                            <strong>{{ $item->stock_quantity }}</strong>
                        </p>

                        <button type="button" class="btn btn-primary" disabled>
                            <i class="bi bi-cart-plus"></i>
                            Add to Cart
                        </button>

                    </div>

                </div>

            </div>

        @empty

            <div class="col-12">

                <div class="alert alert-info">
                    No items are currently available.
                </div>

            </div>
        @endforelse

    </div>

@endsection
