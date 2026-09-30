<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OrderManagementController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['pending', 'confirmed', 'processing', 'completed', 'cancelled'])],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $orders = Order::with('customer')
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($validated['date_from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($validated['date_to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->latest()
            ->get();
        $routePrefix = $request->user()->role;

        return view('orders.management.index', compact('orders', 'routePrefix'));
    }

    public function show(Request $request, Order $order)
    {
        $order->load(['customer', 'orderItems.item', 'statusHistories.changedBy']);
        $routePrefix = $request->user()->role;

        return view('orders.management.show', compact('order', 'routePrefix'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['confirmed', 'processing', 'completed'])],
        ]);

        $updated = DB::transaction(function () use ($order, $validated, $request) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $transitions = [
                'pending' => 'confirmed',
                'confirmed' => 'processing',
                'processing' => 'completed',
            ];

            if (($transitions[$lockedOrder->status] ?? null) !== $validated['status']) {
                return false;
            }

            $lockedOrder->update(['status' => $validated['status']]);
            $lockedOrder->statusHistories()->create([
                'status' => $validated['status'],
                'changed_by' => $request->user()->id,
                'note' => 'Order status updated by '.ucfirst($request->user()->role).'.',
            ]);

            return true;
        });

        if (! $updated) {
            return back()->with('error', 'That order status transition is not allowed.');
        }

        return back()->with('success', 'Order status updated successfully.');
    }

    public function cancel(Request $request, Order $order)
    {
        $cancelled = DB::transaction(function () use ($order, $request) {
            $lockedOrder = Order::whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! in_array($lockedOrder->status, ['pending', 'confirmed', 'processing'], true)) {
                return false;
            }

            $orderItems = $lockedOrder->orderItems()->get();
            $items = Item::whereIn('id', $orderItems->pluck('item_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($orderItems as $orderItem) {
                $items->get($orderItem->item_id)?->increment('stock_quantity', $orderItem->quantity);
            }

            $lockedOrder->update(['status' => 'cancelled']);
            $lockedOrder->statusHistories()->create([
                'status' => 'cancelled',
                'changed_by' => $request->user()->id,
                'note' => 'Order cancelled and stock restored.',
            ]);

            return true;
        });

        if (! $cancelled) {
            return back()->with('error', 'This order can no longer be cancelled.');
        }

        return back()->with('success', 'Order cancelled and stock restored.');
    }

    public function markPaymentPaid(Order $order)
    {
        $updated = DB::transaction(function () use ($order) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($lockedOrder->payment_method !== 'cod'
                || $lockedOrder->payment_status !== 'unpaid'
                || $lockedOrder->status === 'cancelled') {
                return false;
            }

            $lockedOrder->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'payment_reference' => 'COD-'.$lockedOrder->id.'-'.Str::upper(Str::random(6)),
            ]);

            return true;
        });

        if (! $updated) {
            return back()->with('error', 'This COD order cannot be marked as paid.');
        }

        return back()->with('success', 'Cash on Delivery payment marked as paid.');
    }
}
