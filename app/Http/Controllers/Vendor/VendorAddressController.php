<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorAddressRequest;
use App\Services\VendorAddressService;
use Illuminate\Http\JsonResponse;

class VendorAddressController extends Controller
{
    public function __construct(
        protected VendorAddressService $vendorAddressService
    ) {}

    public function show(): JsonResponse
    {
        return $this->execute(function () {

            return response()->json([
                'success' => true,
                'data' => $this->vendorAddressService->show(auth()->user())
            ]);

        });
    }

    public function store(VendorAddressRequest $request): JsonResponse
    {
        return $this->execute(function () use ($request) {

            return response()->json([
                'success' => true,
                'message' => 'Address added successfully.',
                'data' => $this->vendorAddressService->store(
                    auth()->user(),
                    $request->validated()
                )
            ]);

        });
    }

    public function update(VendorAddressRequest $request): JsonResponse
    {
        return $this->execute(function () use ($request) {

            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully.',
                'data' => $this->vendorAddressService->update(
                    auth()->user(),
                    $request->validated()
                )
            ]);

        });
    }
}