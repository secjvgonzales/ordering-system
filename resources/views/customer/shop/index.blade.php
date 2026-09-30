@extends('layouts.main')

@section('title', 'Shop | THREADLINE')

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-end gap-3 py-4 mb-4 border-bottom">
        <div>
            <span class="text-uppercase small fw-semibold text-muted" style="letter-spacing: .16em;">THREADLINE / Essentials</span>
            <h1 class="display-5 fw-bold mt-2 mb-1">Shop the collection</h1>
            <p class="text-muted mb-0">Clean silhouettes made for everyday rotation.</p>
        </div>
        <span id="product-count" class="small text-muted">{{ $items->count() }} {{ $items->count() === 1 ? 'product' : 'products' }}</span>
    </div>

    @include('shop.partials.filters')

    <div id="product-grid" class="row g-4">
        @include('shop.partials.product-grid')
    </div>
@endsection

@include('shop.partials.live-search')
