<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorSettlement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'settlement_number',
        'vendor_id',
        'sub_order_id',
        'payment_id',
        'gross_amount',
        'commission_amount',
        'tax_amount',
        'shipping_amount',
        'refund_amount',
        'net_amount',
        'available_at',
        'status',
        'settled_at',
        'remarks',
    ];

    protected $casts = [
        'available_at' => 'datetime',
        'settled_at' => 'datetime',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function subOrder()
    {
        return $this->belongsTo(SubOrder::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function payouts()
    {
        return $this->hasMany(VendorPayout::class);
    }

}