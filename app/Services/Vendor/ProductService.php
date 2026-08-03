<?php

namespace App\Services\Vendor;

use App\Models\Product;
use App\Repositories\Vendor\ProductRepository;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductService
{
    public function __construct(
        protected ProductRepository $productRepository
    ) {}

    /**
     * Product Listing
     */
    public function getAll(int $vendorId)
    {
        return $this->productRepository->getAll($vendorId);
    }

    /**
     * Product Details
     */
    public function getById(int $id, int $vendorId): Product
    {
        $product = $this->productRepository->findById($id, $vendorId);

        if (! $product) {
            throw ValidationException::withMessages([
                'product' => ['Product not found.'],
            ]);
        }

        return $product;
    }

    /**
     * Create Product
     */
    public function create(array $data, int $vendorId): Product
    {
        if ($this->productRepository->findByName($data['name'], $vendorId)) {
            throw ValidationException::withMessages([
                'name' => ['Product name already exists.'],
            ]);
        }

        $data['vendor_id'] = $vendorId;
        $data['slug'] = $this->generateUniqueSlug($data['name']);
        $data['status'] = 'draft';

        return $this->productRepository->create($data);
    }

    /**
     * Update Product
     */
    public function update(Product $product, array $data, int $vendorId): Product
    {
        if (
            isset($data['name']) &&
            $data['name'] !== $product->name
        ) {
            $existingProduct = $this->productRepository->findByName(
                $data['name'],
                $vendorId
            );

            if ($existingProduct && $existingProduct->id !== $product->id) {
                throw ValidationException::withMessages([
                    'name' => ['Product name already exists.'],
                ]);
            }

            $data['slug'] = $this->generateUniqueSlug($data['name']);
        }

        return $this->productRepository->update($product, $data);
    }

    /**
     * Delete Product
     */
    public function delete(Product $product): bool
    {
        return $this->productRepository->delete($product);
    }

    /**
     * Generate Unique Slug
     */
    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }
}