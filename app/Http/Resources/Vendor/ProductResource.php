<?php

namespace App\Http\Resources\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'vendor' => [
                'id' => $this->vendor?->id,
                'business_name' => $this->vendor?->business_name,
            ],

            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
            ],

            'brand' => $this->brand ? [
                'id' => $this->brand->id,
                'name' => $this->brand->name,
            ] : null,

            'name' => $this->name,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'description' => $this->description,

            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,

            'status' => $this->status,
            'approved_at' => $this->approved_at,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}