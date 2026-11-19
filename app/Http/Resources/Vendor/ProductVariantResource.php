<?php

namespace App\Http\Resources\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'product_id' => $this->product_id,

            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'name' => $this->name,

            'price' => $this->price,
            'sale_price' => $this->sale_price,

            'weight' => $this->weight,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,

            'is_active' => $this->is_active,

            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'primary_image' => $this->whenLoaded('images', function () {
                $img = $this->images->firstWhere('is_primary', true) ?? $this->images->first();
                return $img ? [
                    'id' => $img->id,
                    'image_url' => $img->public_url,
                    'alt_text' => $img->alt_text,
                ] : null;
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}