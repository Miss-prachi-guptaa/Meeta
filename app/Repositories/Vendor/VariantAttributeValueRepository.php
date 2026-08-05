<?php

namespace App\Repositories\Vendor;

use App\Models\ProductVariant;
use App\Models\VariantAttributeValue;

class VariantAttributeValueRepository
{
    /**
     * Get Variant Attribute Values
     */
    public function getAll(ProductVariant $variant)
    {
        return VariantAttributeValue::with([
                'attributeValue.attribute',
            ])
            ->where('product_variant_id', $variant->id)
            ->get();
    }

    /**
     * Find Mapping
     */
    public function findById(int $id): ?VariantAttributeValue
    {
        return VariantAttributeValue::with([
                'attributeValue.attribute',
            ])
            ->find($id);
    }

    /**
     * Create Mapping
     */
    public function create(array $data): VariantAttributeValue
    {
        return VariantAttributeValue::create($data);
    }

    /**
     * Update Mapping
     */
    public function update(
        VariantAttributeValue $mapping,
        array $data
    ): VariantAttributeValue {

        $mapping->update($data);

        return $mapping->fresh([
            'attributeValue.attribute',
        ]);
    }

    /**
     * Delete Mapping
     */
    public function delete(
        VariantAttributeValue $mapping
    ): bool {

        return (bool) $mapping->delete();
    }

    /**
     * Check Duplicate Mapping
     */
    public function exists(
        int $variantId,
        int $attributeValueId
    ): bool {

        return VariantAttributeValue::where(
                'product_variant_id',
                $variantId
            )
            ->where(
                'attribute_value_id',
                $attributeValueId
            )
            ->exists();
    }

    /**
     * Check Attribute Already Assigned
     */
    public function attributeExists(
        int $variantId,
        int $attributeId
    ): bool {

        return VariantAttributeValue::where(
                'product_variant_id',
                $variantId
            )
            ->whereHas('attributeValue', function ($query) use ($attributeId) {

                $query->where(
                    'attribute_id',
                    $attributeId
                );

            })
            ->exists();
    }
}