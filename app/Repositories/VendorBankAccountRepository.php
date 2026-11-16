<?php

namespace App\Repositories;

use App\Models\Vendor;
use App\Models\VendorBankAccount;
use Illuminate\Validation\ValidationException;

class VendorBankAccountRepository
{
    public function getVendor($userId)
    {
        return Vendor::where('user_id', $userId)->firstOrFail();
    }

    public function show($userId)
    {
        $vendor = $this->getVendor($userId);

        return VendorBankAccount::where('vendor_id', $vendor->id)->first();
    }

    public function store($userId, array $data)
    {
        $vendor = $this->getVendor($userId);
        $this->ensureEditable($vendor);

        $exists = VendorBankAccount::where('vendor_id', $vendor->id)->exists();

        if ($exists) {
            throw new \Exception('Bank account already exists.');
        }

        $bank = VendorBankAccount::create([
            'vendor_id' => $vendor->id,
            ...$data,
        ]);

        return $bank->fresh();
    }

    public function update($userId, array $data)
    {
        $vendor = $this->getVendor($userId);
        $this->ensureEditable($vendor);

        $bank = VendorBankAccount::where('vendor_id', $vendor->id)
            ->firstOrFail();

        $bank->update([
            ...$data,
            'is_verified' => false,
            'verified_at' => null,
            'verified_by' => null,
        ]);

        return $bank->fresh();
    }

    private function ensureEditable(Vendor $vendor): void
    {
        if (! in_array($vendor->status, ['draft', 'rejected'], true)) {
            throw ValidationException::withMessages(['status' => ['Your submitted application cannot be edited unless changes are requested.']]);
        }
    }
}
