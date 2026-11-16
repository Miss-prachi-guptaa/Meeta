<?php

namespace App\Http\Resources\Admin;

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

            'vendor' => $this->whenLoaded('vendor', function () {
                return [
                    'id' => $this->vendor->id,
                    'business_name' => $this->vendor->business_name,
                ];
            }),

            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ];
            }),

            'brand' => $this->whenLoaded('brand', function () {
                return [
                    'id' => $this->brand->id,
                    'name' => $this->brand->name,
                ];
            }),

            'name' => $this->name,

            'slug' => $this->slug,

            'short_description' => $this->short_description,

            'description' => $this->description,

            'meta_title' => $this->meta_title,

            'meta_description' => $this->meta_description,

            'status' => $this->status,

            'approved_at' => $this->approved_at,

            'approved_by' => $this->whenLoaded('approvedBy', function () {
    return [
        'id' => $this->approvedBy->id,
        'name' => trim(
            $this->approvedBy->display_name
            ?: $this->approvedBy->first_name . ' ' . $this->approvedBy->last_name
        ),
    ];
}),

            'rejection_reason' => $this->rejection_reason,

            'images' => ProductImageResource::collection(
                $this->whenLoaded('images')
            ),

            'variants' => ProductVariantResource::collection(
                $this->whenLoaded('variants')
            ),

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,
        ];
    }
}