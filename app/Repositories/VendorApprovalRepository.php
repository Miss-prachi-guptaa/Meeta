<?php

namespace App\Repositories;

use App\Models\Vendor;
use Exception;
use Illuminate\Support\Facades\DB;

class VendorApprovalRepository
{
    /**
     * Pending Vendors
     */
    public function pending()
    {
        return Vendor::with('user')
            ->where('status', 'pending')
            ->orderByDesc('submitted_at')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Approve Vendor
     */
    public function approve(int $vendorId, int $adminId)
    {
        return DB::transaction(function () use ($vendorId, $adminId) {

            $vendor = Vendor::with('user')->find($vendorId);

            if (! $vendor) {
                throw new Exception('Vendor not found.');
            }

            if ($vendor->status !== 'pending') {
                throw new Exception('Only pending vendors can be approved.');
            }

            $vendor->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $adminId,
            ]);

            if (! $vendor->user->hasRole('vendor')) {
                $vendor->user->assignRole('vendor');
            }

            return $vendor->fresh(['user']);
        });
    }

    /**
     * Vendor Details
     */
    public function show(int $vendorId)
    {
        $vendor = Vendor::with([
            'user',
            'addresses',
            'bankAccount',
            'documents',
        ])->find($vendorId);

        if (! $vendor) {
            throw new Exception('Vendor not found.');
        }

        return $vendor;
    }

    /**
     * Reject Vendor
     */
    public function reject(int $vendorId, int $adminId, string $reason)
    {
        return DB::transaction(function () use ($vendorId, $reason) {

            $vendor = Vendor::with('user')->find($vendorId);

            if (! $vendor) {
                throw new Exception('Vendor not found.');
            }

            if ($vendor->status !== 'pending') {
                throw new Exception('Only pending vendors can be rejected.');
            }

            $vendor->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'approved_at' => null,
                'approved_by' => null,
            ]);

            return [
                'vendor' => $vendor->fresh(),
                'reason' => $reason,
            ];
        });
    }

    /**
     * Suspend Vendor
     */
    public function suspend(int $vendorId)
    {
        $vendor = Vendor::find($vendorId);

        if (! $vendor) {
            throw new Exception('Vendor not found.');
        }

        if ($vendor->status !== 'approved') {
            throw new Exception('Only approved vendors can be suspended.');
        }

        $vendor->update([
            'status' => 'suspended',
        ]);

        return $vendor->fresh();
    }
}
