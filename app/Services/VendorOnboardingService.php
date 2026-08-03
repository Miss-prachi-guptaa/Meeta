<?php

namespace App\Services;

use App\Repositories\VendorOnboardingRepository;

class VendorOnboardingService
{
    public function __construct(
        protected VendorOnboardingRepository $repository
    ) {}

    public function submit($user)
    {
        return $this->repository->submit($user->id);
    }
}