<?php

namespace App\Repositories\Admin;

use App\Models\Attribute;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AttributeRepository
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Attribute::query();

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

    public function findById(int $id): ?Attribute
    {
        return Attribute::find($id);
    }

    public function create(array $data): Attribute
    {
        return Attribute::create($data);
    }

    public function update(Attribute $attribute, array $data): bool
    {
        return $attribute->update($data);
    }

    public function delete(Attribute $attribute): bool
    {
        return $attribute->delete();
    }

    public function existsByName(string $name, ?int $ignoreId = null): bool
    {
        return Attribute::whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists();
    }

    public function existsBySlug(string $slug): bool
    {
        return Attribute::where('slug', $slug)->exists();
    }

    public function hasValues(int $attributeId): bool
    {
        return Attribute::whereHas('values')
            ->where('id', $attributeId)
            ->exists();
    }
}