<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $variant = $this->variants->sortBy(fn ($item) => $this->effectivePrice($item))->first();
        $image = $this->images->sortByDesc('is_primary')->sortBy('sort_order')->first();

        return [
            'id' => $this->id, 'name' => $this->name, 'slug' => $this->slug,
            'short_description' => $this->short_description,
            'category' => $this->category ? ['id' => $this->category->id, 'name' => $this->category->name, 'slug' => $this->category->slug] : null,
            'brand' => $this->brand ? ['id' => $this->brand->id, 'name' => $this->brand->name, 'slug' => $this->brand->slug] : null,
            'artisan' => $this->vendor ? ['id' => $this->vendor->id, 'name' => $this->vendor->store_name, 'slug' => $this->vendor->store_slug, 'logo' => $this->vendor->logo] : null,
            'image' => $image?->public_url,
            'price' => $variant?->price, 'sale_price' => $variant?->sale_price,
            'variant_id' => $variant?->id,
            'effective_price' => $variant ? $this->effectivePrice($variant) : null,
            'rating' => $this->rating !== null ? round((float) $this->rating, 1) : null,
            'reviews_count' => (int) ($this->reviews_count ?? 0),
            'sold_quantity' => (int) ($this->sold_quantity ?? 0),
        ];
    }

    private function effectivePrice($variant): float
    {
        $price = (float) $variant->price;
        $sale = $variant->sale_price !== null ? (float) $variant->sale_price : null;

        return $sale !== null && $sale > 0 && $sale < $price ? $sale : $price;
    }
}
