@extends('layouts.main')

@section('title', 'Manage Order')

@section('content')
    @php
        $nextStatus = match ($order->status) {
            'pending' => 'confirmed',
            'confirmed' => 'processing',
            'processing' => 'completed',
            default => null,
        };
        $statusClass = match ($order->status) {
            'completed' => 'success',
            'cancelled' => 'danger',
            'processing' => 'primary',
            'confirmed' => 'info',
            default => 'warning',
        };
        $paymentStatusClass = match ($order->payment_status) {
            'paid' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            'cancelled' => 'dark',
            default => 'secondary',
        };
    @endphp

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div>
            <a href="{{ route($routePrefix.'.orders.index') }}" class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left"></i> Orders
            </a>
            <h1 class="fw-bold mt-2 mb-1">Order Details</h1>
            <p class="text-muted mb-0">{{ $order->order_number }}</p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="badge text-bg-{{ $statusClass }} px-3 py-2">{{ ucfirst($order->status) }}</span>

            @if ($nextStatus)
                <form method="POST" action="{{ route($routePrefix.'.orders.status', $order) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ $nextStatus }}">
                    <button type="submit" class="btn btn-dark btn-sm">
                        Mark as {{ ucfirst($nextStatus) }}
                    </button>
                </form>
            @endif

            @if (in_array($order->status, ['pending', 'confirmed', 'processing'], true))
                <form id="cancel-order-form" method="POST" action="{{ route($routePrefix.'.orders.cancel', $order) }}">
                    @csrf
                    @method('PATCH')
                    <button id="cancel-order-button" type="button" class="btn btn-outline-danger btn-sm">
                        Cancel Order
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between gap-3">
            <div>
                <div class="text-muted small">Payment Method</div>
                <div class="fw-semibold">
                    {{ match ($order->payment_method) {
                        'demo_card' => 'Demo Card Payment',
                        'maya' => 'Maya',
                        'cod' => 'Cash on Delivery',
                        default => 'Not recorded',
                    } }}
                </div>
            </div>
            <div>
                <div class="text-muted small">Payment Status</div>
                <span class="badge text-bg-{{ $paymentStatusClass }}">{{ ucfirst($order->payment_status) }}</span>
            </div>
            @if ($order->payment_reference ?? $order->maya_reference)
                <div>
                    <div class="text-muted small">Payment Reference</div>
                    <div class="fw-semibold">{{ $order->payment_reference ?? $order->maya_reference }}</div>
                </div>
            @endif
            @if ($order->paid_at)
                <div>
                    <div class="text-muted small">Paid At</div>
                    <div class="fw-semibold">{{ $order->paid_at->format('M d, Y g:i A') }}</div>
                </div>
            @endif
            @if ($order->payment_method === 'cod'
                && $order->payment_status === 'unpaid'
                && $order->status !== 'cancelled')
                <form id="mark-cod-paid-form" method="POST" action="{{ route($routePrefix.'.orders.payment', $order) }}"
                    class="align-self-md-center">
                    @csrf
                    @method('PATCH')
                    <button id="mark-cod-paid-button" type="button" class="btn btn-success btn-sm">
                        Mark as Paid
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body">
                <div class="text-muted small">Customer</div>
                <div class="fw-semibold">{{ $order->customer->name }}</div>
                <div class="small text-muted">{{ $order->customer->email }}</div>
            </div></div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body">
                <div class="text-muted small">Order Date</div>
                <div class="fw-semibold">{{ $order->created_at->format('M d, Y') }}</div>
                <div class="small text-muted">{{ $order->created_at->format('g:i A') }}</div>
            </div></div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body">
                <div class="text-muted small">Status</div>
                <div class="fw-semibold">{{ ucfirst($order->status) }}</div>
            </div></div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body">
                <div class="text-muted small">Total Amount</div>
                <div class="fw-semibold">&#8369;{{ number_format($order->total_amount, 2) }}</div>
            </div></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3"><h5 class="fw-bold mb-0">Order Items</h5></div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Item</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th class="text-end pe-4">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->orderItems as $orderItem)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $orderItem->item->name }}</td>
                            <td>&#8369;{{ number_format($orderItem->unit_price, 2) }}</td>
                            <td>{{ $orderItem->quantity }}</td>
                            <td class="text-end pe-4 fw-semibold">&#8369;{{ number_format($orderItem->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total</th>
                        <th class="text-end pe-4">&#8369;{{ number_format($order->total_amount, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold">Order Notes</h6>
            <p class="mb-0 text-muted">{{ $order->notes ?: 'No notes provided.' }}</p>
        </div>
    </div>
@endsection

@push('scripts')
    @if ($order->payment_method === 'cod'
        && $order->payment_status === 'unpaid'
        && $order->status !== 'cancelled')
        <script>
            document.getElementById('mark-cod-paid-button').addEventListener('click', function () {
                Swal.fire({
                    title: 'Confirm COD Payment?',
                    text: 'Mark this Cash on Delivery order as paid?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    confirmButtonText: 'Yes, mark as paid'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('mark-cod-paid-form').submit();
                    }
                });
            });
        </script>
    @endif

    @if (in_array($order->status, ['pending', 'confirmed', 'processing'], true))
        <script>
            document.getElementById('cancel-order-button').addEventListener('click', function () {
                Swal.fire({
                    title: 'Cancel this order?',
                    text: 'The ordered quantities will be returned to stock.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Yes, cancel order'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('cancel-order-form').submit();
                    }
                });
            });
        </script>
    @endif
@endpush
