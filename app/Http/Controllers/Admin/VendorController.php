<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;

class VendorController extends Controller
{
    public function index(): JsonResponse
    {
        // Review queue first: pending vendors are ordered by their latest submit/reapply time.
        $vendors = Vendor::with('user')
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderByRaw('COALESCE(submitted_at, created_at) DESC')
            ->orderByDesc('id')
            ->paginate(100);

        return response()->json([
            'success' => true,
            'message' => 'Vendor list fetched successfully.',
            'data' => $vendors,
        ]);
    }
}
