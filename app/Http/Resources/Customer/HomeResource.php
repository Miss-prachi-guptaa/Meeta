<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'categories' => $this->categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'parent_id' => $category->parent_id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'image' => $category->public_image_url,
                    'sort_order' => $category->sort_order,
                ];
            })->values(),

            'best_selling' => ProductCardResource::collection($this->best_selling)->resolve(),
            'trending' => ProductCardResource::collection($this->trending)->resolve(),
            'new_arrivals' => ProductCardResource::collection($this->new_arrivals)->resolve(),
            'regions' => $this->regions,
            'collections' => $this->collections,
            'testimonials' => $this->testimonials,
            'articles' => $this->articles,

            'products' => [
                'data' => ProductCardResource::collection($this->products->getCollection())->resolve(),

                'current_page' => $this->products->currentPage(),
                'last_page' => $this->products->lastPage(),
                'per_page' => $this->products->perPage(),
                'total' => $this->products->total(),
                'from' => $this->products->firstItem(),
                'to' => $this->products->lastItem(),
            ],
        ];
    }
}
