<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $quantity = (int) ($this->quantity ?? 0);
        $reservedQuantity = (int) ($this->reserved_quantity ?? 0);

        return [
            'id' => $this->id,

            'quantity' => $quantity,

            'reserved_quantity' => $reservedQuantity,

            'available_quantity' => max(0, $quantity - $reservedQuantity),

            'minimum_stock' => $this->minimum_stock,

            'maximum_stock' => $this->maximum_stock,
        ];
    }
}