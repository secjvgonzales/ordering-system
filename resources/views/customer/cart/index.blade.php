@extends('layouts.main')

@section('title', 'Your Cart')

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-end gap-3 mb-4">
        <div>
            <span class="text-danger fw-semibold text-uppercase small">Your selection</span>
            <h1 class="fw-bold mt-1 mb-1">Your Cart</h1>
            <p class="text-muted mb-0">Review your items before checkout.</p>
        </div>

        @if ($cartItems->isNotEmpty())
            <form id="clear-cart-form" method="POST" action="{{ route('customer.cart.clear') }}">
                @csrf
                @method('DELETE')
                <button id="clear-cart-button" type="button" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash3 me-1"></i> Clear Cart
                </button>
            </form>
        @endif
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    @if ($cartItems->isEmpty())
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <i class="bi bi-cart-x display-4 text-muted"></i>
                <h4 class="fw-bold mt-3">Your cart is empty</h4>
                <p class="text-muted">Add something from the shop to get started.</p>
                <a href="{{ route('customer.shop') }}" class="btn btn-danger">
                    <i class="bi bi-arrow-left me-1"></i> Continue Shopping
                </a>
            </div>
        </div>
    @else
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        @foreach ($cartItems as $cartItem)
                            <div class="d-flex flex-column flex-md-row align-items-md-center gap-3 p-3 p-md-4 border-bottom">
                                @if ($cartItem['item']->image_path && file_exists(public_path($cartItem['item']->image_path)))
                                    <img src="{{ asset($cartItem['item']->image_path) }}" alt="{{ $cartItem['item']->name }}"
                                        class="rounded-3 object-fit-cover flex-shrink-0" width="78" height="78">
                                @else
                                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 78px; height: 78px; background: #fff3e8; color: #dc3545;">
                                        <i class="bi bi-basket2-fill fs-3"></i>
                                    </div>
                                @endif

                                <div class="flex-grow-1">
                                    <h5 class="fw-bold mb-1">{{ $cartItem['item']->name }}</h5>
                                    <div class="text-muted small">&#8369;{{ number_format($cartItem['item']->price, 2) }} each</div>

                                    @if ($cartItem['item']->status !== 'active' || $cartItem['item']->stock_quantity < 1)
                                        <span class="badge text-bg-danger mt-2">Currently unavailable</span>
                                    @elseif ($cartItem['quantity'] > $cartItem['item']->stock_quantity)
                                        <span class="badge text-bg-warning mt-2">
                                            Only {{ $cartItem['item']->stock_quantity }} in stock
                                        </span>
                                    @endif
                                </div>

                                <form method="POST" action="{{ route('customer.cart.update', $cartItem['item']) }}"
                                    class="d-flex align-items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <label class="visually-hidden" for="quantity-{{ $cartItem['item']->id }}">Quantity</label>
                                    <input id="quantity-{{ $cartItem['item']->id }}" type="number" name="quantity"
                                        value="{{ $cartItem['quantity'] }}" min="1"
                                        max="{{ $cartItem['item']->stock_quantity }}" class="form-control form-control-sm"
                                        style="width: 75px;" @disabled($cartItem['item']->status !== 'active' || $cartItem['item']->stock_quantity < 1)>
                                    <button type="submit" class="btn btn-outline-secondary btn-sm"
                                        @disabled($cartItem['item']->status !== 'active' || $cartItem['item']->stock_quantity < 1)>
                                        Update
                                    </button>
                                </form>

                                <div class="text-md-end" style="min-width: 100px;">
                                    <div class="fw-bold">&#8369;{{ number_format($cartItem['subtotal'], 2) }}</div>
                                    <form method="POST" action="{{ route('customer.cart.destroy', $cartItem['item']) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link link-danger btn-sm p-0 mt-1">Remove</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Order Summary</h5>
                        <div class="d-flex justify-content-between text-muted mb-3">
                            <span>Subtotal</span>
                            <span>&#8369;{{ number_format($grandTotal, 2) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fs-5 fw-bold mb-4">
                            <span>Total</span>
                            <span>&#8369;{{ number_format($grandTotal, 2) }}</span>
                        </div>
                        <a href="{{ route('customer.checkout.index') }}" class="btn btn-danger w-100 mb-2">
                            Proceed to Checkout
                        </a>
                        <a href="{{ route('customer.shop') }}" class="btn btn-outline-secondary w-100">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    @if ($cartItems->isNotEmpty())
        <script>
            document.getElementById('clear-cart-button').addEventListener('click', function () {
                Swal.fire({
                    title: 'Clear your cart?',
                    text: 'All items will be removed from your cart.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Yes, clear cart'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('clear-cart-form').submit();
                    }
                });
            });
        </script>
    @endif
@endpush
