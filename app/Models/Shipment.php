<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shipment_number',
        'sub_order_id',
        'provider',
        'shipping_partner',
        'tracking_number',
        'tracking_url',
        'status',
        'package_weight',
        'package_length',
        'package_width',
        'package_height',
        'shipped_at',
        'expected_delivery_at',
        'delivered_at',
        'remarks',
    ];

    protected $casts = [
        'package_weight' => 'decimal:3',
        'package_length' => 'decimal:2',
        'package_width' => 'decimal:2',
        'package_height' => 'decimal:2',
        'shipped_at' => 'datetime',
        'expected_delivery_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function subOrder()
    {
        return $this->belongsTo(SubOrder::class);
    }

    public function tracking()
    {
        return $this->hasMany(ShipmentTracking::class);
    }
}
