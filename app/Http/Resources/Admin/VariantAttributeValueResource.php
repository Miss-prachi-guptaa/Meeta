<?php

namespace App\Http\Resources\Admin;

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

            'attribute' => $this->whenLoaded('attribute', function () {
                return [
                    'id' => $this->attribute->id,
                    'name' => $this->attribute->name,
                ];
            }),

            'value' => $this->value,

            'slug' => $this->slug,

            'sort_order' => $this->sort_order,

            'status' => $this->status,
        ];
    }
}