<?php

namespace App\Services\Admin;

use App\Models\Attribute;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Repositories\Admin\AttributeRepository;

class AttributeService
{
    public function __construct(
        protected AttributeRepository $attributeRepository
    ) {}

    public function getAttributes(array $filters)
    {
        return $this->attributeRepository->paginate($filters);
    }

    public function getAttribute(int $id)
    {
        return $this->attributeRepository->findById($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            if ($this->attributeRepository->existsByName($data['name'])) {
                throw ValidationException::withMessages([
                    'name' => ['Attribute already exists.'],
                ]);
            }

            $data['slug'] = $this->generateSlug($data['name']);

            return $this->attributeRepository->create($data);
        });
    }

    public function update(Attribute $attribute, array $data)
    {
        return DB::transaction(function () use ($attribute, $data) {

            if (isset($data['name'])) {

                if (
                    $this->attributeRepository->existsByName(
                        $data['name'],
                        $attribute->id
                    )
                ) {
                    throw ValidationException::withMessages([
                        'name' => ['Attribute already exists.'],
                    ]);
                }

                if ($data['name'] !== $attribute->name) {
                    $data['slug'] = $this->generateSlug($data['name']);
                }
            }

            $this->attributeRepository->update($attribute, $data);

            return $attribute->refresh();
        });
    }

    public function updateStatus(Attribute $attribute, string $status)
    {
        $this->attributeRepository->update($attribute, [
            'status' => $status,
        ]);

        return $attribute->refresh();
    }

    public function delete(Attribute $attribute)
    {
        if ($this->attributeRepository->hasValues($attribute->id)) {
            throw ValidationException::withMessages([
                'attribute' => ['Cannot delete attribute with values.'],
            ]);
        }

        return $this->attributeRepository->delete($attribute);
    }

    private function generateSlug(string $name): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $counter = 1;

        while ($this->attributeRepository->existsBySlug($slug)) {
            $slug = $original . '-' . $counter++;
        }

        return $slug;
    }
}