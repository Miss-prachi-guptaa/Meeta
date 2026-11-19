<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\ProductImage\StoreProductImageRequest;
use App\Http\Requests\Vendor\ProductImage\UpdateProductImageRequest;
use App\Http\Resources\Vendor\ProductImageResource;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\Vendor\ProductImageService;
use App\Services\Vendor\ProductService;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    public function __construct(
        protected ProductImageService $productImageService,
        protected ProductService $productService
    ) {}

    /**
     * Product Image Listing
     */
    public function index(Request $request, Product $product)
    {
        return $this->execute(function () use ($request, $product) {

            $product = $this->productService->getById(
                $product->id,
                $request->user()->vendor->id
            );

            $images = $this->productImageService->getAll($product);

            return response()->json([
                'success' => true,
                'message' => 'Product images fetched successfully.',
                'data' => ProductImageResource::collection($images),
            ]);
        });
    }

    /**
     * Upload Product Images
     */
    public function store(StoreProductImageRequest $request, Product $product)
    {
        return $this->execute(function () use ($request, $product) {

            $product = $this->productService->getById(
                $product->id,
                $request->user()->vendor->id
            );

            $images = $this->productImageService->upload(
                $product,
                $request->file('images'),
                $request->input('alt_text'),
                $request->input('product_variant_id') ? (int) $request->input('product_variant_id') : null
            );

            return response()->json([
                'success' => true,
                'message' => 'Product images uploaded successfully.',
                'data' => ProductImageResource::collection(collect($images)),
            ]);
        });
    }

    /**
     * Update Product Image
     */
    public function update(UpdateProductImageRequest $request, Product $product, ProductImage $image)
    {
        return $this->execute(function () use ($request, $product, $image) {

            $this->productService->getById(
                $product->id,
                $request->user()->vendor->id
            );

            $image = $this->productImageService->getById($image->id);

            $image = $this->productImageService->update(
                $image,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Product image updated successfully.',
                'data' => new ProductImageResource($image),
            ]);
        });
    }

    /**
     * Delete Product Image
     */
    public function destroy(Request $request, Product $product, ProductImage $image)
    {
        return $this->execute(function () use ($request, $product, $image) {

            $this->productService->getById(
                $product->id,
                $request->user()->vendor->id
            );

            $image = $this->productImageService->getById($image->id);

            $this->productImageService->delete($image);

            return response()->json([
                'success' => true,
                'message' => 'Product image deleted successfully.',
                'data' => null,
            ]);
        });
    }
}