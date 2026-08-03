<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVendorDocumentRequest;
use App\Http\Requests\UpdateVendorDocumentRequest;
use App\Services\VendorDocumentService;
use Illuminate\Http\JsonResponse;

class VendorDocumentController extends Controller
{
    public function __construct(
        protected VendorDocumentService $vendorDocumentService
    ) {}

    public function index(): JsonResponse
    {
        return $this->execute(function () {

            return response()->json([
                'success' => true,
                'data' => $this->vendorDocumentService->show(auth()->user())
            ]);

        });
    }

    public function store(StoreVendorDocumentRequest $request): JsonResponse
    {
        return $this->execute(function () use ($request) {

            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully.',
                'data' => $this->vendorDocumentService->store(
                    auth()->user(),
                    $request->validated()
                )
            ], 201);

        });
    }

    public function update(UpdateVendorDocumentRequest $request, $id): JsonResponse
    {
        return $this->execute(function () use ($request, $id) {

            return response()->json([
                'success' => true,
                'message' => 'Document updated successfully.',
                'data' => $this->vendorDocumentService->update(
                    auth()->user(),
                    $id,
                    $request->validated()
                )
            ]);

        });
    }

    public function destroy($id): JsonResponse
    {
        return $this->execute(function () use ($id) {

            $this->vendorDocumentService->delete(auth()->user(), $id);

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully.'
            ]);

        });
    }
}