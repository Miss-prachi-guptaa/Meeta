<?php

namespace App\Http\Resources\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VariantAttributeValueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'product_variant_id' => $this->product_variant_id,

            'attribute_value' => [
                'id' => $this->attributeValue->id,
                'value' => $this->attributeValue->value,
                'attribute' => [
                    'id' => $this->attributeValue->attribute->id,
                    'name' => $this->attributeValue->attribute->name,
                ],
            ],

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}