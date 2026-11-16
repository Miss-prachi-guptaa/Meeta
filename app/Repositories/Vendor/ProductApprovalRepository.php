<?php

namespace App\Repositories\Vendor;

use App\Models\Product;

class ProductApprovalRepository
{
    /**
     * Submit Product For Approval
     */
    public function submit(
        Product $product
    ): Product {

        $product->update([
            'status' => 'pending',
            'approved_at' => null,
            'approved_by' => null,
            'rejection_reason' => null,
        ]);

        return $product->fresh();
    }
}