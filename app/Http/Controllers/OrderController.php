<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Services\MayaCheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('customer.cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $items = Item::whereIn('id', array_keys($cart))->get()->keyBy('id');

        if ($items->count() !== count($cart)) {
            return redirect()->route('customer.cart.index')
                ->with('error', 'An item in your cart is no longer available.');
        }

        $cartItems = collect();
        $grandTotal = 0;
        $canCheckout = true;

        foreach ($cart as $itemId => $quantity) {
            $item = $items->get($itemId);
            $subtotal = (float) $item->price * $quantity;
            $grandTotal += $subtotal;

            if ($item->status !== 'active' || $quantity < 1 || $quantity > $item->stock_quantity) {
                $canCheckout = false;
            }

            $cartItems->push([
                'item' => $item,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ]);
        }

        return view('customer.checkout.index', compact('cartItems', 'grandTotal', 'canCheckout'));
    }

    public function store(Request $request, MayaCheckoutService $maya)
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:cod,demo_card,maya'],
        ]);
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('customer.cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $order = DB::transaction(function () use ($cart, $request, $validated) {
            $items = Item::whereIn('id', array_keys($cart))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($items->count() !== count($cart)) {
                throw ValidationException::withMessages([
                    'cart' => 'An item in your cart is no longer available.',
                ]);
            }

            $orderLines = [];
            $grandTotal = 0;

            foreach ($cart as $itemId => $quantity) {
                $item = $items->get($itemId);
                $quantity = (int) $quantity;

                if ($item->status !== 'active') {
                    throw ValidationException::withMessages([
                        'cart' => $item->name.' is no longer available.',
                    ]);
                }

                if ($quantity < 1 || $quantity > $item->stock_quantity) {
                    throw ValidationException::withMessages([
                        'cart' => 'There is not enough stock for '.$item->name.'.',
                    ]);
                }

                $unitPrice = (float) $item->price;
                $subtotal = $unitPrice * $quantity;
                $grandTotal += $subtotal;
                $orderLines[] = compact('item', 'quantity', 'unitPrice', 'subtotal');
            }

            $order = Order::create([
                'order_number' => 'ORD-'.now()->format('YmdHis').'-'.random_int(1000, 9999),
                'customer_id' => $request->user()->id,
                'total_amount' => $grandTotal,
                'status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'payment_status' => in_array($validated['payment_method'], ['demo_card', 'maya'], true)
                    ? 'pending'
                    : 'unpaid',
                'notes' => $validated['notes'] ?? null,
                'created_by' => $request->user()->id,
            ]);

            $order->statusHistories()->create([
                'status' => 'pending',
                'changed_by' => $request->user()->id,
                'note' => 'Order placed.',
            ]);

            foreach ($orderLines as $line) {
                $order->orderItems()->create([
                    'item_id' => $line['item']->id,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unitPrice'],
                    'subtotal' => $line['subtotal'],
                ]);

                $line['item']->decrement('stock_quantity', $line['quantity']);
            }

            return $order;
        });

        $request->session()->forget('cart');

        if ($order->payment_method === 'demo_card') {
            return redirect()->route('customer.demo-payment.show', $order);
        }

        if ($order->payment_method === 'maya') {
            try {
                $checkout = $maya->createCheckout($order);
                $order->update(['maya_checkout_id' => $checkout['checkoutId']]);

                return redirect()->away($checkout['redirectUrl']);
            } catch (Throwable $exception) {
                report($exception);
                $order->update(['payment_status' => 'failed']);

                return redirect()->route('customer.orders.show', $order)
                    ->with('error', 'Your order was created, but Maya checkout could not be started. Please contact the store before trying again.');
            }
        }

        return redirect()->route('customer.orders.show', $order)
            ->with('success', 'Your order has been placed successfully.');
    }

    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->latest()
            ->get();

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order)
    {
        if ($order->customer_id !== $request->user()->id) {
            abort(404);
        }

        $order->load(['customer', 'orderItems.item', 'statusHistories']);

        return view('customer.orders.show', compact('order'));
    }

    public function receipt(Request $request, Order $order)
    {
        if ($order->customer_id !== $request->user()->id) {
            abort(404);
        }

        if ($order->payment_status !== 'paid') {
            return redirect()->route('customer.orders.show', $order)
                ->with('error', 'A receipt is available after payment is confirmed.');
        }

        $order->load(['customer', 'orderItems.item']);

        return view('customer.orders.receipt', compact('order'));
    }
}
