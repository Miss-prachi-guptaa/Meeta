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

            // Used by vendor product lists so a seller can identify items quickly.
            'primary_image' => $this->whenLoaded('images', function () {
                $image = $this->images->firstWhere('is_primary', true) ?? $this->images->first();

                return $image ? [
                    'id' => $image->id,
                    'image_url' => $image->public_url,
                    'alt_text' => $image->alt_text,
                ] : null;
            }),

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
