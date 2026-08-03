<?php

namespace App\Repositories;

use App\Models\Vendor;
use App\Models\VendorAddress;
use App\Models\VendorBankAccount;
use App\Models\VendorDocument;

class VendorOnboardingRepository
{
    public function submit($userId)
    {
        $vendor = Vendor::where('user_id', $userId)->firstOrFail();

        // Profile Check
        if (
            empty($vendor->store_name) ||
            empty($vendor->business_name)
        ) {
            throw new \Exception('Complete vendor profile first.');
        }

        // Address Check
        if (!VendorAddress::where('vendor_id', $vendor->id)->exists()) {
            throw new \Exception('Vendor address is required.');
        }

        // Bank Check
        if (!VendorBankAccount::where('vendor_id', $vendor->id)->exists()) {
            throw new \Exception('Vendor bank account is required.');
        }

        // Required Documents
        $requiredDocuments = [
            'pan',
            'aadhaar',
        ];

        foreach ($requiredDocuments as $type) {

            if (!VendorDocument::where('vendor_id', $vendor->id)
                ->where('document_type', $type)
                ->exists()) {

                throw new \Exception(strtoupper($type).' document is required.');
            }
        }

        $vendor->status = 'pending';

        $vendor->save();

        return $vendor->fresh();
    }
}