<?php

namespace App\Repositories;

use App\Models\Vendor;
use App\Models\VendorBankAccount;

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

        $bank = VendorBankAccount::where('vendor_id', $vendor->id)
            ->firstOrFail();

        $bank->update($data);

        return $bank->fresh();
    }
}