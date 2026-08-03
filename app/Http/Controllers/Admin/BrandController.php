<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBrandRequest;
use App\Http\Requests\Admin\UpdateBrandRequest;
use App\Http\Requests\Admin\UpdateBrandStatusRequest;
use App\Http\Resources\Admin\BrandResource;
use App\Models\Brand;
use App\Services\Admin\BrandService;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function __construct(
        protected BrandService $brandService
    ) {}

    public function index(Request $request)
    {
        return $this->execute(function () use ($request) {

            $brands = $this->brandService->getBrands($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Brands fetched successfully.',
                'data' => BrandResource::collection($brands),
            ]);
        });
    }

    public function show(Brand $brand)
    {
        return $this->execute(function () use ($brand) {

            $brand = $this->brandService->getBrand($brand->id);

            return response()->json([
                'success' => true,
                'message' => 'Brand fetched successfully.',
                'data' => new BrandResource($brand),
            ]);
        });
    }

    public function store(StoreBrandRequest $request)
    {
        return $this->execute(function () use ($request) {

            $brand = $this->brandService->create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Brand created successfully.',
                'data' => new BrandResource($brand),
            ], 201);
        });
    }

    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        return $this->execute(function () use ($request, $brand) {

            $brand = $this->brandService->update(
                $brand,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Brand updated successfully.',
                'data' => new BrandResource($brand),
            ]);
        });
    }

    public function updateStatus(UpdateBrandStatusRequest $request, Brand $brand)
    {
        return $this->execute(function () use ($request, $brand) {

            $brand = $this->brandService->updateStatus(
                $brand,
                $request->status
            );

            return response()->json([
                'success' => true,
                'message' => 'Brand status updated successfully.',
                'data' => new BrandResource($brand),
            ]);
        });
    }

    public function destroy(Brand $brand)
    {
        return $this->execute(function () use ($brand) {

            $this->brandService->delete($brand);

            return response()->json([
                'success' => true,
                'message' => 'Brand deleted successfully.',
            ]);
        });
    }
}