<?php

namespace App\Services\Admin;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Repositories\Admin\CategoryRepository;
use App\Services\FileUploadService;
use Illuminate\Validation\ValidationException;

class CategoryService
{
    public function __construct(
        protected CategoryRepository $categoryRepository,
        protected FileUploadService $fileUploadService
    ) {}

    public function getCategories(array $filters)
    {
        return $this->categoryRepository->paginate($filters);
    }

    public function getCategoryTree()
    {
        return $this->categoryRepository->getTree();
    }

    public function getCategory(int $id)
    {
        return $this->categoryRepository->findById($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            if ($this->categoryRepository->existsByName($data['name'], $data['parent_id'] ?? null)) {
                throw new \Exception('Category already exists.');
            }

            $data['slug'] = $this->generateSlug($data['name']);

          if (!empty($data['image'])) {
    $upload = $this->fileUploadService->upload(
        $data['image'],
        'categories'
    );

    $data['image'] = $upload['file_path'];
}

            return $this->categoryRepository->create($data);
        });
    }


public function update(Category $category, array $data)
{
    return DB::transaction(function () use ($category, $data) {

        // Prevent self parent
        if (
            array_key_exists('parent_id', $data) &&
            $data['parent_id'] == $category->id
        ) {
            throw ValidationException::withMessages([
                'parent_id' => ['Category cannot be its own parent.'],
            ]);
        }

        // Duplicate name check
        if (isset($data['name'])) {

            if (
                $this->categoryRepository->existsByName(
                    $data['name'],
                    $data['parent_id'] ?? $category->parent_id,
                    $category->id
                )
            ) {
                throw ValidationException::withMessages([
                    'name' => ['Category already exists.'],
                ]);
            }

            // Generate slug only when name changes
            if ($data['name'] !== $category->name) {
                $data['slug'] = $this->generateSlug($data['name']);
            }
        }

        // Upload new image
        if (!empty($data['image'])) {

            if ($category->image) {
                $this->fileUploadService->delete($category->image);
            }

            $upload = $this->fileUploadService->upload(
                $data['image'],
                'categories'
            );

            $data['image'] = $upload['file_path'];
        }

        // Update category
        $this->categoryRepository->update($category, $data);

        return $category->refresh();
    });
}

    public function delete(Category $category)
    {
        if ($this->categoryRepository->hasChildren($category->id)) {
            throw new \Exception('Child categories exist.');
        }

        if ($this->categoryRepository->hasProducts($category->id)) {
            throw new \Exception('Category contains products.');
        }

        return $this->categoryRepository->delete($category);
    }

    public function updateStatus(Category $category, bool $status)
    {
        $this->categoryRepository->update($category, [
            'is_active' => $status
        ]);

        return $category->refresh();
    }

    private function generateSlug(string $name): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $counter = 1;

        while ($this->categoryRepository->existsBySlug($slug)) {
            $slug = "{$original}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}