<?php

namespace App\Services\Admin;

use App\Models\Product;
use App\Repositories\Admin\ProductApprovalRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProductApprovalService
{
    public function __construct(
        protected ProductApprovalRepository $repository
    ) {}

    /**
     * Get pending products.
     */
    public function getPendingProducts(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getPendingProducts($perPage);
    }

    /**
     * Get product details.
     */
    public function getProduct(int $productId): Product
    {
        $product = $this->repository->findById($productId);

        if (! $product) {
            throw new HttpException(404, 'Product not found.');
        }

        return $product;
    }

    /**
     * Approve product.
     */
    public function approveProduct(int $productId, int $adminId): Product
    {
        $product = $this->repository->findById($productId);

        if (! $product) {
            throw new HttpException(404, 'Product not found.');
        }

        if ($product->status !== 'pending') {
            throw new HttpException(
                422,
                'Only pending products can be approved.'
            );
        }

        return $this->repository->approve($product, $adminId);
    }

    /**
     * Reject product.
     */
    public function rejectProduct(int $productId, string $reason): Product
    {
        $product = $this->repository->findById($productId);

        if (! $product) {
            throw new HttpException(404, 'Product not found.');
        }

        if ($product->status !== 'pending') {
            throw new HttpException(
                422,
                'Only pending products can be rejected.'
            );
        }

        return $this->repository->reject($product, $reason);
    }
}