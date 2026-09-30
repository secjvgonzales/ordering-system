@extends('layouts.main')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div>
            <span class="text-primary fw-semibold text-uppercase small">Store overview</span>
            <h1 class="fw-bold mt-1 mb-1">Admin Dashboard</h1>
            <p class="text-muted mb-0">Welcome, {{ auth()->user()->name }}.</p>
        </div>

        <form method="GET" class="d-flex align-items-end gap-2">
            <div>
                <label for="range" class="form-label small mb-1">Reporting period</label>
                <select id="range" name="range" class="form-select" onchange="this.form.submit()">
                    <option value="today" @selected($range === 'today')>Today</option>
                    <option value="7days" @selected($range === '7days')>Last 7 days</option>
                    <option value="30days" @selected($range === '30days')>Last 30 days</option>
                    <option value="all" @selected($range === 'all')>All time</option>
                </select>
            </div>
            <noscript><button class="btn btn-primary">Apply</button></noscript>
        </form>
    </div>

    <div class="row g-3 mb-4">
        @php
            $orderCards = [
                ['label' => 'Total Orders', 'value' => number_format($metrics['totalOrders']), 'icon' => 'bi-receipt', 'color' => 'primary'],
                ['label' => 'Pending', 'value' => number_format($metrics['pendingOrders']), 'icon' => 'bi-hourglass-split', 'color' => 'warning'],
                ['label' => 'Processing', 'value' => number_format($metrics['processingOrders']), 'icon' => 'bi-gear', 'color' => 'info'],
                ['label' => 'Completed', 'value' => number_format($metrics['completedOrders']), 'icon' => 'bi-check-circle', 'color' => 'success'],
                ['label' => 'Cancelled', 'value' => number_format($metrics['cancelledOrders']), 'icon' => 'bi-x-circle', 'color' => 'danger'],
                ['label' => 'Total Sales', 'value' => '&#8369;'.number_format($metrics['totalSales'], 2), 'icon' => 'bi-cash-stack', 'color' => 'success'],
            ];
        @endphp

        @foreach ($orderCards as $card)
            <div class="col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">{{ $card['label'] }}</div>
                            <div class="fs-3 fw-bold">{!! $card['value'] !!}</div>
                        </div>
                        <i class="bi {{ $card['icon'] }} fs-2 text-{{ $card['color'] }}"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 fw-bold mb-0">Inventory</h2>
        <a href="{{ route('admin.items.index') }}" class="btn btn-sm btn-outline-primary">Manage products</a>
    </div>

    <div class="row g-3 mb-4">
        @foreach ([
            ['label' => 'Total Products', 'value' => $metrics['totalProducts'], 'icon' => 'bi-box-seam'],
            ['label' => 'Active Products', 'value' => $metrics['activeProducts'], 'icon' => 'bi-bag-check'],
            ['label' => 'Low Stock (5 or less)', 'value' => $metrics['lowStockProducts'], 'icon' => 'bi-exclamation-triangle'],
        ] as $card)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">{{ $card['label'] }}</div>
                            <div class="fs-3 fw-bold">{{ number_format($card['value']) }}</div>
                        </div>
                        <i class="bi {{ $card['icon'] }} fs-2 text-primary"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <div>
                <h2 class="h5 fw-bold mb-0">Orders Needing Attention</h2>
                <small class="text-muted">Latest pending, confirmed, and processing orders</small>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">View all orders</a>
        </div>
        <div class="card-body p-0">
            @if ($attentionOrders->isEmpty())
                <p class="text-muted text-center py-5 mb-0">No orders currently need attention.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Order</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attentionOrders as $order)
                                <tr>
                                    <td class="ps-4 fw-semibold">{{ $order->order_number }}</td>
                                    <td>{{ $order->customer->name }}</td>
                                    <td><span class="badge text-bg-warning">{{ ucfirst($order->status) }}</span></td>
                                    <td>&#8369;{{ number_format($order->total_amount, 2) }}</td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">Review</a>
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
