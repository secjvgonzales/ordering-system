<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $items = Item::whereIn('id', array_keys($cart))->get()->keyBy('id');
        $cartItems = collect();
        $grandTotal = 0;

        foreach ($cart as $itemId => $quantity) {
            $item = $items->get($itemId);

            if (! $item) {
                unset($cart[$itemId]);
                continue;
            }

            $subtotal = (float) $item->price * $quantity;
            $grandTotal += $subtotal;

            $cartItems->push([
                'item' => $item,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ]);
        }

        $request->session()->put('cart', $cart);

        return view('customer.cart.index', compact('cartItems', 'grandTotal'));
    }

    public function store(Request $request, Item $item)
    {
        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($item->status !== 'active') {
            return back()->with('error', 'This item is no longer available.');
        }

        if ($item->stock_quantity < 1) {
            return back()->with('error', 'This item is out of stock.');
        }

        $cart = $request->session()->get('cart', []);
        $quantity = ($cart[$item->id] ?? 0) + ($validated['quantity'] ?? 1);

        if ($quantity > $item->stock_quantity) {
            return back()->with('error', 'You cannot add more than the available stock.');
        }

        $cart[$item->id] = $quantity;
        $request->session()->put('cart', $cart);

        return back()
            ->with('success', $item->name.' added to your cart.')
            ->with('open_cart_offcanvas', true);
    }

    public function guestStore(Request $request, Item $item): RedirectResponse
    {
        abort_unless($item->status === 'active', 404);

        if ($request->user()) {
            abort_unless($request->user()->role === 'customer' && $request->user()->status === 'active', 403);

            return $this->store($request, $item);
        }

        $request->session()->put('url.intended', url()->previous());

        return redirect()
            ->route('register')
            ->with('status', 'Please create an account or log in to add products to your cart.');
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $request->session()->get('cart', []);

        if (! isset($cart[$item->id])) {
            return back()
                ->with('error', 'This item is not in your cart.')
                ->with('open_cart_offcanvas', true);
        }

        if ($item->status !== 'active' || $item->stock_quantity < 1) {
            return back()
                ->with('error', 'This item is no longer available.')
                ->with('open_cart_offcanvas', true);
        }

        if ($validated['quantity'] > $item->stock_quantity) {
            return back()
                ->with('error', 'Quantity cannot exceed the available stock.')
                ->with('open_cart_offcanvas', true);
        }

        $cart[$item->id] = $validated['quantity'];
        $request->session()->put('cart', $cart);

        return back()
            ->with('success', 'Cart quantity updated.')
            ->with('open_cart_offcanvas', true);
    }

    public function destroy(Request $request, Item $item)
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$item->id]);
        $request->session()->put('cart', $cart);

        return back()
            ->with('success', $item->name.' removed from your cart.')
            ->with('open_cart_offcanvas', true);
    }

    public function clear(Request $request)
    {
        $request->session()->forget('cart');

        return back()->with('success', 'Your cart has been cleared.');
    }
}
