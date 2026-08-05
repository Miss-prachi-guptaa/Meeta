<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\ProductVariant\StoreProductVariantRequest;
use App\Http\Requests\Vendor\ProductVariant\UpdateProductVariantRequest;
use App\Http\Resources\Vendor\ProductVariantResource;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Vendor\ProductService;
use App\Services\Vendor\ProductVariantService;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function __construct(
        protected ProductVariantService $productVariantService,
        protected ProductService $productService
    ) {}

    /**
     * Variant Listing
     */
    public function index(Request $request, Product $product)
    {
        return $this->execute(function () use ($request, $product) {

            $product = $this->productService->getById(
                $product->id,
                $request->user()->vendor->id
            );

            $variants = $this->productVariantService->getAll($product);

            return response()->json([
                'success' => true,
                'message' => 'Product variants fetched successfully.',
                'data' => ProductVariantResource::collection($variants),
            ]);
        });
    }

    /**
     * Variant Details
     */
    public function show(
        Request $request,
        Product $product,
        ProductVariant $variant
    ) {
        return $this->execute(function () use ($request, $product, $variant) {

            $product = $this->productService->getById(
                $product->id,
                $request->user()->vendor->id
            );

            $variant = $this->productVariantService->getById(
                $variant->id,
                $product->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Product variant fetched successfully.',
                'data' => new ProductVariantResource($variant),
            ]);
        });
    }

    /**
     * Create Variant
     */
    public function store(
        StoreProductVariantRequest $request,
        Product $product
    ) {
        return $this->execute(function () use ($request, $product) {

            $product = $this->productService->getById(
                $product->id,
                $request->user()->vendor->id
            );

            $variant = $this->productVariantService->create(
                $request->validated(),
                $product
            );

            return response()->json([
                'success' => true,
                'message' => 'Product variant created successfully.',
                'data' => new ProductVariantResource($variant),
            ]);
        });
    }

    /**
     * Update Variant
     */
    public function update(
        UpdateProductVariantRequest $request,
        Product $product,
        ProductVariant $variant
    ) {
        return $this->execute(function () use ($request, $product, $variant) {

            $product = $this->productService->getById(
                $product->id,
                $request->user()->vendor->id
            );

            $variant = $this->productVariantService->getById(
                $variant->id,
                $product->id
            );

            $variant = $this->productVariantService->update(
                $variant,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Product variant updated successfully.',
                'data' => new ProductVariantResource($variant),
            ]);
        });
    }

    /**
     * Delete Variant
     */
    public function destroy(
        Request $request,
        Product $product,
        ProductVariant $variant
    ) {
        return $this->execute(function () use ($request, $product, $variant) {

            $product = $this->productService->getById(
                $product->id,
                $request->user()->vendor->id
            );

            $variant = $this->productVariantService->getById(
                $variant->id,
                $product->id
            );

            $this->productVariantService->delete($variant);

            return response()->json([
                'success' => true,
                'message' => 'Product variant deleted successfully.',
                'data' => null,
            ]);
        });
    }
}