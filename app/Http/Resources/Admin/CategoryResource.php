<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'parent_name' => $this->parent?->name,

            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image' => $this->public_image_url,

            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,

            'children' => CategoryResource::collection(
                $this->whenLoaded('childrenRecursive')
            ),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
