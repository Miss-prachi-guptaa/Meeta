<?php

namespace App\Services\Admin;

use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\FileUploadService;
use App\Repositories\Admin\BrandRepository;

class BrandService
{
    public function __construct(
        protected BrandRepository $brandRepository,
        protected FileUploadService $fileUploadService
    ) {}

    public function getBrands(array $filters)
    {
        return $this->brandRepository->paginate($filters);
    }

    public function getBrand(int $id)
    {
        return $this->brandRepository->findById($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            if ($this->brandRepository->existsByName($data['name'])) {
                throw ValidationException::withMessages([
                    'name' => ['Brand already exists.'],
                ]);
            }

            $data['slug'] = $this->generateSlug($data['name']);

            if (!empty($data['logo'])) {

                $upload = $this->fileUploadService->upload(
                    $data['logo'],
                    'brands'
                );

                $data['logo'] = $upload['file_path'];
            }

            return $this->brandRepository->create($data);
        });
    }

    public function update(Brand $brand, array $data)
    {
        return DB::transaction(function () use ($brand, $data) {

            if (isset($data['name'])) {

                if (
                    $this->brandRepository->existsByName(
                        $data['name'],
                        $brand->id
                    )
                ) {
                    throw ValidationException::withMessages([
                        'name' => ['Brand already exists.'],
                    ]);
                }

                if ($data['name'] !== $brand->name) {
                    $data['slug'] = $this->generateSlug($data['name']);
                }
            }

            if (!empty($data['logo'])) {

                if ($brand->logo) {
                    $this->fileUploadService->delete($brand->logo);
                }

                $upload = $this->fileUploadService->upload(
                    $data['logo'],
                    'brands'
                );

                $data['logo'] = $upload['file_path'];
            }

            $this->brandRepository->update($brand, $data);

            return $brand->refresh();
        });
    }

    public function updateStatus(Brand $brand, string $status)
    {
        $this->brandRepository->update($brand, [
            'status' => $status,
        ]);

        return $brand->refresh();
    }

    public function delete(Brand $brand)
    {
        if ($this->brandRepository->hasProducts($brand->id)) {
            throw ValidationException::withMessages([
                'brand' => ['Cannot delete brand with products.'],
            ]);
        }

        if ($brand->logo) {
            $this->fileUploadService->delete($brand->logo);
        }

        return $this->brandRepository->delete($brand);
    }

    private function generateSlug(string $name): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $counter = 1;

        while ($this->brandRepository->existsBySlug($slug)) {
            $slug = $original . '-' . $counter++;
        }

        return $slug;
    }
}