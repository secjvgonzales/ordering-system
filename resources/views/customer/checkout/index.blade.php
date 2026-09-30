@extends('layouts.main')

@section('title', 'Checkout')

@section('content')
    <div class="mb-4">
        <span class="text-danger fw-semibold text-uppercase small">Final review</span>
        <h1 class="fw-bold mt-1 mb-1">Checkout</h1>
        <p class="text-muted mb-0">Confirm your details and order items.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form id="checkout-form" method="POST" action="{{ route('customer.checkout.store') }}">
        @csrf

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Customer Information</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="{{ auth()->user()->email }}" disabled>
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label">Notes <span class="text-muted">(optional)</span></label>
                                <textarea id="notes" name="notes" rows="3" maxlength="1000" class="form-control"
                                    placeholder="Add a note for your order">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Payment Method</h5>

                        <div class="form-check border rounded p-3 ps-5 mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment-cod"
                                value="cod" @checked(old('payment_method', 'cod') === 'cod')>
                            <label class="form-check-label w-100" for="payment-cod">
                                <span class="fw-semibold d-block">Cash on Delivery</span>
                                <span class="small text-muted">Pay when your order is delivered.</span>
                            </label>
                        </div>

                        <div class="form-check border border-dark rounded p-3 ps-5 mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment-demo-card"
                                value="demo_card" @checked(old('payment_method') === 'demo_card')>
                            <label class="form-check-label w-100" for="payment-demo-card">
                                <span class="fw-semibold d-block">Demo Card Payment</span>
                                <span class="small text-muted">Reliable presentation payment using local demo test cards.</span>
                            </label>
                        </div>

                        <div class="form-check border rounded p-3 ps-5">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment-maya"
                                value="maya" @checked(old('payment_method') === 'maya')>
                            <label class="form-check-label w-100" for="payment-maya">
                                <span class="fw-semibold d-block">Maya Sandbox (Experimental)</span>
                                <span class="small text-muted">Requires active Maya Sandbox credentials.</span>
                            </label>
                        </div>

                        @error('payment_method')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <a href="{{ route('customer.cart.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Cart
                </a>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Order Summary</h5>

                        @foreach ($cartItems as $cartItem)
                            <div class="d-flex gap-3 py-3 border-bottom">
                                @if ($cartItem['item']->image_path && file_exists(public_path($cartItem['item']->image_path)))
                                    <img src="{{ asset($cartItem['item']->image_path) }}" alt="{{ $cartItem['item']->name }}"
                                        class="rounded object-fit-cover" width="58" height="58">
                                @else
                                    <div class="rounded bg-light d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 58px; height: 58px;">
                                        <i class="bi bi-basket2-fill text-danger"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $cartItem['item']->name }}</div>
                                    <div class="small text-muted">Qty: {{ $cartItem['quantity'] }}</div>
                                    @if ($cartItem['item']->status !== 'active' || $cartItem['quantity'] > $cartItem['item']->stock_quantity)
                                        <div class="small text-danger">Please update this item in your cart.</div>
                                    @endif
                                </div>
                                <div class="fw-semibold">&#8369;{{ number_format($cartItem['subtotal'], 2) }}</div>
                            </div>
                        @endforeach

                        <div class="d-flex justify-content-between fs-5 fw-bold my-4">
                            <span>Total</span>
                            <span>&#8369;{{ number_format($grandTotal, 2) }}</span>
                        </div>

                        <button id="place-order-button" type="button" class="btn btn-threadline w-100"
                            @disabled(! $canCheckout)>
                            Place Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    @if ($canCheckout)
        <script>
            document.getElementById('place-order-button').addEventListener('click', function () {
                Swal.fire({
                    title: 'Place this order?',
                    text: document.getElementById('payment-maya').checked
                        ? 'Your order will be created before you continue to Maya Sandbox.'
                        : document.getElementById('payment-demo-card').checked
                            ? 'Your order will be created before you continue to THREADLINE PAY.'
                            : 'Please confirm that your order details are correct.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#111111',
                    confirmButtonText: 'Yes, place order'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('checkout-form').submit();
                    }
                });
            });
        </script>
    @endif
@endpush
