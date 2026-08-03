<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryStatusRequest;
use App\Http\Resources\Admin\CategoryResource;
use App\Models\Category;
use App\Services\Admin\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

  public function index(Request $request)
{
    return $this->execute(function () use ($request) {

        $categories = $this->categoryService->getCategories($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Categories fetched successfully.',
            'data' => CategoryResource::collection($categories),
        ]);
    });
}

    public function tree()
{
    return $this->execute(function () {

        $categories = $this->categoryService->getCategoryTree();

        return response()->json([
            'success' => true,
            'message' => 'Category tree fetched successfully.',
            'data' => CategoryResource::collection($categories),
        ]);
    });
}

 public function show(Category $category)
{
    return $this->execute(function () use ($category) {

        $category = $this->categoryService->getCategory($category->id);

        return response()->json([
            'success' => true,
            'message' => 'Category details fetched successfully.',
            'data' => new CategoryResource($category),
        ]);
    });
}
   public function store(StoreCategoryRequest $request)
{
    return $this->execute(function () use ($request) {

        $category = $this->categoryService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'data' => new CategoryResource($category),
        ], 201);
    });
}
    public function update(UpdateCategoryRequest $request, Category $category)
{
    return $this->execute(function () use ($request, $category) {

        $category = $this->categoryService->update(
            $category,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.',
            'data' => new CategoryResource($category),
        ]);
    });
}

public function destroy(Category $category)
{
    return $this->execute(function () use ($category) {

        $this->categoryService->delete($category);

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.',
        ]);
    });
}

   public function updateStatus(UpdateCategoryStatusRequest $request, Category $category)
{
    return $this->execute(function () use ($request, $category) {

        $category = $this->categoryService->updateStatus(
            $category,
            $request->boolean('is_active')
        );

        return response()->json([
            'success' => true,
            'message' => 'Category status updated successfully.',
            'data' => new CategoryResource($category),
        ]);
    });
}
}