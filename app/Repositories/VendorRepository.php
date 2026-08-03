<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Vendor;

class VendorRepository
{
    public function createUser(array $data)
    {
        return User::create($data);
    }

    public function createVendor(array $data)
    {
        return Vendor::create($data);
    }

    public function getVendorByUserId($userId)
{
    return Vendor::where('user_id', $userId)->firstOrFail();
}

public function updateVendor($userId, array $data)
{
    $vendor = Vendor::where('user_id', $userId)->firstOrFail();

    $vendor->update([
        'store_name' => $data['store_name'] ?? $vendor->store_name,
        'store_slug' => isset($data['store_name'])
            ? \Illuminate\Support\Str::slug($data['store_name'])
            : $vendor->store_slug,
        'business_name' => $data['business_name'] ?? $vendor->business_name,
        'gst_number' => $data['gst_number'] ?? $vendor->gst_number,
        'pan_number' => $data['pan_number'] ?? $vendor->pan_number,
        'description' => $data['description'] ?? $vendor->description,
        'support_email' => $data['support_email'] ?? $vendor->support_email,
        'support_phone' => $data['support_phone'] ?? $vendor->support_phone,
    ]);

    return $vendor->fresh();
}
}