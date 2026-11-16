<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\HomeResource;
use App\Services\Customer\HomeService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        protected HomeService $homeService
    ) {}

    /**
     * Customer Home
     *
     * Public endpoint.
     */
    public function index(Request $request)
    {
        return $this->execute(function () use ($request) {

            $filters = $request->validate([
                'search' => ['nullable', 'string', 'max:100'],
                'category_id' => ['nullable', 'integer'],
                'brand_id' => ['nullable', 'integer'],
                'sort' => ['nullable', 'in:latest,oldest,price_low,price_high'],
                'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            ]);

            $homeData = $this->homeService->getHomeData(
                $filters
            );

            $resource = new HomeResource(
                (object) $homeData
            );

            return response()->json([
                'success' => true,
                'message' => 'Home data fetched successfully.',
                'data' => $resource->toArray($request),
            ]);
        });
    }
}
