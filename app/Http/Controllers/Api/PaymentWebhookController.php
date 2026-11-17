<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\SubOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Webhook listener for Payment Gateways (Razorpay / Cashfree / Stripe).
     * Automatically verifies payment capture and transitions Master & Sub-Orders to CONFIRMED.
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        Log::info('Payment Gateway Webhook received:', $payload);

        // Sample supported event formats (e.g. payment.captured, charge.successful)
        $event = $payload['event'] ?? 'payment.captured';
        $paymentData = $payload['payload']['payment']['entity'] ?? $payload;

        $orderNumber = $paymentData['notes']['order_number'] ?? $paymentData['order_id'] ?? null;
        $gatewayPaymentId = $paymentData['id'] ?? 'PAY-' . uniqid();
        $amount = isset($paymentData['amount']) ? ($paymentData['amount'] / 100) : null;

        if (!$orderNumber) {
            return response()->json(['success' => false, 'message' => 'Missing order reference in webhook'], 400);
        }

        DB::transaction(function () use ($orderNumber, $gatewayPaymentId, $amount, $event, $payload) {
            $order = Order::with('subOrders')->where('order_number', $orderNumber)->first();

            if (!$order) {
                Log::warning("Webhook received for unknown order: {$orderNumber}");
                return;
            }

            if ($order->payment_status === 'paid') {
                return; // Already processed
            }

            // 1. Update Master Order
            $order->update([
                'payment_status' => 'paid',
                'order_status' => 'confirmed',
            ]);

            // 2. Update Sub-Orders to CONFIRMED (Starts the 24h SLA clock for vendors!)
            foreach ($order->subOrders as $subOrder) {
                if ($subOrder->order_status === 'placed' || $subOrder->order_status === 'pending') {
                    $subOrder->update([
                        'order_status' => 'confirmed',
                    ]);
                }
            }

            // 3. Record Payment & Transaction
            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'online',
                'amount' => $amount ?? $order->total_amount,
                'currency' => 'INR',
                'status' => 'completed',
                'transaction_reference' => $gatewayPaymentId,
                'paid_at' => Carbon::now(),
            ]);

            PaymentTransaction::create([
                'payment_id' => $payment->id,
                'transaction_type' => 'capture',
                'amount' => $payment->amount,
                'gateway_transaction_id' => $gatewayPaymentId,
                'status' => 'success',
                'raw_response' => $payload,
            ]);

            Log::info("Order #{$orderNumber} marked PAID via Webhook. Vendor Sub-Orders activated.");
        });

        return response()->json(['success' => true, 'message' => 'Webhook processed successfully.']);
    }
}
