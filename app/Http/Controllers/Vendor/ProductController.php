<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\Product\StoreProductRequest;
use App\Http\Requests\Vendor\Product\UpdateProductRequest;
use App\Http\Resources\Vendor\ProductResource;
use App\Models\Product;
use App\Services\Vendor\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    /**
     * Product Listing
     */
    public function index(Request $request)
    {
        return $this->execute(function () use ($request) {

            $products = $this->productService->getAll(
                $request->user()->vendor->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Products fetched successfully.',
                'data' => ProductResource::collection($products),
            ]);
        });
    }

    /**
     * Product Details
     */
    public function show(Request $request, Product $product)
    {
        return $this->execute(function () use ($request, $product) {

            $product = $this->productService->getById(
                $product->id,
                $request->user()->vendor->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Product fetched successfully.',
                'data' => new ProductResource($product),
            ]);
        });
    }

    /**
     * Create Product
     */
    public function store(StoreProductRequest $request)
    {
        return $this->execute(function () use ($request) {

            $product = $this->productService->create(
                $request->validated(),
                $request->user()->vendor->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'data' => new ProductResource($product),
            ]);
        });
    }

    /**
     * Update Product
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        return $this->execute(function () use ($request, $product) {

            $product = $this->productService->getById(
                $product->id,
                $request->user()->vendor->id
            );

            $product = $this->productService->update(
                $product,
                $request->validated(),
                $request->user()->vendor->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'data' => new ProductResource($product),
            ]);
        });
    }

    /**
     * Delete Product
     */
    public function destroy(Request $request, Product $product)
    {
        return $this->execute(function () use ($request, $product) {

            $product = $this->productService->getById(
                $product->id,
                $request->user()->vendor->id
            );

            $this->productService->delete($product);

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully.',
                'data' => null,
            ]);
        });
    }
}