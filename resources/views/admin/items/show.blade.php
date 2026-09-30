@extends('layouts.main')

@section('title', 'View Product')

@section('content')

    <div class="card shadow-sm">

        <div class="card-header">
            <h3>Product Details</h3>
        </div>

        <div class="card-body">

            <div class="mb-4">
                @if ($item->image_path && file_exists(public_path($item->image_path)))
                    <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}"
                        class="rounded object-fit-cover" style="width: 220px; height: 165px;">
                @else
                    <div class="rounded bg-light d-flex align-items-center justify-content-center"
                        style="width: 220px; height: 165px;">
                        <i class="bi bi-image display-5 text-muted"></i>
                    </div>
                @endif
            </div>

            <div class="mb-3">
                <strong>ID</strong>
                <p>{{ $item->id }}</p>
            </div>

            <div class="mb-3">
                <strong>Name</strong>
                <p>{{ $item->name }}</p>
            </div>

            <div class="mb-3">
                <strong>Category</strong>
                <p>{{ $item->category ?? 'Uncategorized' }}</p>
            </div>

            <div class="mb-3">
                <strong>Description</strong>
                <p>{{ $item->description ?: 'No description.' }}</p>
            </div>

            <div class="mb-3">
                <strong>Price</strong>
                <p>₱{{ number_format($item->price, 2) }}</p>
            </div>

            <div class="mb-3">
                <strong>Stock Quantity</strong>
                <p>{{ $item->stock_quantity }}</p>
            </div>

            <div class="mb-3">
                <strong>Status</strong>

                <p>
                    @if ($item->status === 'active')
                        <span class="badge bg-success">
                            Active
                        </span>
                    @else
                        <span class="badge bg-secondary">
                            Inactive
                        </span>
                    @endif
                </p>
            </div>

            <a href="{{ route('admin.items.edit', $item) }}" class="btn btn-warning">
                Edit
            </a>

            <a href="{{ route('admin.items.index') }}" class="btn btn-secondary">
                Back
            </a>

        </div>

    </div>

@endsection
