<?php

namespace App\Services\Vendor;

use App\Models\AttributeValue;
use App\Models\ProductVariant;
use App\Models\VariantAttributeValue;
use App\Repositories\Vendor\VariantAttributeValueRepository;
use Illuminate\Validation\ValidationException;

class VariantAttributeValueService
{
    public function __construct(
        protected VariantAttributeValueRepository $variantAttributeValueRepository
    ) {}

    /**
     * Variant Attribute Value Listing
     */
    public function getAll(ProductVariant $variant)
    {
        return $this->variantAttributeValueRepository->getAll($variant);
    }

    /**
     * Variant Attribute Value Details
     */
    public function getById(
        int $id,
        int $variantId
    ): VariantAttributeValue {

        $mapping = $this->variantAttributeValueRepository
            ->findById($id);

        if (
            !$mapping ||
            $mapping->product_variant_id !== $variantId
        ) {
            throw ValidationException::withMessages([
                'mapping' => [
                    'Variant attribute value not found.',
                ],
            ]);
        }

        return $mapping;
    }

    /**
     * Create Mapping
     */
    public function create(
        ProductVariant $variant,
        array $data
    ): VariantAttributeValue {

        $attributeValue = AttributeValue::findOrFail(
            $data['attribute_value_id']
        );

        if (
            $this->variantAttributeValueRepository->exists(
                $variant->id,
                $attributeValue->id
            )
        ) {
            throw ValidationException::withMessages([
                'attribute_value_id' => [
                    'Attribute value already assigned.',
                ],
            ]);
        }

        if (
            $this->variantAttributeValueRepository->attributeExists(
                $variant->id,
                $attributeValue->attribute_id
            )
        ) {
            throw ValidationException::withMessages([
                'attribute_value_id' => [
                    'Only one value can be assigned for the same attribute.',
                ],
            ]);
        }

        return $this->variantAttributeValueRepository->create([
            'product_variant_id' => $variant->id,
            'attribute_value_id' => $attributeValue->id,
        ]);
    }

    /**
     * Update Mapping
     */
    public function update(
        VariantAttributeValue $mapping,
        array $data
    ): VariantAttributeValue {

        return $this->variantAttributeValueRepository->update(
            $mapping,
            $data
        );
    }

    /**
     * Delete Mapping
     */
    public function delete(
        VariantAttributeValue $mapping
    ): bool {

        return $this->variantAttributeValueRepository
            ->delete($mapping);
    }
}