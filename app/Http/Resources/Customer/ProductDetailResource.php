<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $optionGroups = $this->variants->flatMap->attributeValues
            ->filter(fn ($value) => $value->attribute)
            ->groupBy('attribute_id')
            ->map(function ($values) {
                $attribute = $values->first()->attribute;

                return [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'slug' => $attribute->slug,
                    'values' => $values->unique('id')->sortBy('sort_order')->map(fn ($value) => [
                        'id' => $value->id,
                        'value' => $value->value,
                        'slug' => $value->slug,
                    ])->values(),
                ];
            })->sortBy('id')->values();

        return array_merge((new ProductCardResource($this->resource))->toArray($request), [
            'description' => $this->description,
            'images' => $this->images->map(fn ($image) => ['id' => $image->id, 'url' => $image->public_url, 'alt_text' => $image->alt_text, 'is_primary' => $image->is_primary]),
            'variants' => $this->variants->map(fn ($variant) => [
                'id' => $variant->id, 'name' => $variant->name, 'sku' => $variant->sku,
                'price' => (float) $variant->price, 'sale_price' => $variant->sale_price !== null ? (float) $variant->sale_price : null,
                'effective_price' => $this->effectivePrice($variant),
                'in_stock' => $variant->inventory && ($variant->inventory->quantity - $variant->inventory->reserved_quantity) > 0,
                'available_quantity' => max(0, ($variant->inventory?->quantity ?? 0) - ($variant->inventory?->reserved_quantity ?? 0)),
                'attributes' => $variant->attributeValues->filter(fn ($value) => $value->attribute)->map(fn ($value) => [
                    'attribute_id' => $value->attribute_id,
                    'attribute' => $value->attribute->name,
                    'attribute_slug' => $value->attribute->slug,
                    'value_id' => $value->id,
                    'value' => $value->value,
                    'value_slug' => $value->slug,
                ])->values(),
            ]),
            'option_groups' => $optionGroups,
            'reviews' => $this->reviews->map(fn ($review) => ['id' => $review->id, 'rating' => $review->rating, 'title' => $review->title, 'text' => $review->review_text, 'verified' => $review->is_verified_purchase, 'customer' => $review->user?->name, 'created_at' => $review->created_at]),
        ]);
    }

    private function effectivePrice($variant): float
    {
        $price = (float) $variant->price;
        $sale = $variant->sale_price !== null ? (float) $variant->sale_price : null;

        return $sale !== null && $sale > 0 && $sale < $price ? $sale : $price;
    }
}
