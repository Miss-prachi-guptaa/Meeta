<?php

namespace App\Repositories\Vendor;

use App\Models\Product;
use App\Models\ProductVariant;

class ProductVariantRepository
{
    /**
     * Get Product Variants
     */
    public function getAll(Product $product)
    {
        return ProductVariant::with(['images', 'attributeValues'])->where('product_id', $product->id)
            ->latest()
            ->get();
    }

    /**
     * Find Variant
     */
    public function findById(int $id): ?ProductVariant
    {
        return ProductVariant::find($id);
    }

    /**
     * Create Variant
     */
    public function create(array $data): ProductVariant
    {
        return ProductVariant::create($data);
    }

    /**
     * Update Variant
     */
    public function update(ProductVariant $variant, array $data): ProductVariant
    {
        $variant->update($data);

        return $variant->fresh();
    }

    /**
     * Delete Variant
     */
    public function delete(ProductVariant $variant): bool
    {
        return (bool) $variant->delete();
    }

    /**
     * Find By Barcode
     */
    public function findByBarcode(?string $barcode): ?ProductVariant
    {
        if (!$barcode) {
            return null;
        }

        return ProductVariant::where('barcode', $barcode)->first();
    }

    /**
     * SKU Exists
     */
    public function skuExists(string $sku): bool
    {
        return ProductVariant::where('sku', $sku)->exists();
    }
}