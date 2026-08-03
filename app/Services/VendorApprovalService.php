<?php

namespace App\Services;

use App\Repositories\VendorApprovalRepository;

class VendorApprovalService
{
    public function __construct(
        protected VendorApprovalRepository $repository
    ) {}

    /**
     * Pending Vendors
     */
    public function pending()
    {
        return $this->repository->pending();
    }

    /**
     * Approve Vendor
     */
    public function approve(int $vendorId, $admin)
    {
        return $this->repository->approve(
            $vendorId,
            $admin->id
        );
    }

    /**
 * Vendor Details
 */
public function show(int $vendorId)
{
    return $this->repository->show($vendorId);
}

/**
 * Reject Vendor
 */
public function reject(int $vendorId, $admin, string $reason)
{
    return $this->repository->reject(
        $vendorId,
        $admin->id,
        $reason
    );
}

/**
 * Suspend Vendor
 */
public function suspend(int $vendorId)
{
    return $this->repository->suspend($vendorId);
}
}