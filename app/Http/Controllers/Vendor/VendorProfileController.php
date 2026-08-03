<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateVendorProfileRequest;
use App\Services\VendorProfileService;
use Illuminate\Http\JsonResponse;

class VendorProfileController extends Controller
{
    public function __construct(
    protected VendorProfileService $vendorProfileService
) {}
    //

    public function show(): JsonResponse
{
    return $this->execute(function () {

        $vendor = $this->vendorProfileService->show(auth()->user());

        return response()->json([
            'success' => true,
            'data' => $vendor
        ]);

    });
}

public function update(UpdateVendorProfileRequest $request): JsonResponse
{
    return $this->execute(function () use ($request) {

        $vendor = $this->vendorProfileService->update(
            auth()->user(),
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Vendor profile updated successfully.',
            'data' => $vendor
        ]);

    });
}
}
