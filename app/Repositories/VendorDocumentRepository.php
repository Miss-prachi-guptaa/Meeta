<?php

namespace App\Repositories;

use App\Models\Vendor;
use App\Models\VendorDocument;
use App\Services\FileUploadService;

class VendorDocumentRepository
{
    public function __construct(
        protected FileUploadService $fileUploadService
    ) {}

    public function getVendor($userId)
    {
        return Vendor::where('user_id', $userId)->firstOrFail();
    }

    public function show($userId)
{
    $vendor = $this->getVendor($userId);

    return VendorDocument::where('vendor_id', $vendor->id)
        ->latest()
        ->get();
}

public function store($userId, array $data)
{
    $vendor = $this->getVendor($userId);

    $exists = VendorDocument::where('vendor_id', $vendor->id)
        ->where('document_type', $data['document_type'])
        ->exists();

    if ($exists) {
        throw new \Exception('Document already uploaded.');
    }

    $file = $this->fileUploadService->upload(
        $data['document'],
        'vendors/documents'
    );

    return VendorDocument::create([
        'vendor_id'       => $vendor->id,
        'document_type'   => $data['document_type'],
        'document_number' => $data['document_number'] ?? null,

        'file_name' => $file['file_name'],
        'file_path' => $file['file_path'],
        'mime_type' => $file['mime_type'],
        'file_size' => $file['file_size'],
    ])->fresh();
}

public function update($userId, $documentId, array $data)
{
    $vendor = $this->getVendor($userId);

    $document = VendorDocument::where('vendor_id', $vendor->id)
        ->where('id', $documentId)
        ->firstOrFail();

    // Document number update
    $document->document_number = $data['document_number'] ?? $document->document_number;

    // New file uploaded
    if (isset($data['document'])) {

        // Delete old file
        $this->fileUploadService->delete($document->file_path);

        // Upload new file
        $file = $this->fileUploadService->upload(
            $data['document'],
            'vendors/documents'
        );

        $document->file_name = $file['file_name'];
        $document->file_path = $file['file_path'];
        $document->mime_type = $file['mime_type'];
        $document->file_size = $file['file_size'];

        // Re-verification required
        $document->status = 'pending';
        $document->verified_at = null;
        $document->verified_by = null;
        $document->remarks = null;
    }

    $document->save();

    return $document->fresh();
}

public function delete($userId, $documentId)
{
    $vendor = $this->getVendor($userId);

    $document = VendorDocument::where('vendor_id', $vendor->id)
        ->where('id', $documentId)
        ->firstOrFail();

    $this->fileUploadService->delete($document->file_path);

    $document->delete();

    return true;
}
}