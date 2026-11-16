<?php

namespace App\Repositories\Admin;

use App\Models\Product;

class ProductApprovalRepository
{
    /**
     * Common relations for admin product approval.
     */
    private array $relations = [
        'vendor',
        'category',
        'brand',
        'approvedBy',
        'images',
        'variants.inventory',
        'variants.images',
        'variants.attributeValues.attribute',
    ];

    /**
     * Get all pending products.
     */
    public function getPendingProducts($perPage = 15)
    {
        return Product::with($this->relations)
            ->pending()
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Find product by id.
     */
    public function findById(int $productId): ?Product
    {
        return Product::with($this->relations)
            ->find($productId);
    }

    /**
     * Approve product.
     */
    public function approve(Product $product, int $adminId): Product
    {
        $product->update([
            'status' => 'active',
            'approved_at' => now(),
            'approved_by' => $adminId,
            'rejection_reason' => null,
        ]);

        return $product->fresh($this->relations);
    }

    /**
     * Reject product.
     */
    public function reject(Product $product, string $reason): Product
    {
        $product->update([
            'status' => 'rejected',
            'approved_at' => null,
            'approved_by' => null,
            'rejection_reason' => $reason,
        ]);

        return $product->fresh($this->relations);
    }
}