<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;

class VendorController extends Controller
{


    public function index(): JsonResponse
    {
        $vendors = Vendor::with('user')->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Vendor list fetched successfully.',
            'data' => $vendors
        ]);
    }



}