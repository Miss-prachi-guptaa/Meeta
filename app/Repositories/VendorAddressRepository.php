<?php

namespace App\Repositories;

use App\Models\Vendor;
use App\Models\VendorAddress;

class VendorAddressRepository
{
    public function getVendor($userId)
    {
        return Vendor::where('user_id', $userId)->firstOrFail();
    }

    public function getAddress($userId)
    {
        $vendor = $this->getVendor($userId);

        return VendorAddress::where('vendor_id', $vendor->id)->get();
    }

    public function storeAddress($userId, array $data)
    {
        $vendor = $this->getVendor($userId);

        return VendorAddress::create([
            'vendor_id' => $vendor->id,
            ...$data
        ]);
    }

    public function updateAddress($userId, array $data)
    {
        $vendor = $this->getVendor($userId);

        $address = VendorAddress::where('vendor_id', $vendor->id)
            ->where('type', $data['type'])
            ->firstOrFail();

        $address->update($data);

        return $address->fresh();
    }
}