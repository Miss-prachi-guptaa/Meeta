<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorPayout extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payout_number',
        'vendor_id',
        'vendor_settlement_id',
        'amount',
        'payment_method',
        'transaction_reference',
        'status',
        'processed_at',
        'processed_by',
        'gateway_response',
        'remarks',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'gateway_response' => 'array',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function settlement()
    {
        return $this->belongsTo(VendorSettlement::class, 'vendor_settlement_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}