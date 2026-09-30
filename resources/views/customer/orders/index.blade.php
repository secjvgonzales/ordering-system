@extends('layouts.main')

@section('title', 'My Orders')

@section('content')
    <div class="mb-4">
        <span class="text-danger fw-semibold text-uppercase small">Order history</span>
        <h1 class="fw-bold mt-1 mb-1">My Orders</h1>
        <p class="text-muted mb-0">View your submitted orders and their current status.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if ($orders->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-receipt display-4 text-muted"></i>
                    <h5 class="fw-bold mt-3">No orders yet</h5>
                    <a href="{{ route('customer.shop') }}" class="btn btn-danger mt-2">Start Shopping</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Order Number</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td class="ps-4 fw-semibold">{{ $order->order_number }}</td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    <td><span class="badge text-bg-warning">{{ ucfirst($order->status) }}</span></td>
                                    <td>&#8369;{{ number_format($order->total_amount, 2) }}</td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('customer.orders.show', $order) }}"
                                            class="btn btn-outline-danger btn-sm">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
