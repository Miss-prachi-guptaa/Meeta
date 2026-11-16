<?php

namespace App\Http\Resources\Admin;

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

            'sku' => $this->sku,

            'barcode' => $this->barcode,

            'name' => $this->name,

            'price' => $this->price,

            'sale_price' => $this->sale_price,

            'weight' => $this->weight,

            'length' => $this->length,

            'width' => $this->width,

            'height' => $this->height,

            'is_active' => (bool) $this->is_active,

            'inventory' => new InventoryResource(
                $this->whenLoaded('inventory')
            ),

            'attribute_values' => VariantAttributeValueResource::collection(
                $this->whenLoaded('attributeValues')
            ),

            'images' => ProductImageResource::collection(
                $this->whenLoaded('images')
            ),
        ];
    }
}