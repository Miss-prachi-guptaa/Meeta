<?php

namespace App\Services\Admin;

use App\Models\AttributeValue;
use App\Repositories\Admin\AttributeValueRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AttributeValueService
{
    public function __construct(
        protected AttributeValueRepository $attributeValueRepository
    ) {}

    public function getAttributeValues(array $filters)
    {
        return $this->attributeValueRepository->paginate($filters);
    }

    public function getAttributeValue(int $id)
    {
        return $this->attributeValueRepository->findById($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            if (
                $this->attributeValueRepository->existsByValue(
                    $data['attribute_id'],
                    $data['value']
                )
            ) {
                throw ValidationException::withMessages([
                    'value' => ['Attribute value already exists.'],
                ]);
            }

            $data['slug'] = $this->generateSlug(
                $data['attribute_id'],
                $data['value']
            );

            return $this->attributeValueRepository->create($data);
        });
    }

    public function update(AttributeValue $attributeValue, array $data)
    {
        return DB::transaction(function () use ($attributeValue, $data) {

            if (isset($data['value'])) {

                $attributeId = $data['attribute_id'] ?? $attributeValue->attribute_id;

                if (
                    $this->attributeValueRepository->existsByValue(
                        $attributeId,
                        $data['value'],
                        $attributeValue->id
                    )
                ) {
                    throw ValidationException::withMessages([
                        'value' => ['Attribute value already exists.'],
                    ]);
                }

                if ($data['value'] !== $attributeValue->value) {

                    $data['slug'] = $this->generateSlug(
                        $attributeId,
                        $data['value']
                    );
                }
            }

            $this->attributeValueRepository->update(
                $attributeValue,
                $data
            );

            return $attributeValue->refresh();
        });
    }

    public function updateStatus(
        AttributeValue $attributeValue,
        string $status
    ) {
        $this->attributeValueRepository->update(
            $attributeValue,
            [
                'status' => $status,
            ]
        );

        return $attributeValue->refresh();
    }

    public function delete(AttributeValue $attributeValue)
    {
        return $this->attributeValueRepository->delete($attributeValue);
    }

    private function generateSlug(
        int $attributeId,
        string $value
    ): string {

        $slug = Str::slug($value);
        $original = $slug;
        $counter = 1;

        while (
            $this->attributeValueRepository->existsBySlug(
                $attributeId,
                $slug
            )
        ) {
            $slug = $original . '-' . $counter++;
        }

        return $slug;
    }
}