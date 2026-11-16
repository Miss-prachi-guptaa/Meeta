<?php

namespace App\Services\Vendor;

use App\Models\Notification;
use App\Models\Order;
use App\Models\SubOrder;

class OrderLifecycleService
{
    /**
     * Keep the customer-facing parent order status in sync with all vendor sub-orders.
     */
    public function syncParentOrder(Order $order): void
    {
        $subOrders = $order->subOrders()->get(['order_status', 'subtotal']);
        $statuses = $subOrders->pluck('order_status');

        $status = match (true) {
            $statuses->isEmpty() => $order->order_status,
            $statuses->every(fn (string $value) => $value === 'cancelled') => 'cancelled',
            $statuses->every(fn (string $value) => in_array($value, ['delivered', 'cancelled'], true)) => 'delivered',
            $statuses->contains('shipped') || $statuses->contains('delivered') => 'shipped',
            $statuses->contains(fn (string $value) => in_array($value, ['processing', 'ready_to_ship'], true)) => 'processing',
            default => 'confirmed',
        };

        $activeSubtotal = round((float) $subOrders->where('order_status', '!=', 'cancelled')->sum('subtotal'), 2);
        $shippingAmount = $activeSubtotal > 0 ? (float) $order->shipping_amount : 0;
        $updates = [
            'order_status' => $status,
            'subtotal' => $activeSubtotal,
            'total_amount' => round($activeSubtotal + $shippingAmount - (float) $order->discount_amount + (float) $order->tax_amount, 2),
        ];

        if ($status === 'cancelled') {
            $updates['payment_status'] = 'failed';
            $order->payments()->where('status', 'pending')->update(['status' => 'cancelled', 'amount' => 0]);
        } else {
            $order->payments()->where('status', 'pending')->update(['amount' => $updates['total_amount']]);
        }

        // COD is considered collected only after every non-cancelled package is delivered.
        if ($status === 'delivered') {
            $updates['payment_status'] = 'paid';
            $order->payments()->where('payment_method', 'cod')->where('status', 'pending')->update(['status' => 'paid', 'paid_at' => now()]);
        }

        $order->update($updates);
    }

    /**
     * Add an in-app update that the customer can show in the order notification feed.
     */
    public function notifyCustomer(SubOrder $subOrder, string $title, string $message, string $type): void
    {
        Notification::create([
            'user_id' => $subOrder->order->user_id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'channel' => 'database',
            'reference_type' => 'order',
            'reference_id' => $subOrder->order_id,
        ]);
    }
}
