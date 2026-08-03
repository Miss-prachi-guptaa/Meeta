<?php

namespace App\Services\Vendor;

use App\Models\Product;
use App\Models\ProductImage;
use App\Repositories\Vendor\ProductImageRepository;
use App\Services\FileUploadService;
use Illuminate\Validation\ValidationException;

class ProductImageService
{
    public function __construct(
        protected ProductImageRepository $productImageRepository,
        protected FileUploadService $fileUploadService
    ) {}

    /**
     * Get Product Images
     */
    public function getAll(Product $product)
    {
        return $this->productImageRepository->getAll($product);
    }

    /**
     * Upload Product Images
     */
    public function upload(Product $product, array $files, ?string $altText = null)
    {
        $images = [];

        foreach ($files as $file) {

            $uploadedFile = $this->fileUploadService->upload(
                $file,
                'products'
            );

            $images[] = $this->productImageRepository->create([
                'product_id' => $product->id,
                'image_url' => $uploadedFile['file_path'],
                'alt_text' => $altText,
                'sort_order' => $this->productImageRepository->getNextSortOrder($product),
                'is_primary' => !$this->productImageRepository->hasPrimaryImage($product),
            ]);
        }

        return $images;
    }

    /**
     * Update Product Image
     */
    public function update(ProductImage $image, array $data): ProductImage
    {
        if (!empty($data['is_primary'])) {

            $this->productImageRepository->resetPrimary(
                $image->product
            );
        }

        return $this->productImageRepository->update(
            $image,
            $data
        );
    }

    /**
     * Delete Product Image
     */
    public function delete(ProductImage $image): bool
    {
        $this->fileUploadService->delete(
            $image->image_url
        );

        return $this->productImageRepository->delete(
            $image
        );
    }

    /**
     * Find Product Image
     */
    public function getById(int $id): ProductImage
    {
        $image = $this->productImageRepository->findById($id);

        if (!$image) {
            throw ValidationException::withMessages([
                'image' => ['Product image not found.'],
            ]);
        }

        return $image;
    }
}