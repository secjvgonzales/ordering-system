<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MayaCheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class MayaWebhookController extends Controller
{
    public function __invoke(Request $request, MayaCheckoutService $maya): JsonResponse
    {
        $paymentId = $request->string('id')->toString();

        if ($paymentId === '') {
            return response()->json(['message' => 'Missing Maya payment ID.'], 422);
        }

        try {
            $payment = $maya->retrievePayment($paymentId);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json(['message' => 'Payment verification is unavailable.'], 503);
        }

        $status = $payment['status'] ?? $payment['paymentStatus'] ?? null;

        if (! in_array($status, ['PAYMENT_SUCCESS', 'PAYMENT_FAILED'], true)) {
            return response()->json(['message' => 'Event ignored.']);
        }

        $referenceNumber = $payment['requestReferenceNumber'] ?? null;
        $order = Order::where(function ($query) use ($paymentId, $referenceNumber) {
            $query->where('maya_checkout_id', $paymentId)
                ->orWhere('maya_payment_id', $paymentId);

            if ($referenceNumber) {
                $query->orWhere('order_number', $referenceNumber);
            }
        })->first();

        if (! $order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        if ($status === 'PAYMENT_SUCCESS'
            && ($this->toCentavos($payment['amount'] ?? 0) !== $this->toCentavos($order->total_amount)
                || ($payment['currency'] ?? null) !== 'PHP')) {
            return response()->json(['message' => 'Payment amount or currency does not match.'], 422);
        }

        DB::transaction(function () use ($order, $payment, $paymentId, $status) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $mayaReference = $payment['receiptNumber']
                ?? data_get($payment, 'receipt.receiptNo')
                ?? $payment['requestReferenceNumber']
                ?? null;

            if ($status === 'PAYMENT_SUCCESS' && $lockedOrder->payment_status !== 'paid') {
                $lockedOrder->update([
                    'payment_status' => 'paid',
                    'maya_payment_id' => $paymentId,
                    'maya_reference' => $mayaReference,
                    'paid_at' => now(),
                ]);
            }

            if ($status === 'PAYMENT_FAILED'
                && ! in_array($lockedOrder->payment_status, ['paid', 'failed'], true)) {
                $lockedOrder->update([
                    'payment_status' => 'failed',
                    'maya_payment_id' => $paymentId,
                    'maya_reference' => $mayaReference,
                ]);
            }
        });

        return response()->json(['message' => 'Webhook processed.']);
    }

    private function toCentavos(mixed $amount): int
    {
        return (int) round((float) $amount * 100);
    }
}
