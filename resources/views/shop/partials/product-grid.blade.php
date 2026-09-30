@forelse ($items as $item)
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <article class="card product-card h-100 overflow-hidden">
            <a href="{{ route('products.show', $item) }}" class="text-decoration-none">
                @if ($item->image_path && file_exists(public_path($item->image_path)))
                    <img src="{{ asset($item->image_path) }}" class="product-image" alt="{{ $item->name }}">
                @else
                    <div class="product-placeholder d-flex align-items-center justify-content-center">
                        <i class="bi bi-image fs-1"></i>
                    </div>
                @endif
            </a>

            <div class="card-body px-0 d-flex flex-column">
                <div class="d-flex justify-content-between gap-3">
                    <h2 class="h6 fw-semibold mb-1">{{ $item->name }}</h2>
                    <span class="fw-semibold text-nowrap">&#8369;{{ number_format($item->price, 2) }}</span>
                </div>
                <div class="small mb-3 {{ $item->stock_quantity > 0 ? 'text-success' : 'text-danger' }}">
                    {{ $item->stock_quantity > 0 ? 'In stock' : 'Out of stock' }}
                </div>
                <div class="d-grid gap-2 mt-auto">
                    <a href="{{ route('products.show', $item) }}" class="btn btn-outline-dark">View Product</a>

                    @if (auth()->check() && auth()->user()->role === 'customer')
                        <form method="POST" action="{{ route('customer.cart.store', $item) }}">
                            @csrf
                            <button type="submit" class="btn btn-threadline w-100" @disabled($item->stock_quantity <= 0)>
                                Add to Cart
                            </button>
                        </form>
                    @elseif (auth()->guest())
                        <form method="POST" action="{{ route('products.cart.guest', $item) }}">
                            @csrf
                            <button type="submit" class="btn btn-threadline w-100" @disabled($item->stock_quantity <= 0)>
                                Add to Cart
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </article>
    </div>
@empty
    <div class="col-12">
        <div class="border text-center py-5">
            <i class="bi bi-search fs-1 text-muted d-block mb-2"></i>
            No products found.
        </div>
    </div>
@endforelse
