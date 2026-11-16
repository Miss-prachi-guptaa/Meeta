<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderListResource extends JsonResource
{
    /**
     * Use case: lightweight order card for the customer's paginated order history.
     */
    public function toArray(Request $request): array
    {
        $items = $this->subOrders->flatMap->orderItems;

        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'order_status' => $this->order_status,
            'payment_status' => $this->payment_status,
            'total_amount' => (float) $this->total_amount,
            'placed_at' => $this->created_at,
            'total_items' => (int) $items->sum('quantity'),
            'total_packages' => $this->subOrders->count(),
            'can_cancel' => in_array($this->order_status, ['pending', 'confirmed'], true) && $this->payment_status !== 'paid',
            'preview_items' => $items->take(3)->map(fn ($item) => [
                'id' => $item->id,
                'product_name' => $item->product_name,
                'variant_name' => $item->variant_name,
                'quantity' => (int) $item->quantity,
                'image' => $item->product?->images?->sortByDesc('is_primary')->first()?->public_url,
            ])->values(),
        ];
    }
}
