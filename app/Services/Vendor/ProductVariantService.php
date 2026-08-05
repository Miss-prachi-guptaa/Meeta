<?php

namespace App\Services\Vendor;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Repositories\Vendor\ProductVariantRepository;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductVariantService
{
    public function __construct(
        protected ProductVariantRepository $productVariantRepository
    ) {}

    /**
     * Variant Listing
     */
    public function getAll(Product $product)
    {
        return $this->productVariantRepository->getAll($product);
    }

    /**
     * Variant Details
     */
    public function getById(int $id, int $productId): ProductVariant
    {
        $variant = $this->productVariantRepository->findById($id);

        if (!$variant || $variant->product_id !== $productId) {
            throw ValidationException::withMessages([
                'variant' => ['Product variant not found.'],
            ]);
        }

        return $variant;
    }

    /**
     * Create Variant
     */
    public function create(array $data, Product $product): ProductVariant
    {
        if (!empty($data['barcode'])) {

            $barcode = $this->productVariantRepository
                ->findByBarcode($data['barcode']);

            if ($barcode) {
                throw ValidationException::withMessages([
                    'barcode' => ['Barcode already exists.'],
                ]);
            }
        }

        $data['product_id'] = $product->id;
        $data['sku'] = $this->generateSku();

        return $this->productVariantRepository->create($data);
    }

    /**
     * Update Variant
     */
    public function update(ProductVariant $variant, array $data): ProductVariant
    {
        if (
            !empty($data['barcode']) &&
            $data['barcode'] !== $variant->barcode
        ) {

            $barcode = $this->productVariantRepository
                ->findByBarcode($data['barcode']);

            if ($barcode) {
                throw ValidationException::withMessages([
                    'barcode' => ['Barcode already exists.'],
                ]);
            }
        }

        return $this->productVariantRepository->update(
            $variant,
            $data
        );
    }

    /**
     * Delete Variant
     */
    public function delete(ProductVariant $variant): bool
    {
        return $this->productVariantRepository->delete($variant);
    }

    /**
     * Generate SKU
     */
    protected function generateSku(): string
    {
        do {

            $sku = 'SKU' . strtoupper(Str::random(8));

        } while (
            $this->productVariantRepository->skuExists($sku)
        );

        return $sku;
    }
}