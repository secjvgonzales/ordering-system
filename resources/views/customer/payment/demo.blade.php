@extends('layouts.main')

@section('title', 'THREADLINE PAY')

@section('content')
    <div class="mx-auto" style="max-width: 980px;">
        <div class="text-center mb-4">
            <div class="fw-bold brand-wordmark fs-4">THREADLINE PAY</div>
            <span class="badge text-bg-warning mt-2">DEMO PAYMENT - No real charge will be made.</span>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <div class="border border-dark rounded p-4 mb-5 bg-light">
                            <div class="small fw-bold text-uppercase mb-1" style="letter-spacing: .12em;">Presentation Demo</div>
                            <p class="text-muted small mb-3">Use these buttons to simulate the payment gateway response. No real payment is processed.</p>

                            <div class="d-grid gap-2">
                                <form method="POST" action="{{ route('customer.demo-payment.process', $order) }}">
                                    @csrf
                                    <input type="hidden" name="demo_result" value="success">
                                    <button type="submit" class="btn btn-success btn-lg w-100">
                                        <i class="bi bi-check-circle me-1"></i> Simulate Successful Payment
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('customer.demo-payment.process', $order) }}">
                                    @csrf
                                    <input type="hidden" name="demo_result" value="failure">
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        Simulate Failed Payment
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <div class="small text-uppercase text-muted">Optional manual simulator</div>
                                <h1 class="h3 fw-bold mb-0">Card details</h1>
                            </div>
                            <i class="bi bi-shield-lock fs-2"></i>
                        </div>

                        <form method="POST" action="{{ route('customer.demo-payment.process', $order) }}" autocomplete="off">
                            @csrf

                            <div class="mb-3">
                                <label for="cardholder_name" class="form-label">Cardholder Name</label>
                                <input id="cardholder_name" type="text" name="cardholder_name" class="form-control form-control-lg"
                                    placeholder="Name on card" required autocomplete="off">
                            </div>

                            <div class="mb-3">
                                <label for="card_number" class="form-label">Card Number</label>
                                <input id="card_number" type="text" name="card_number" class="form-control form-control-lg"
                                    inputmode="numeric" maxlength="19" placeholder="0000 0000 0000 0000" required autocomplete="off">
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-7">
                                    <label for="expiry" class="form-label">Expiry</label>
                                    <input id="expiry" type="text" name="expiry" class="form-control form-control-lg"
                                        inputmode="numeric" maxlength="5" placeholder="MM/YY" required autocomplete="off">
                                </div>
                                <div class="col-5">
                                    <label for="cvv" class="form-label">CVV</label>
                                    <input id="cvv" type="password" name="cvv" class="form-control form-control-lg"
                                        inputmode="numeric" maxlength="3" placeholder="123" required autocomplete="off">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-threadline btn-lg w-100">
                                <i class="bi bi-lock me-1"></i>
                                Pay &#8369;{{ number_format($order->total_amount, 2) }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold">Payment Summary</h2>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Order</span>
                            <span class="fw-semibold">{{ $order->order_number }}</span>
                        </div>
                        <div class="py-3 border-bottom">
                            <div class="text-muted small">Customer</div>
                            <div class="fw-semibold">{{ $order->customer->name }}</div>
                            <div class="small text-muted">{{ $order->customer->email }}</div>
                        </div>
                        <div class="d-flex justify-content-between pt-3 fs-5 fw-bold">
                            <span>Amount</span>
                            <span>&#8369;{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="card border-secondary bg-light">
                    <div class="card-body p-4">
                        <h2 class="h6 fw-bold"><i class="bi bi-credit-card me-1"></i> Optional Manual Simulator</h2>
                        <p class="small mb-0 text-muted">Enter any sample card information. No real card data is processed or stored.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
