<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorRegisterRequest;
use App\Services\VendorService;
use Illuminate\Http\JsonResponse;

class VendorAuthController extends Controller
{
    public function __construct(
        protected VendorService $vendorService
    ) {}

    public function register(VendorRegisterRequest $request): JsonResponse
    {
        try {

            $result = $this->vendorService->register($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Vendor application submitted successfully.',
                'data' => $result
            ], 201);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }
}