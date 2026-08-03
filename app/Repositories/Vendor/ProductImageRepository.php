<?php

namespace App\Repositories\Vendor;

use App\Models\Product;
use App\Models\ProductImage;

class ProductImageRepository
{
    /**
     * Get Product Images
     */
    public function getAll(Product $product)
    {
        return ProductImage::where('product_id', $product->id)
            ->ordered()
            ->get();
    }

    /**
     * Find Image
     */
    public function findById(int $id): ?ProductImage
    {
        return ProductImage::find($id);
    }

    /**
     * Create Image
     */
    public function create(array $data): ProductImage
    {
        return ProductImage::create($data);
    }

    /**
     * Update Image
     */
    public function update(ProductImage $image, array $data): ProductImage
    {
        $image->update($data);

        return $image->fresh();
    }

    /**
     * Delete Image
     */
    public function delete(ProductImage $image): bool
    {
        return (bool) $image->delete();
    }

    /**
     * Remove Existing Primary Image
     */
    public function resetPrimary(Product $product): void
    {
        ProductImage::where('product_id', $product->id)
            ->update([
                'is_primary' => false,
            ]);
    }

    /**
     * Get Next Sort Order
     */
    public function getNextSortOrder(Product $product): int
    {
        return (int) ProductImage::where('product_id', $product->id)
                ->max('sort_order') + 1;
    }

    /**
     * Check Primary Image Exists
     */
    public function hasPrimaryImage(Product $product): bool
    {
        return ProductImage::where('product_id', $product->id)
            ->where('is_primary', true)
            ->exists();
    }
}