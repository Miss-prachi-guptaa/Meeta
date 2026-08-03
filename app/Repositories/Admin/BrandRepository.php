<?php

namespace App\Repositories\Admin;

use App\Models\Brand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BrandRepository
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Brand::query();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $allowedSorts = ['name', 'created_at'];

        $sortBy = in_array($filters['sort_by'] ?? '', $allowedSorts)
            ? $filters['sort_by']
            : 'created_at';

        $sortOrder = strtolower($filters['sort_order'] ?? 'desc') === 'asc'
            ? 'asc'
            : 'desc';

        return $query
            ->orderBy($sortBy, $sortOrder)
            ->paginate($filters['per_page'] ?? 15);
    }

    public function findById(int $id): ?Brand
    {
        return Brand::find($id);
    }

    public function create(array $data): Brand
    {
        return Brand::create($data);
    }

    public function update(Brand $brand, array $data): bool
    {
        return $brand->update($data);
    }

    public function delete(Brand $brand): bool
    {
        return $brand->delete();
    }

    public function existsByName(string $name, ?int $ignoreId = null): bool
    {
        return Brand::where('name', $name)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists();
    }

    public function existsBySlug(string $slug): bool
    {
        return Brand::where('slug', $slug)->exists();
    }

    public function hasProducts(int $brandId): bool
    {
        return Brand::whereHas('products')->where('id', $brandId)->exists();
    }
}