@extends('layouts.main')

@section('title', 'Shop | THREADLINE')

@section('content')
    <section class="py-4 py-lg-5 mb-4 border-bottom">
        <div class="row align-items-end g-3">
            <div class="col-lg-8">
                <span class="text-uppercase small fw-semibold text-muted" style="letter-spacing: .16em;">THREADLINE / Essentials</span>
                <h1 class="display-3 fw-bold mt-2 mb-2">Everyday form.<br>Street-ready comfort.</h1>
            </div>
            <div class="col-lg-4">
                <p class="text-muted mb-0">Minimal tees designed for repeat wear, relaxed proportions, and effortless daily styling.</p>
            </div>
        </div>
    </section>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h5 text-uppercase mb-0" style="letter-spacing: .12em;">Latest products</h2>
        <span id="product-count" class="small text-muted">{{ $items->count() }} {{ $items->count() === 1 ? 'product' : 'products' }}</span>
    </div>

    @include('shop.partials.filters')

    <div id="product-grid" class="row g-4">
        @include('shop.partials.product-grid')
    </div>
@endsection

@include('shop.partials.live-search')
