<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\VariantAttributeValue\StoreVariantAttributeValueRequest;
use App\Http\Requests\Vendor\VariantAttributeValue\UpdateVariantAttributeValueRequest;
use App\Http\Resources\Vendor\VariantAttributeValueResource;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantAttributeValue;
use App\Services\Vendor\ProductService;
use App\Services\Vendor\ProductVariantService;
use App\Services\Vendor\VariantAttributeValueService;
use Illuminate\Http\Request;

class VariantAttributeValueController extends Controller
{
    public function __construct(
        protected VariantAttributeValueService $variantAttributeValueService,
        protected ProductService $productService,
        protected ProductVariantService $productVariantService
    ) {}

    /**
     * Variant Attribute Value Listing
     */
    public function index(
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

            $mappings = $this->variantAttributeValueService->getAll($variant);

            return response()->json([
                'success' => true,
                'message' => 'Variant attribute values fetched successfully.',
                'data' => VariantAttributeValueResource::collection($mappings),
            ]);
        });
    }

    /**
     * Create Mapping
     */
    public function store(
        StoreVariantAttributeValueRequest $request,
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

            $mapping = $this->variantAttributeValueService->create(
                $variant,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Variant attribute value added successfully.',
                'data' => new VariantAttributeValueResource($mapping),
            ]);
        });
    }

    /**
     * Update Mapping
     */
    public function update(
        UpdateVariantAttributeValueRequest $request,
        Product $product,
        ProductVariant $variant,
        VariantAttributeValue $attribute
    ) {
        return $this->execute(function () use ($request, $product, $variant, $attribute) {

            $product = $this->productService->getById(
                $product->id,
                $request->user()->vendor->id
            );

            $variant = $this->productVariantService->getById(
                $variant->id,
                $product->id
            );

            $attribute = $this->variantAttributeValueService->getById(
                $attribute->id,
                $variant->id
            );

            $attribute = $this->variantAttributeValueService->update(
                $attribute,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Variant attribute value updated successfully.',
                'data' => new VariantAttributeValueResource($attribute),
            ]);
        });
    }

    /**
     * Delete Mapping
     */
    public function destroy(
        Request $request,
        Product $product,
        ProductVariant $variant,
        VariantAttributeValue $attribute
    ) {
        return $this->execute(function () use ($request, $product, $variant, $attribute) {

            $product = $this->productService->getById(
                $product->id,
                $request->user()->vendor->id
            );

            $variant = $this->productVariantService->getById(
                $variant->id,
                $product->id
            );

            $attribute = $this->variantAttributeValueService->getById(
                $attribute->id,
                $variant->id
            );

            $this->variantAttributeValueService->delete($attribute);

            return response()->json([
                'success' => true,
                'message' => 'Variant attribute value deleted successfully.',
                'data' => null,
            ]);
        });
    }
}