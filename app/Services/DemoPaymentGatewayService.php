<?php

namespace App\Services;

use App\Models\Order;

class DemoPaymentGatewayService
{
    public const EXPIRY_MINUTES = 15;

    public function prepare(Order $order): void
    {
        $order->update([
            'transaction_ref' => 'DEMO-GW-'.$order->id.'-'.str()->upper(str()->random(8)),
            'payment_expires_at' => now()->addMinutes(self::EXPIRY_MINUTES),
        ]);
    }

    public function qrPayload(Order $order): string
    {
        return implode('|', [
            'SECONDPC-DEMO-GATEWAY',
            "REQUEST:{$order->transaction_ref}",
            'AMOUNT:'.number_format((float) $order->total_amount, 2, '.', ''),
            'EXPIRES:'.$order->payment_expires_at?->toIso8601String(),
        ]);
    }

    public function hasActivePaymentRequest(Order $order): bool
    {
        return $order->payment_method === 'demo_gateway'
            && $order->payment_status === 'pending'
            && $order->payment_expires_at?->isFuture()
            && str_starts_with((string) $order->transaction_ref, 'DEMO-GW-');
    }
}
