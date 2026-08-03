<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorSetting extends Model
{
    use HasFactory;

    /**
     * Mass Assignable Attributes
     */
    protected $fillable = [
        'vendor_id',
        'accept_orders',
        'auto_accept_orders',
        'vacation_mode',
        'processing_time',
        'email_notifications',
        'sms_notifications',
    ];

    /**
     * Attribute Casting
     */
    protected function casts(): array
    {
        return [
            'accept_orders'       => 'boolean',
            'auto_accept_orders'  => 'boolean',
            'vacation_mode'       => 'boolean',
            'processing_time'     => 'integer',
            'email_notifications' => 'boolean',
            'sms_notifications'   => 'boolean',
        ];
    }

    /**
     * Vendor
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}