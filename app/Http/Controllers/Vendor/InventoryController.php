<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\Inventory\StoreInventoryRequest;
use App\Http\Requests\Vendor\Inventory\UpdateInventoryRequest;
use App\Http\Resources\Vendor\InventoryResource;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Vendor\InventoryService;
use App\Services\Vendor\ProductService;
use App\Services\Vendor\ProductVariantService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected ProductService $productService,
        protected ProductVariantService $productVariantService
    ) {}

    /**
     * Inventory Details
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

            $inventory = $this->inventoryService->getByVariant($variant);

            return response()->json([
                'success' => true,
                'message' => 'Inventory fetched successfully.',
                'data' => $inventory
                    ? new InventoryResource($inventory)
                    : null,
            ]);
        });
    }

    /**
     * Create Inventory
     */
    public function store(
        StoreInventoryRequest $request,
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

            $inventory = $this->inventoryService->create(
                $variant,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Inventory created successfully.',
                'data' => new InventoryResource($inventory),
            ]);
        });
    }

    /**
     * Update Inventory
     */
    public function update(
        UpdateInventoryRequest $request,
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

           $inventory = $this->inventoryService
    ->getOrFailByVariant($variant);
    
            $inventory = $this->inventoryService->update(
                $inventory,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Inventory updated successfully.',
                'data' => new InventoryResource($inventory),
            ]);
        });
    }
}