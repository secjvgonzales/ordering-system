@extends('layouts.main')

@section('title', 'View Item')

@section('content')

    <div class="card shadow-sm">

        <div class="card-header">
            <h3>Item Details</h3>
        </div>

        <div class="card-body">

            <div class="mb-3">
                <strong>ID</strong>
                <p>{{ $item->id }}</p>
            </div>

            <div class="mb-3">
                <strong>Name</strong>
                <p>{{ $item->name }}</p>
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
