@extends('layouts.main')

@section('title', 'Shop')

@section('content')

    <div class="mb-4">
        <h2>Shop</h2>
        <p class="text-muted">
            Browse available items.
        </p>
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

                        <p class="mb-0">
                            Stock:
                            <strong>{{ $item->stock_quantity }}</strong>
                        </p>

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
