<?php

namespace App\Services;

use App\Repositories\VendorRepository;

class VendorProfileService
{
    public function __construct(
        protected VendorRepository $vendorRepository
    ) {}

    public function show($user)
    {
        return $this->vendorRepository->getVendorByUserId($user->id);
    }

    public function update($user, array $data)
    {
        return $this->vendorRepository->updateVendor($user->id, $data);
    }
}