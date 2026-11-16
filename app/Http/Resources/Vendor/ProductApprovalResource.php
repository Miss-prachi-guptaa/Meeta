<?php

namespace App\Http\Resources\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductApprovalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'name' => $this->name,

            'status' => $this->status,

            'approved_at' => $this->approved_at,

            'approved_by' => $this->approved_by,

            'rejection_reason' => $this->rejection_reason,

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,
        ];
    }
}