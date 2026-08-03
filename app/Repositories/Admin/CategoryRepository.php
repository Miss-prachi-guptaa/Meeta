<?php

namespace App\Repositories\Admin;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Category::with('parent');

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['parent_id'])) {
            $query->where('parent_id', $filters['parent_id']);
        }

        $allowedSorts = ['name', 'sort_order', 'created_at'];

        $sortBy = in_array($filters['sort_by'] ?? '', $allowedSorts)
            ? $filters['sort_by']
            : 'sort_order';

        $sortOrder = strtolower($filters['sort_order'] ?? 'asc') === 'desc'
            ? 'desc'
            : 'asc';

        return $query->orderBy($sortBy, $sortOrder)
            ->paginate($filters['per_page'] ?? 15);
    }

    public function getTree(): Collection
    {
        return Category::root()
            ->with('childrenRecursive')
            ->orderBy('sort_order')
            ->get();
    }

    public function findById(int $id): ?Category
    {
        return Category::with(['parent', 'childrenRecursive'])->find($id);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }

    public function existsByName(string $name, ?int $parentId, ?int $ignoreId = null): bool
    {
        return Category::where('name', $name)
            ->where('parent_id', $parentId)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists();
    }

    public function existsBySlug(string $slug): bool
    {
        return Category::where('slug', $slug)->exists();
    }

    public function hasChildren(int $categoryId): bool
    {
        return Category::where('parent_id', $categoryId)->exists();
    }

    public function hasProducts(int $categoryId): bool
    {
        return Category::whereHas('products', function ($query) use ($categoryId) {
            $query->where('category_id', $categoryId);
        })->exists();
    }
}