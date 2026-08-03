<?php

namespace App\Services;

use App\Repositories\VendorAddressRepository;

class VendorAddressService
{
    public function __construct(
        protected VendorAddressRepository $repository
    ) {}

    public function show($user)
    {
        return $this->repository->getAddress($user->id);
    }

    public function store($user, array $data)
    {
        return $this->repository->storeAddress($user->id, $data);
    }

    public function update($user, array $data)
    {
        return $this->repository->updateAddress($user->id, $data);
    }
}