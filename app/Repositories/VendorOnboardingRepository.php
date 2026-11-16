<?php

namespace App\Repositories;

use App\Models\Vendor;
use App\Models\VendorAddress;
use App\Models\VendorBankAccount;
use App\Models\VendorDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VendorOnboardingRepository
{
    public function submit($userId)
    {
        $vendor = Vendor::where('user_id', $userId)->firstOrFail();

        if (! in_array($vendor->status, ['draft', 'rejected'], true)) {
            throw ValidationException::withMessages([
                'status' => [$vendor->status === 'pending'
                    ? 'Your application is already under review.'
                    : 'Only draft or rejected applications can be submitted.'],
            ]);
        }

        // Profile Check
        if (
            empty($vendor->store_name) ||
            empty($vendor->business_name)
        ) {
            throw ValidationException::withMessages(['profile' => ['Complete vendor profile first.']]);
        }

        // Address Check
        if (! VendorAddress::where('vendor_id', $vendor->id)->exists()) {
            throw ValidationException::withMessages(['address' => ['Vendor address is required.']]);
        }

        // Bank Check
        if (! VendorBankAccount::where('vendor_id', $vendor->id)->exists()) {
            throw ValidationException::withMessages(['bank_account' => ['Vendor bank account is required.']]);
        }

        // Required Documents
        $requiredDocuments = [
            'pan',
            'aadhaar',
            'cancelled_cheque',
        ];

        foreach ($requiredDocuments as $type) {

            $document = VendorDocument::where('vendor_id', $vendor->id)
                ->where('document_type', $type)
                ->latest()
                ->first();

            if (! $document) {
                throw ValidationException::withMessages(['documents' => [strtoupper($type).' document is required.']]);
            }

            if ($document->status === 'rejected') {
                throw ValidationException::withMessages(['documents' => [strtoupper($type).' document must be corrected before reapplying.']]);
            }
        }

        DB::transaction(function () use ($vendor) {
            $vendor->update([
                'status' => 'pending',
                'submitted_at' => now(),
                'rejection_reason' => null,
                'approved_at' => null,
                'approved_by' => null,
            ]);
        });

        return $vendor->fresh();
    }
}
