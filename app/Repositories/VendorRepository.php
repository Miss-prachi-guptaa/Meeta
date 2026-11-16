<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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

        $this->ensureEditable($vendor);

        $vendor->update([
            'store_name' => $data['store_name'] ?? $vendor->store_name,
            'store_slug' => isset($data['store_name'])
                ? Str::slug($data['store_name'])
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

    private function ensureEditable(Vendor $vendor): void
    {
        if (! in_array($vendor->status, ['draft', 'rejected'], true)) {
            throw ValidationException::withMessages([
                'status' => [$vendor->status === 'pending'
                    ? 'Your application is under review and cannot be edited.'
                    : 'This application cannot be edited in its current status.'],
            ]);
        }
    }
}
