<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    /**
     * Use case: complete order-detail screen using purchase-time item and address snapshots.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'order_status' => $this->order_status,
            'payment_status' => $this->payment_status,
            'placed_at' => $this->created_at,
            'can_cancel' => in_array($this->order_status, ['pending', 'confirmed'], true) && $this->payment_status !== 'paid',
            'cancellation_reason' => $this->cancellation_reason,
            'customer_note' => $this->notes,
            'delivery_address' => [
                'full_name' => $this->shipping_full_name,
                'phone' => $this->shipping_phone,
                'address_line_1' => $this->shipping_address_line_1,
                'address_line_2' => $this->shipping_address_line_2,
                'landmark' => $this->shipping_landmark,
                'city' => $this->shipping_city,
                'state' => $this->shipping_state,
                'country' => $this->shipping_country,
                'postal_code' => $this->shipping_postal_code,
            ],
            'amounts' => [
                'subtotal' => (float) $this->subtotal,
                'discount' => (float) $this->discount_amount,
                'tax' => (float) $this->tax_amount,
                'shipping' => (float) $this->shipping_amount,
                'total' => (float) $this->total_amount,
            ],
            'payment' => $this->payments->sortByDesc('created_at')->first() ? [
                'payment_number' => $this->payments->sortByDesc('created_at')->first()->payment_number,
                'method' => $this->payments->sortByDesc('created_at')->first()->payment_method,
                'status' => $this->payments->sortByDesc('created_at')->first()->status,
                'amount' => (float) $this->payments->sortByDesc('created_at')->first()->amount,
                'paid_at' => $this->payments->sortByDesc('created_at')->first()->paid_at,
            ] : null,
            'packages' => $this->subOrders->map(fn ($subOrder) => [
                'id' => $subOrder->id,
                'number' => $subOrder->sub_order_number,
                'vendor' => [
                    'name' => $subOrder->vendor?->store_name,
                    'slug' => $subOrder->vendor?->store_slug,
                ],
                'status' => $subOrder->order_status,
                'cancellation_reason' => $subOrder->cancellation_reason,
                'amount' => (float) $subOrder->total_amount,
                'items' => $subOrder->orderItems->map(fn ($item) => [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_slug' => $item->product?->slug,
                    'product_name' => $item->product_name,
                    'variant_name' => $item->variant_name,
                    'sku' => $item->sku,
                    'quantity' => (int) $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'discount' => (float) $item->discount_amount,
                    'tax' => (float) $item->tax_amount,
                    'total' => (float) $item->total_amount,
                    'image' => $item->product?->images?->sortByDesc('is_primary')->first()?->public_url,
                ])->values(),
                'shipment' => $subOrder->shipments->sortByDesc('created_at')->first() ? $this->shipmentData($subOrder->shipments->sortByDesc('created_at')->first()) : null,
            ])->values(),
        ];
    }

    private function shipmentData($shipment): array
    {
        return [
            'id' => $shipment->id,
            'shipment_number' => $shipment->shipment_number,
            'shipping_partner' => $shipment->shipping_partner,
            'tracking_number' => $shipment->tracking_number,
            'tracking_url' => $shipment->tracking_url,
            'status' => $shipment->status,
            'shipped_at' => $shipment->shipped_at,
            'expected_delivery_at' => $shipment->expected_delivery_at,
            'delivered_at' => $shipment->delivered_at,
            'tracking' => $shipment->tracking->sortBy('tracked_at')->map(fn ($event) => [
                'status' => $event->status,
                'location' => $event->location,
                'description' => $event->description,
                'tracked_at' => $event->tracked_at,
            ])->values(),
        ];
    }
}
