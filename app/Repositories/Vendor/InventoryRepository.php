<?php

namespace App\Repositories\Vendor;

use App\Models\Inventory;
use App\Models\ProductVariant;

class InventoryRepository
{
    /**
     * Get Inventory By Variant
     */
    public function getByVariant(ProductVariant $variant): ?Inventory
    {
        return Inventory::where(
            'product_variant_id',
            $variant->id
        )->first();
    }

    /**
     * Create Inventory
     */
    public function create(array $data): Inventory
    {
        return Inventory::create($data);
    }

    /**
     * Update Inventory
     */
    public function update(
        Inventory $inventory,
        array $data
    ): Inventory {

        $inventory->update($data);

        return $inventory->fresh();
    }

    /**
     * Check Inventory Exists
     */
    public function exists(
        int $variantId
    ): bool {

        return Inventory::where(
            'product_variant_id',
            $variantId
        )->exists();
    }

    /**
     * Delete Inventory
     */
    public function delete(
        Inventory $inventory
    ): bool {

        return (bool) $inventory->delete();
    }
}