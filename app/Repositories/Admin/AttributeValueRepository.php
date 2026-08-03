<?php

namespace App\Repositories\Admin;

use App\Models\AttributeValue;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AttributeValueRepository
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = AttributeValue::with('attribute');

        if (!empty($filters['search'])) {
            $query->where('value', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['attribute_id'])) {
            $query->where('attribute_id', $filters['attribute_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $allowedSorts = ['value', 'sort_order', 'created_at'];

        $sortBy = in_array($filters['sort_by'] ?? '', $allowedSorts)
            ? $filters['sort_by']
            : 'sort_order';

        $sortOrder = strtolower($filters['sort_order'] ?? 'asc') === 'desc'
            ? 'desc'
            : 'asc';

        return $query
            ->orderBy($sortBy, $sortOrder)
            ->paginate($filters['per_page'] ?? 15);
    }

    public function findById(int $id): ?AttributeValue
    {
        return AttributeValue::with('attribute')->find($id);
    }

    public function create(array $data): AttributeValue
    {
        return AttributeValue::create($data);
    }

    public function update(AttributeValue $attributeValue, array $data): bool
    {
        return $attributeValue->update($data);
    }

    public function delete(AttributeValue $attributeValue): bool
    {
        return $attributeValue->delete();
    }

    public function existsByValue(
        int $attributeId,
        string $value,
        ?int $ignoreId = null
    ): bool {
        return AttributeValue::where('attribute_id', $attributeId)
            ->whereRaw('LOWER(value) = ?', [strtolower($value)])
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists();
    }

    public function existsBySlug(
        int $attributeId,
        string $slug
    ): bool {
        return AttributeValue::where('attribute_id', $attributeId)
            ->where('slug', $slug)
            ->exists();
    }
}