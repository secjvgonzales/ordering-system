<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MayaPaymentController extends Controller
{
    public function success(Request $request): RedirectResponse
    {
        $order = $this->customerOrder($request);

        return redirect()->route('customer.orders.show', $order)
            ->with('success', 'Payment received. We are verifying your transaction.');
    }

    public function failure(Request $request): RedirectResponse
    {
        $order = $this->customerOrder($request);

        return redirect()->route('customer.orders.show', $order)
            ->with('error', 'Maya could not complete the payment. Your order remains available for review.');
    }

    public function cancel(Request $request): RedirectResponse
    {
        $order = $this->customerOrder($request);

        return redirect()->route('customer.orders.show', $order)
            ->with('error', 'Maya checkout was cancelled. Your order was not marked as paid.');
    }

    private function customerOrder(Request $request): Order
    {
        return Order::whereKey($request->integer('order'))
            ->where('customer_id', $request->user()->id)
            ->firstOrFail();
    }
}
