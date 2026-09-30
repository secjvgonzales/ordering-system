@extends('layouts.main')

@section('title', 'Order Receipt | THREADLINE')

@section('content')
    @php
        $subtotal = $order->orderItems->sum(fn ($item) => (float) $item->subtotal);
        $paymentReference = $order->payment_reference ?? $order->maya_reference;
    @endphp

    <div class="receipt-wrap mx-auto" style="max-width: 900px;">
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Order Details
            </a>
            <button type="button" class="btn btn-dark" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Print Receipt
            </button>
        </div>

        <div class="card receipt-card border shadow-sm">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-sm-row justify-content-between gap-3 pb-4 border-bottom">
                    <div>
                        <div class="brand-wordmark fw-bold fs-3">THREADLINE</div>
                        <div class="text-uppercase text-muted small mt-1" style="letter-spacing: .14em;">Order Receipt</div>
                    </div>
                    <div class="text-sm-end">
                        <div class="text-muted small">Receipt / Invoice Number</div>
                        <div class="fw-bold">INV-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </div>

                <div class="row g-4 py-4 border-bottom">
                    <div class="col-sm-6">
                        <div class="text-muted small">Order Number</div>
                        <div class="fw-semibold">{{ $order->order_number }}</div>
                        <div class="text-muted small mt-3">Order Date</div>
                        <div>{{ $order->created_at->format('M d, Y g:i A') }}</div>
                        <div class="text-muted small mt-3">Order Status</div>
                        <div class="fw-semibold">{{ ucfirst($order->status) }}</div>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <div class="text-muted small">Customer</div>
                        <div class="fw-semibold">{{ $order->customer->name }}</div>
                        <div>{{ $order->customer->email }}</div>
                        <div class="text-muted small mt-3">Payment Method</div>
                        <div>{{ match ($order->payment_method) {
                            'demo_card' => 'Demo Card Payment',
                            'maya' => 'Maya',
                            'cod' => 'Cash on Delivery',
                            default => 'Not recorded',
                        } }}</div>
                        <div class="text-muted small mt-3">Payment Status</div>
                        <div class="fw-semibold text-success">Paid</div>
                        @if ($paymentReference)
                            <div class="text-muted small mt-3">Payment Reference</div>
                            <div class="fw-semibold">{{ $paymentReference }}</div>
                        @endif
                        <div class="text-muted small mt-3">Paid At</div>
                        <div>{{ $order->paid_at?->format('M d, Y g:i A') }}</div>
                    </div>
                </div>

                <div class="table-responsive my-4">
                    <table class="table table-bordered align-middle receipt-table">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderItems as $orderItem)
                                <tr>
                                    <td>{{ $orderItem->item?->name ?? 'Product unavailable' }}</td>
                                    <td class="text-end">&#8369;{{ number_format($orderItem->unit_price, 2) }}</td>
                                    <td class="text-center">{{ $orderItem->quantity }}</td>
                                    <td class="text-end">&#8369;{{ number_format($orderItem->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="ms-auto" style="max-width: 360px;">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>Subtotal</span>
                        <span>&#8369;{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between pt-3 fs-5 fw-bold">
                        <span>Total Paid</span>
                        <span>&#8369;{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>

                <p class="text-center text-muted small mt-5 mb-0">Thank you for shopping with THREADLINE.</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <style>
        @media print {
            nav,
            .offcanvas,
            .no-print,
            .swal2-container {
                display: none !important;
            }

            body,
            main {
                background: #fff !important;
            }

            main.container {
                width: 100% !important;
                max-width: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .receipt-wrap {
                max-width: none !important;
            }

            .receipt-card {
                border: 0 !important;
                box-shadow: none !important;
            }

            .receipt-table,
            .receipt-table th,
            .receipt-table td {
                border-color: #777 !important;
            }
        }
    </style>
@endpush
