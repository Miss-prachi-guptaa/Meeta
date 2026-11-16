<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    /**
     * Mass Assignable Attributes
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'display_name',
        'email',
        'phone',
        'password',
        'status',
        'gender',
        'date_of_birth',
        'profile_image',
        'email_verified_at',
        'last_login_at',
    ];

    /**
     * Hidden Attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute Casting
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_of_birth' => 'date',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Vendor Profile
    public function vendor()
    {
        return $this->hasOne(Vendor::class);
    }

    // Customer Addresses
    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function preferences()
    {
        return $this->hasOne(CustomerPreference::class);
    }

    // Shopping Cart
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    // Wishlist
    public function wishlist()
    {
        return $this->hasOne(Wishlist::class);
    }

    // Orders
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Reviews
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Social Accounts
    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    // OTP Verifications
    public function otpVerifications()
    {
        return $this->hasMany(OtpVerification::class);
    }

    // Notifications
    public function userNotifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Audit Logs
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // Vendors Approved By This User (Admin)
    public function approvedVendors()
    {
        return $this->hasMany(Vendor::class, 'approved_by');
    }

    public function approvedProducts()
    {
        return $this->hasMany(Product::class, 'approved_by');
    }

    /**
     * Verified Vendor Documents
     */
    public function verifiedVendorDocuments()
    {
        return $this->hasMany(VendorDocument::class, 'verified_by');
    }

    /**
     * Verified Vendor Bank Accounts
     */
    public function verifiedVendorBankAccounts()
    {
        return $this->hasMany(VendorBankAccount::class, 'verified_by');
    }

    /**
     * Inventory Transactions
     */
    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'performed_by');
    }

    public function returns()
    {
        return $this->hasMany(ReturnModel::class);
    }

    public function processedPayouts()
    {
        return $this->hasMany(VendorPayout::class, 'processed_by');
    }
}
