<?php

namespace App\Services\Vendor;

use App\Models\Product;
use App\Repositories\Vendor\ProductApprovalRepository;
use Illuminate\Validation\ValidationException;

class ProductApprovalService
{
    public function __construct(
        protected ProductApprovalRepository $productApprovalRepository
    ) {}

    /**
     * Submit Product For Approval
     */
    public function submit(
        Product $product
    ): Product {

        /*
        |--------------------------------------------------------------------------
        | Status Validation
        |--------------------------------------------------------------------------
        */

        if ($product->status === 'pending') {
            throw ValidationException::withMessages([
                'product' => [
                    'Product is already submitted for approval.',
                ],
            ]);
        }

        if ($product->status === 'active') {
            throw ValidationException::withMessages([
                'product' => [
                    'Product is already approved.',
                ],
            ]);
        }

        if (!in_array($product->status, ['draft', 'rejected'])) {
            throw ValidationException::withMessages([
                'product' => [
                    'Product cannot be submitted for approval.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Product Images
        |--------------------------------------------------------------------------
        */

        if (!$product->images()->exists()) {
            throw ValidationException::withMessages([
                'images' => [
                    'Please upload at least one product image.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Product Variants
        |--------------------------------------------------------------------------
        */

        $variants = $product->variants()->with('inventory')->get();

        if ($variants->isEmpty()) {
            throw ValidationException::withMessages([
                'variants' => [
                    'Please create at least one product variant.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Inventory Validation
        |--------------------------------------------------------------------------
        */

        foreach ($variants as $variant) {

            if (!$variant->inventory) {
                throw ValidationException::withMessages([
                    'inventory' => [
                        "Inventory is missing for variant SKU: {$variant->sku}.",
                    ],
                ]);
            }
        }

        return $this->productApprovalRepository
            ->submit($product);
    }
}