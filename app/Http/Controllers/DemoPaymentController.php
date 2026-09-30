<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DemoPaymentController extends Controller
{
    public function show(Request $request, Order $order): View|RedirectResponse
    {
        $this->ensurePayableByCustomer($request, $order);

        if ($order->payment_status === 'paid') {
            return redirect()->route('customer.orders.show', $order)
                ->with('success', 'This demo payment has already been confirmed.');
        }

        return view('customer.payment.demo', compact('order'));
    }

    public function process(Request $request, Order $order): RedirectResponse
    {
        $this->ensurePayableByCustomer($request, $order);

        if (in_array($request->input('demo_result'), ['success', 'failure'], true)) {
            return $this->processSimulation($request, $order, $request->input('demo_result'));
        }

        $validator = Validator::make($request->only([
            'cardholder_name',
            'card_number',
            'expiry',
            'cvv',
        ]), [
            'cardholder_name' => ['required', 'string', 'max:255'],
            'card_number' => ['required', 'string'],
            'expiry' => ['required', 'string'],
            'cvv' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        return $this->processSimulation($request, $order, 'success');
    }

    private function processSimulation(Request $request, Order $order, string $demoResult): RedirectResponse
    {
        $result = DB::transaction(function () use ($request, $order, $demoResult) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $this->ensurePayableByCustomer($request, $lockedOrder);

            if ($lockedOrder->payment_status === 'paid') {
                return 'already_paid';
            }

            if ($lockedOrder->status === 'cancelled') {
                return 'cancelled';
            }

            if ($demoResult === 'failure') {
                $lockedOrder->update(['payment_status' => 'failed']);

                return 'failed';
            }

            $lockedOrder->update([
                'payment_status' => 'paid',
                'payment_reference' => 'DEMO-'.Str::upper(Str::random(8)),
                'paid_at' => now(),
            ]);

            return 'paid';
        });

        if ($result === 'already_paid') {
            return redirect()->route('customer.orders.show', $order)
                ->with('success', 'This demo payment has already been confirmed.');
        }

        if ($result === 'cancelled') {
            return redirect()->route('customer.orders.show', $order)
                ->with('error', 'A cancelled order cannot be paid.');
        }

        if ($result === 'failed') {
            return back()->with('error', 'Payment failed. You can retry the demo payment.');
        }

        return redirect()->route('customer.orders.show', $order)
            ->with('success', 'Payment successful! Your demo payment has been confirmed.');
    }

    private function ensurePayableByCustomer(Request $request, Order $order): void
    {
        abort_unless(
            $order->customer_id === $request->user()->id && $order->payment_method === 'demo_card',
            404
        );
    }
}
