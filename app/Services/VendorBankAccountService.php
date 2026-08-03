<?php

namespace App\Services;

use App\Repositories\VendorBankAccountRepository;

class VendorBankAccountService
{
    public function __construct(
        protected VendorBankAccountRepository $repository
    ) {}

    public function show($user)
    {
        return $this->repository->show($user->id);
    }

    public function store($user, array $data)
    {
        return $this->repository->store($user->id, $data);
    }

    public function update($user, array $data)
    {
        return $this->repository->update($user->id, $data);
    }
}