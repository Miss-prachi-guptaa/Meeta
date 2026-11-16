<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\VendorApprovalService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Admin\RejectVendorRequest;

class VendorApprovalController extends Controller
{
    public function __construct(
        protected VendorApprovalService $vendorApprovalService
    ) {}

    /**
     * Pending Vendors
     */
    public function pending(): JsonResponse
    {
        return $this->execute(function () {

            return response()->json([
                'success' => true,
                'data' => $this->vendorApprovalService->pending()
            ]);

        });
    }

    /**
     * Approve Vendor
     */
    public function approve(int $id): JsonResponse
    {
        return $this->execute(function () use ($id) {

            $vendor = $this->vendorApprovalService->approve(
                $id,
                auth()->user()
            );

            return response()->json([
                'success' => true,
                'message' => 'Vendor approved successfully.',
                'data' => $vendor
            ]);

        });
    }

    /**
 * Vendor Details
 */
public function show(int $id): JsonResponse
{
    return $this->execute(function () use ($id) {

        return response()->json([
            'success' => true,
            'data' => $this->vendorApprovalService->show($id)
        ]);

    });
}
public function reject(
    RejectVendorRequest $request,
    int $id
): JsonResponse {
    return $this->execute(function () use ($request, $id) {

        $vendor = $this->vendorApprovalService->reject(
            $id,
            auth()->user(),
            $request->rejection_reason
        );

        return response()->json([
            'success' => true,
            'message' => 'Vendor rejected successfully.',
            'data' => $vendor
        ]);
    });
}

public function suspend(int $id): JsonResponse
{
    return $this->execute(function () use ($id) {

        $vendor = $this->vendorApprovalService->suspend($id);

        return response()->json([
            'success' => true,
            'message' => 'Vendor suspended successfully.',
            'data' => $vendor
        ]);

    });
}
}