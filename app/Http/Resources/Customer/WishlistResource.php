<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WishlistResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'items_count' => $this->items->count(), 'items' => $this->items->map(function ($item) use ($request) {
            $product = $item->productVariant->product;
            $product->setRelation('variants', collect([$item->productVariant]));
            $card = (new ProductCardResource($product))->toArray($request);

            return ['id' => $item->id, 'variant_id' => $item->product_variant_id, 'product' => $card];
        })];
    }
}
