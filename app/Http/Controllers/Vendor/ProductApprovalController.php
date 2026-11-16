<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\Vendor\ProductApprovalResource;
use App\Models\Product;
use App\Services\Vendor\ProductApprovalService;
use App\Services\Vendor\ProductService;
use Illuminate\Http\Request;

class ProductApprovalController extends Controller
{
    public function __construct(
        protected ProductApprovalService $productApprovalService,
        protected ProductService $productService
    ) {}

    /**
     * Submit Product For Approval
     */
    public function submit(
        Request $request,
        Product $product
    ) {
        return $this->execute(function () use ($request, $product) {

            $product = $this->productService->getById(
                $product->id,
                $request->user()->vendor->id
            );

            $product = $this->productApprovalService
                ->submit($product);

            return response()->json([
                'success' => true,
                'message' => 'Product submitted for approval successfully.',
                'data' => new ProductApprovalResource($product),
            ]);
        });
    }
}