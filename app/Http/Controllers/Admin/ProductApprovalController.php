<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectProductRequest;
use App\Http\Resources\Admin\ProductApprovalResource;
use App\Services\Admin\ProductApprovalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductApprovalController extends Controller
{
    public function __construct(
        protected ProductApprovalService $service
    ) {}

    /**
     * Get pending products.
     */
    public function index(Request $request): JsonResponse
    {
        $products = $this->service->getPendingProducts(
            $request->integer('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'message' => 'Pending products fetched successfully.',
            'data' => ProductApprovalResource::collection($products),
        ]);
    }

    /**
     * Get product details.
     */
    public function show(int $product): JsonResponse
    {
        $product = $this->service->getProduct($product);

        return response()->json([
            'success' => true,
            'message' => 'Product details fetched successfully.',
            'data' => new ProductApprovalResource($product),
        ]);
    }

    /**
     * Approve product.
     */
    public function approve(int $product): JsonResponse
    {
        $product = $this->service->approveProduct(
            $product,
            auth()->id()
        );

        return response()->json([
            'success' => true,
            'message' => 'Product approved successfully.',
            'data' => new ProductApprovalResource($product),
        ]);
    }

    /**
     * Reject product.
     */
    public function reject(
        RejectProductRequest $request,
        int $product
    ): JsonResponse {
        $product = $this->service->rejectProduct(
            $product,
            $request->rejection_reason
        );

        return response()->json([
            'success' => true,
            'message' => 'Product rejected successfully.',
            'data' => new ProductApprovalResource($product),
        ]);
    }
}