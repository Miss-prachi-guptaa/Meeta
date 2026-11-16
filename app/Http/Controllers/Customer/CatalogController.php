<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\ProductCardResource;
use App\Http\Resources\Customer\ProductDetailResource;
use App\Models\Category;
use App\Repositories\Customer\HomeRepository;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function __construct(protected HomeRepository $products) {}

    public function index(Request $request)
    {
        return $this->execute(function () use ($request) {
            $filters = $request->validate([
                'search' => ['nullable', 'string', 'max:100'], 'category_id' => ['nullable', 'integer'],
                'brand_id' => ['nullable', 'integer'], 'sort' => ['nullable', 'in:latest,oldest,price_low,price_high'],
                'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            ]);
            $products = $this->products->getProducts($filters);

            return response()->json(['success' => true, 'message' => 'Products fetched successfully.', 'data' => [
                'items' => ProductCardResource::collection($products->getCollection())->resolve(),
                'pagination' => ['current_page' => $products->currentPage(), 'last_page' => $products->lastPage(), 'per_page' => $products->perPage(), 'total' => $products->total()],
            ]]);
        });
    }

    public function show(string $slug)
    {
        return $this->execute(function () use ($slug) {
            $visible = $this->products->visibleProductsQuery()
                ->with([
                    'variants' => fn ($query) => $query->active()->with(['inventory', 'attributeValues' => fn ($values) => $values->active()->with('attribute')]),
                    'reviews' => fn ($q) => $q->where('is_approved', true)->latest()->with('user:id,name'),
                ])
                ->where('slug', $slug)->first();
            if (! $visible) {
                return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
            }

            return response()->json(['success' => true, 'message' => 'Product fetched successfully.', 'data' => (new ProductDetailResource($visible))->resolve()]);
        });
    }

    public function categories()
    {
        return $this->execute(function () {
            $categories = Category::active()->root()->with(['childrenRecursive' => fn ($q) => $q->active()])->orderBy('sort_order')->get();

            return response()->json(['success' => true, 'message' => 'Categories fetched successfully.', 'data' => $categories]);
        });
    }

    public function suggestions(Request $request)
    {
        return $this->execute(function () use ($request) {
            $data = $request->validate(['q' => ['required', 'string', 'min:2', 'max:100'], 'limit' => ['nullable', 'integer', 'min:1', 'max:10']]);
            $items = $this->products->visibleProductsQuery()->where(fn ($q) => $q->where('name', 'like', '%'.$data['q'].'%')->orWhere('short_description', 'like', '%'.$data['q'].'%'))->limit($data['limit'] ?? 8)->get();

            return response()->json(['success' => true, 'message' => 'Suggestions fetched successfully.', 'data' => ProductCardResource::collection($items)->resolve()]);
        });
    }
}
