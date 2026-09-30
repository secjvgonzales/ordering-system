@php
    $offcanvasCart = session('cart', []);
    $offcanvasProducts = \App\Models\Item::whereIn('id', array_keys($offcanvasCart))->get()->keyBy('id');
    $offcanvasLines = collect();
    $offcanvasTotal = 0;

    foreach ($offcanvasCart as $itemId => $quantity) {
        $item = $offcanvasProducts->get($itemId);

        if (! $item) {
            continue;
        }

        $quantity = (int) $quantity;
        $subtotal = (float) $item->price * $quantity;
        $offcanvasTotal += $subtotal;
        $offcanvasLines->push(compact('item', 'quantity', 'subtotal'));
    }
@endphp

<div class="offcanvas offcanvas-end" tabindex="-1" id="customerCartOffcanvas"
    aria-labelledby="customerCartOffcanvasLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold" id="customerCartOffcanvasLabel">
            <i class="bi bi-cart3 me-1"></i> Your Cart
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body d-flex flex-column">
        @if ($offcanvasLines->isEmpty())
            <div class="text-center my-auto">
                <i class="bi bi-cart-x display-4 text-muted"></i>
                <p class="fw-semibold mt-3">Your cart is empty</p>
                <a href="{{ route('customer.shop') }}" class="btn btn-threadline">Shop</a>
            </div>
        @else
            <div class="flex-grow-1">
                @foreach ($offcanvasLines as $line)
                    <div class="d-flex gap-3 py-3 border-bottom">
                        @if ($line['item']->image_path && file_exists(public_path($line['item']->image_path)))
                            <img src="{{ asset($line['item']->image_path) }}" alt="{{ $line['item']->name }}"
                                class="rounded object-fit-cover flex-shrink-0" width="64" height="64">
                        @else
                            <div class="rounded bg-light d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 64px; height: 64px;">
                                <i class="bi bi-bag text-dark"></i>
                            </div>
                        @endif

                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $line['item']->name }}</div>
                            <div class="small text-muted mb-2">
                                Price: &#8369;{{ number_format($line['item']->price, 2) }}
                            </div>

                            <div class="d-flex align-items-center gap-2 mb-2">
                                <form method="POST" action="{{ route('customer.cart.update', $line['item']) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="quantity" value="{{ $line['quantity'] - 1 }}">
                                    <button type="submit" class="btn btn-outline-dark btn-sm px-2 py-0"
                                        aria-label="Decrease {{ $line['item']->name }} quantity"
                                        @disabled($line['quantity'] <= 1)>&minus;</button>
                                </form>

                                <span class="small fw-semibold text-center" style="min-width: 1.5rem;">{{ $line['quantity'] }}</span>

                                <form method="POST" action="{{ route('customer.cart.update', $line['item']) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="quantity" value="{{ $line['quantity'] + 1 }}">
                                    <button type="submit" class="btn btn-outline-dark btn-sm px-2 py-0"
                                        aria-label="Increase {{ $line['item']->name }} quantity">+</button>
                                </form>
                            </div>

                            <form method="POST" action="{{ route('customer.cart.destroy', $line['item']) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link link-danger btn-sm p-0 text-decoration-none">Remove</button>
                            </form>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">Subtotal</div>
                            <div class="fw-semibold">&#8369;{{ number_format($line['subtotal'], 2) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="border-top pt-3 mt-3">
                <div class="d-flex justify-content-between fs-5 fw-bold mb-3">
                    <span>Total</span>
                    <span>&#8369;{{ number_format($offcanvasTotal, 2) }}</span>
                </div>
                <div class="d-grid gap-2">
                    <a href="{{ route('customer.cart.index') }}" class="btn btn-outline-secondary">View Cart</a>
                    <a href="{{ route('customer.checkout.index') }}" class="btn btn-threadline">Proceed to Checkout</a>
                </div>
            </div>
        @endif
    </div>
</div>
