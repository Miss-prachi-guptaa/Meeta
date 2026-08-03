<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Services\VendorOnboardingService;
use Illuminate\Http\JsonResponse;

class VendorOnboardingController extends Controller
{
    public function __construct(
        protected VendorOnboardingService $vendorOnboardingService
    ) {}

    public function submit(): JsonResponse
    {
        return $this->execute(function () {

            return response()->json([
                'success' => true,
                'message' => 'Vendor profile submitted successfully for verification.',
                'data' => $this->vendorOnboardingService->submit(auth()->user())
            ]);

        });
    }
}