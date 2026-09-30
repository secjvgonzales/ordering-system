@extends('layouts.main')

@section('title', 'Order Details')

@section('content')
    @php
        $steps = [
            'pending' => 'Order Placed',
            'confirmed' => 'Confirmed',
            'processing' => 'Processing',
            'completed' => 'Completed',
        ];
        $historyByStatus = $order->statusHistories->keyBy('status');
        $currentStepIndex = array_search($order->status, array_keys($steps), true);
        $paymentStatusClass = match ($order->payment_status) {
            'paid' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            'cancelled' => 'dark',
            default => 'secondary',
        };
        $orderStatusClass = match ($order->status) {
            'completed' => 'success',
            'cancelled' => 'danger',
            'processing' => 'primary',
            'confirmed' => 'info',
            default => 'warning',
        };
        $paymentReference = $order->payment_reference ?? $order->maya_reference;
    @endphp

    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-end gap-3 mb-4">
        <div>
            <a href="{{ route('customer.orders.index') }}" class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left"></i> My Orders
            </a>
            <h1 class="fw-bold mt-2 mb-1">Order Details</h1>
            <p class="text-muted mb-0">{{ $order->order_number }}</p>
        </div>
        <span class="badge text-bg-{{ $orderStatusClass }} align-self-start align-self-sm-end px-3 py-2">
            {{ ucfirst($order->status) }}
        </span>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold mb-1">Order Fulfillment</h5>
                    <p class="small text-muted mb-0">Track each step as your order is prepared.</p>
                </div>
                @if ($order->status === 'cancelled')
                    <span class="badge text-bg-danger">Cancelled</span>
                @endif
            </div>

            <div class="order-tracker">
                @foreach ($steps as $status => $label)
                    @php
                        $stepIndex = array_search($status, array_keys($steps), true);
                        $history = $historyByStatus->get($status);
                        $isReached = $order->status === 'cancelled'
                            ? (bool) $history
                            : $currentStepIndex !== false && $stepIndex <= $currentStepIndex;
                        $isCurrent = $order->status === $status;
                    @endphp

                    <div class="tracker-step {{ $isReached ? 'is-reached' : '' }} {{ $isCurrent ? 'is-current' : '' }}">
                        <div class="tracker-marker">
                            <i class="bi {{ $isReached ? 'bi-check-lg' : 'bi-circle' }}"></i>
                        </div>
                        <div class="tracker-copy">
                            <div class="fw-semibold">{{ $label }}</div>
                            <div class="small text-muted">
                                {{ $history ? $history->created_at->format('M d, g:i A') : 'Pending' }}
                            </div>
                        </div>
                    </div>

                    @if (! $loop->last)
                        <div class="tracker-connector {{ $isReached && $historyByStatus->has(array_keys($steps)[$stepIndex + 1]) ? 'is-reached' : '' }}"></div>
                    @endif
                @endforeach
            </div>

            @if ($order->status === 'cancelled')
                <div class="alert alert-danger mt-4 mb-0">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelled
                    @if ($historyByStatus->get('cancelled'))
                        on {{ $historyByStatus->get('cancelled')->created_at->format('M d, Y g:i A') }}
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Order Date</div>
                    <div class="fw-semibold">{{ $order->created_at->format('M d, Y g:i A') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Customer</div>
                    <div class="fw-semibold">{{ $order->customer->name }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Total Amount</div>
                    <div class="fw-semibold">&#8369;{{ number_format($order->total_amount, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Payment</div>
                    <div class="fw-semibold mb-1">
                        {{ match ($order->payment_method) {
                            'demo_card' => 'Demo Card Payment',
                            'maya' => 'Maya',
                            'cod' => 'Cash on Delivery',
                            default => 'Not recorded',
                        } }}
                    </div>
                    <span class="badge text-bg-{{ $paymentStatusClass }}">{{ ucfirst($order->payment_status) }}</span>
                    @if ($paymentReference)
                        <div class="small text-muted mt-2">Reference</div>
                        <div class="small fw-semibold">{{ $paymentReference }}</div>
                    @endif
                    @if ($order->paid_at)
                        <div class="small text-muted mt-1">{{ $order->paid_at->format('M d, Y g:i A') }}</div>
                    @endif
                    @if ($order->payment_status === 'paid')
                        <a href="{{ route('customer.orders.receipt', $order) }}" class="btn btn-outline-dark btn-sm mt-3">
                            <i class="bi bi-printer me-1"></i> Print Receipt
                        </a>
                    @endif
                    @if ($order->payment_method === 'demo_card'
                        && in_array($order->payment_status, ['pending', 'failed'], true)
                        && $order->status !== 'cancelled')
                        <a href="{{ route('customer.demo-payment.show', $order) }}" class="btn btn-dark btn-sm mt-3">
                            Pay Now
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3"><h5 class="fw-bold mb-0">Order Items</h5></div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th class="text-end pe-4">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->orderItems as $orderItem)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    @if ($orderItem->item->image_path && file_exists(public_path($orderItem->item->image_path)))
                                        <img src="{{ asset($orderItem->item->image_path) }}" alt="{{ $orderItem->item->name }}"
                                            class="rounded object-fit-cover" width="48" height="48">
                                    @endif
                                    <span class="fw-semibold">{{ $orderItem->item->name }}</span>
                                </div>
                            </td>
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

    @if ($order->notes)
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold">Order Notes</h6>
                <p class="mb-0 text-muted">{{ $order->notes }}</p>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <style>
        .order-tracker {
            display: flex;
            align-items: flex-start;
        }

        .tracker-step {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            min-width: 145px;
            color: #adb5bd;
        }

        .tracker-marker {
            width: 2rem;
            height: 2rem;
            border: 2px solid #ced4da;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: #fff;
        }

        .tracker-step.is-reached {
            color: #212529;
        }

        .tracker-step.is-reached .tracker-marker {
            border-color: #212529;
            background: #212529;
            color: #fff;
        }

        .tracker-step.is-current .tracker-marker {
            box-shadow: 0 0 0 .3rem rgba(33, 37, 41, .12);
        }

        .tracker-connector {
            height: 2px;
            background: #dee2e6;
            flex: 1;
            min-width: 24px;
            margin: 1rem .75rem 0;
        }

        .tracker-connector.is-reached {
            background: #212529;
        }

        @media (max-width: 767.98px) {
            .order-tracker {
                flex-direction: column;
            }

            .tracker-step {
                min-width: 0;
            }

            .tracker-connector {
                width: 2px;
                height: 32px;
                min-width: 2px;
                flex: none;
                margin: .25rem 0 .25rem 1rem;
            }
        }
    </style>
@endpush
