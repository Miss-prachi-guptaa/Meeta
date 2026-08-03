<?php

namespace App\Repositories\Vendor;

use App\Models\Product;

class ProductRepository
{
    /**
     * Get Vendor Products
     */
    public function getAll(int $vendorId)
    {
        return Product::with(['category', 'brand'])
            ->where('vendor_id', $vendorId)
            ->latest()
            ->paginate();
    }

    /**
     * Get Product By Id
     */
    public function findById(int $id, int $vendorId): ?Product
    {
        return Product::with(['category', 'brand'])
            ->where('vendor_id', $vendorId)
            ->find($id);
    }

    /**
     * Find Product By Name
     */
    public function findByName(string $name, int $vendorId): ?Product
    {
        return Product::where('vendor_id', $vendorId)
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->first();
    }

    /**
     * Create Product
     */
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    /**
     * Update Product
     */
    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->fresh(['category', 'brand']);
    }

    /**
     * Delete Product
     */
    public function delete(Product $product): bool
    {
        return (bool) $product->delete();
    }
}