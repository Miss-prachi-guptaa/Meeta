<?php

namespace App\Services;

use App\Repositories\VendorDocumentRepository;

class VendorDocumentService
{
    public function __construct(
        protected VendorDocumentRepository $repository
    ) {}

    public function show($user)
    {
        return $this->repository->show($user->id);
    }

    public function store($user, array $data)
    {
        return $this->repository->store($user->id, $data);
    }

    public function update($user, $documentId, array $data)
    {
        return $this->repository->update($user->id, $documentId, $data);
    }

    public function delete($user, $documentId)
    {
        return $this->repository->delete($user->id, $documentId);
    }
}