<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MayaCheckoutService
{
    public function createCheckout(Order $order): array
    {
        $this->ensureSandboxConfiguration('public_key');
        $order->loadMissing(['customer', 'orderItems.item']);

        $response = Http::withBasicAuth(config('services.maya.public_key'), '')
            ->acceptJson()
            ->timeout(15)
            ->post($this->baseUrl().'/checkout/v1/checkouts', [
                'totalAmount' => [
                    'value' => (float) $order->total_amount,
                    'currency' => 'PHP',
                ],
                'buyer' => [
                    'firstName' => $order->customer->name,
                    'contact' => [
                        'email' => $order->customer->email,
                    ],
                ],
                'items' => $order->orderItems->map(function ($orderItem) {
                    return [
                        'name' => $orderItem->item?->name ?? 'THREADLINE Product',
                        'code' => (string) $orderItem->item_id,
                        'quantity' => $orderItem->quantity,
                        'amount' => [
                            'value' => (float) $orderItem->unit_price,
                            'currency' => 'PHP',
                        ],
                        'totalAmount' => [
                            'value' => (float) $orderItem->subtotal,
                            'currency' => 'PHP',
                        ],
                    ];
                })->values()->all(),
                'requestReferenceNumber' => $order->order_number,
                'redirectUrl' => [
                    'success' => route('customer.maya.success', ['order' => $order->id]),
                    'failure' => route('customer.maya.failure', ['order' => $order->id]),
                    'cancel' => route('customer.maya.cancel', ['order' => $order->id]),
                ],
            ]);

        $checkout = $response->json();

        if (! $response->successful() || empty($checkout['checkoutId']) || empty($checkout['redirectUrl'])) {
            throw new RuntimeException('Maya could not start checkout. Please try again later.');
        }

        return $checkout;
    }

    public function retrievePayment(string $paymentId): array
    {
        $this->ensureSandboxConfiguration('secret_key');

        $response = Http::withBasicAuth(config('services.maya.secret_key'), '')
            ->acceptJson()
            ->timeout(15)
            ->get($this->baseUrl().'/payments/v1/payments/'.urlencode($paymentId));

        if (! $response->successful() || ! is_array($response->json())) {
            throw new RuntimeException('Maya payment verification is temporarily unavailable.');
        }

        return $response->json();
    }

    private function ensureSandboxConfiguration(string $key): void
    {
        if (config('services.maya.environment') !== 'sandbox'
            || parse_url($this->baseUrl(), PHP_URL_HOST) !== 'pg-sandbox.paymaya.com') {
            throw new RuntimeException('This demo supports Maya Sandbox only.');
        }

        if (blank(config('services.maya.'.$key))) {
            throw new RuntimeException('Maya Sandbox credentials are not configured.');
        }
    }

    private function baseUrl(): string
    {
        return rtrim(config('services.maya.base_url'), '/');
    }
}
