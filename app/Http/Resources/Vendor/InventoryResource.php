<?php

namespace App\Http\Resources\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'product_variant_id' => $this->product_variant_id,

            'quantity' => $this->quantity,

            'reserved_quantity' => $this->reserved_quantity,

            'available_quantity' => max(
                0,
                $this->quantity - $this->reserved_quantity
            ),

            'minimum_stock' => $this->minimum_stock,

            'maximum_stock' => $this->maximum_stock,

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,
        ];
    }
}