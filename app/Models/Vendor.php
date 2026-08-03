<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Mass Assignable Attributes
     */
    protected $fillable = [
        'user_id',
        'store_name',
        'store_slug',
        'business_name',
        'gst_number',
        'pan_number',
        'description',
        'logo',
        'banner',
        'support_email',
        'support_phone',
        'status',
        'rejection_reason',
        'approved_at',
        'approved_by',
    ];

    /**
     * Attribute Casting
     */
    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Vendor Owner
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Approved By (Admin)
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Store Products
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Store Addresses
     */
    public function addresses()
    {
        return $this->hasMany(VendorAddress::class);
    }

    /**
     * Store Settings
     */
    public function settings()
    {
        return $this->hasOne(VendorSetting::class);
    }

    /**
     * Vendor Orders
     */
    public function subOrders()
    {
        return $this->hasMany(SubOrder::class);
    }

    /**
     * Vendor Settlements
     */
    public function settlements()
    {
        return $this->hasMany(VendorSettlement::class);
    }

    /**
     * Vendor Payouts
     */
    public function payouts()
    {
        return $this->hasMany(VendorPayout::class);
    }

    /**
 * Bank Account
 */
public function bankAccount()
{
    return $this->hasOne(VendorBankAccount::class);
}

/**
 * Documents
 */
public function documents()
{
    return $this->hasMany(VendorDocument::class);
}
    /**
     * Vendor Ledger
     */
    public function ledgers()
    {
        return $this->hasMany(VendorLedger::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsSuspendedAttribute(): bool
    {
        return $this->status === 'suspended';
    }
}
