@extends('layouts.main')

@section('title', 'Manage Orders')

@section('content')
    <div class="mb-4">
        <span class="text-danger fw-semibold text-uppercase small">Order management</span>
        <h1 class="fw-bold mt-1 mb-1">Orders</h1>
        <p class="text-muted mb-0">View and manage customer orders.</p>
    </div>

    <form method="GET" class="card card-body border-0 shadow-sm mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="">All statuses</option>
                    @foreach (['pending', 'confirmed', 'processing', 'completed', 'cancelled'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="date_from" class="form-label">From</label>
                <input id="date_from" type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label">To</label>
                <input id="date_to" type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-danger flex-grow-1">Filter</button>
                <a href="{{ route($routePrefix.'.orders.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if ($orders->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-receipt display-4 text-muted"></i>
                    <h5 class="fw-bold mt-3">No orders found</h5>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Order Number</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                @php
                                    $statusClass = match ($order->status) {
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        'processing' => 'primary',
                                        'confirmed' => 'info',
                                        default => 'warning',
                                    };
                                @endphp
                                <tr>
                                    <td class="ps-4 fw-semibold">{{ $order->order_number }}</td>
                                    <td>{{ $order->customer->name }}</td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    <td><span class="badge text-bg-{{ $statusClass }}">{{ ucfirst($order->status) }}</span></td>
                                    <td>&#8369;{{ number_format($order->total_amount, 2) }}</td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route($routePrefix.'.orders.show', $order) }}"
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
