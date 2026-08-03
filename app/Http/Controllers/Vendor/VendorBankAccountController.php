<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorBankAccountRequest;
use App\Services\VendorBankAccountService;
use Illuminate\Http\JsonResponse;

class VendorBankAccountController extends Controller
{
    public function __construct(
        protected VendorBankAccountService $vendorBankAccountService
    ) {}

    public function show(): JsonResponse
    {
        return $this->execute(function () {

            return response()->json([
                'success' => true,
                'data' => $this->vendorBankAccountService->show(auth()->user())
            ]);

        });
    }

    public function store(VendorBankAccountRequest $request): JsonResponse
    {
        return $this->execute(function () use ($request) {

            return response()->json([
                'success' => true,
                'message' => 'Bank account added successfully.',
                'data' => $this->vendorBankAccountService->store(
                    auth()->user(),
                    $request->validated()
                )
            ]);

        });
    }

    public function update(VendorBankAccountRequest $request): JsonResponse
    {
        return $this->execute(function () use ($request) {

            return response()->json([
                'success' => true,
                'message' => 'Bank account updated successfully.',
                'data' => $this->vendorBankAccountService->update(
                    auth()->user(),
                    $request->validated()
                )
            ]);

        });
    }
}