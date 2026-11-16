<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $items = $this->items->map(function ($item) {
            $variant = $item->productVariant;
            $product = $variant->product;
            $image = $product->images->sortByDesc('is_primary')->sortBy('sort_order')->first();

            return [
                'id' => $item->id, 'quantity' => $item->quantity, 'unit_price' => $item->unit_price,
                'line_total' => round((float) $item->unit_price * $item->quantity, 2),
                'variant' => ['id' => $variant->id, 'name' => $variant->name, 'sku' => $variant->sku,
                    'available_quantity' => max(0, ($variant->inventory?->quantity ?? 0) - ($variant->inventory?->reserved_quantity ?? 0))],
                'product' => ['id' => $product->id, 'name' => $product->name, 'slug' => $product->slug, 'image' => $image?->public_url,
                    'artisan' => $product->vendor?->store_name],
            ];
        });

        return ['id' => $this->id, 'items' => $items, 'items_count' => $items->sum('quantity'),
            'subtotal' => round($items->sum('line_total'), 2)];
    }
}
