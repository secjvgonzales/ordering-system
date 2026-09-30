@extends('layouts.main')

@section('title', $item->name.' | THREADLINE')

@section('content')
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('shop.index') }}" class="text-dark">Shop</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $item->name }}</li>
        </ol>
    </nav>

    <div class="row g-4 g-lg-5 align-items-start">
        <div class="col-lg-7">
            @if ($item->image_path && file_exists(public_path($item->image_path)))
                <img src="{{ asset($item->image_path) }}" class="w-100 object-fit-cover bg-light"
                    style="min-height: 420px; max-height: 720px;" alt="{{ $item->name }}">
            @else
                <div class="product-placeholder d-flex align-items-center justify-content-center" style="min-height: 520px;">
                    <i class="bi bi-image display-2"></i>
                </div>
            @endif
        </div>

        <div class="col-lg-5">
            <div class="position-lg-sticky" style="top: 100px;">
                <span class="small text-uppercase text-muted" style="letter-spacing: .16em;">THREADLINE Essential</span>
                <h1 class="display-5 fw-bold mt-2">{{ $item->name }}</h1>
                <div class="fs-4 mb-3">&#8369;{{ number_format($item->price, 2) }}</div>

                @if ($item->stock_quantity > 0)
                    <p class="small text-success"><i class="bi bi-check-circle me-1"></i> In stock, {{ $item->stock_quantity }} available</p>
                @else
                    <p class="small text-danger"><i class="bi bi-x-circle me-1"></i> Out of stock</p>
                @endif

                <p class="text-muted py-3 border-top border-bottom">{{ $item->description ?: 'A THREADLINE everyday essential.' }}</p>

                @if (auth()->check() && auth()->user()->role === 'customer')
                    <form method="POST" action="{{ route('customer.cart.store', $item) }}" class="mt-4">
                        @csrf
                        <label for="quantity" class="form-label small text-uppercase fw-semibold">Quantity</label>
                        <div class="d-flex gap-2">
                            <input id="quantity" type="number" name="quantity" value="1" min="1"
                                max="{{ $item->stock_quantity }}" class="form-control" style="max-width: 90px;">
                            <button type="submit" class="btn btn-threadline btn-lg flex-grow-1" @disabled($item->stock_quantity <= 0)>
                                Add to Cart
                            </button>
                        </div>
                    </form>
                @elseif (auth()->guest())
                    <form method="POST" action="{{ route('products.cart.guest', $item) }}" class="mt-4">
                        @csrf
                        <label for="quantity" class="form-label small text-uppercase fw-semibold">Quantity</label>
                        <div class="d-flex gap-2">
                            <input id="quantity" type="number" name="quantity" value="1" min="1"
                                max="{{ $item->stock_quantity }}" class="form-control" style="max-width: 90px;">
                            <button type="submit" class="btn btn-threadline btn-lg flex-grow-1" @disabled($item->stock_quantity <= 0)>
                                Add to Cart
                            </button>
                        </div>
                    </form>
                    <p class="small text-muted mt-2">An account is required to add products to your cart.</p>
                @else
                    <div class="alert alert-light border mt-4 mb-0">Customer accounts can purchase products from this store.</div>
                @endif
            </div>
        </div>
    </div>
@endsection
