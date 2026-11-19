<?php

namespace App\Services\Vendor;

use App\Models\Inventory;
use App\Models\ProductVariant;
use App\Repositories\Vendor\InventoryRepository;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function __construct(
        protected InventoryRepository $inventoryRepository
    ) {}

    /**
     * Get Inventory
     */
    public function getByVariant(
        ProductVariant $variant
    ): ?Inventory {

        return $this->inventoryRepository
            ->getByVariant($variant);
    }

    /**
     * Create Inventory
     */
    public function create(
        ProductVariant $variant,
        array $data
    ): Inventory {

        if (
            $this->inventoryRepository->exists(
                $variant->id
            )
        ) {
            throw ValidationException::withMessages([
                'inventory' => [
                    'Inventory already exists for this variant.',
                ],
            ]);
        }

        // Default null values to 0 for database integrity
        $data['reserved_quantity'] = (int) ($data['reserved_quantity'] ?? 0);
        $data['minimum_stock'] = (int) ($data['minimum_stock'] ?? 0);
        if (empty($data['maximum_stock'])) {
            $data['maximum_stock'] = null;
        }

        $this->validateStock($data);

        $data['product_variant_id'] = $variant->id;

        return $this->inventoryRepository->create($data);
    }

    /**
     * Update Inventory
     */
    public function update(
        Inventory $inventory,
        array $data
    ): Inventory {

        $data = array_merge(
            $inventory->toArray(),
            $data
        );

        $data['reserved_quantity'] = (int) ($data['reserved_quantity'] ?? 0);
        $data['minimum_stock'] = (int) ($data['minimum_stock'] ?? 0);
        if (empty($data['maximum_stock'])) {
            $data['maximum_stock'] = null;
        }

        $this->validateStock($data);

        return $this->inventoryRepository->update(
            $inventory,
            $data
        );
    }

    /**
     * Validate Inventory
     */
    protected function validateStock(
        array $data
    ): void {

        $quantity = (int) ($data['quantity'] ?? 0);

        $reserved = (int) ($data['reserved_quantity'] ?? 0);

        $minimum = (int) ($data['minimum_stock'] ?? 0);

        $maximum = $data['maximum_stock'] ?? null;

        if ($reserved > $quantity) {
            throw ValidationException::withMessages([
                'reserved_quantity' => [
                    'Reserved quantity cannot exceed total quantity.',
                ],
            ]);
        }

        if (
            !is_null($maximum) &&
            $maximum < $minimum
        ) {
            throw ValidationException::withMessages([
                'maximum_stock' => [
                    'Maximum stock must be greater than or equal to minimum stock.',
                ],
            ]);
        }
    }

    /**
 * Get Inventory Or Fail
 */
public function getOrFailByVariant(
    ProductVariant $variant
): Inventory {

    $inventory = $this->inventoryRepository
        ->getByVariant($variant);

    if (!$inventory) {
        throw ValidationException::withMessages([
            'inventory' => [
                'Inventory not found.',
            ],
        ]);
    }

    return $inventory;
}
}