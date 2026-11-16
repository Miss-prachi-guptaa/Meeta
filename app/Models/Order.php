<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'shipping_full_name',
        'shipping_phone',
        'shipping_address_line_1',
        'shipping_address_line_2',
        'shipping_landmark',
        'shipping_city',
        'shipping_state',
        'shipping_country',
        'shipping_postal_code',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'shipping_amount',
        'total_amount',
        'payment_status',
        'order_status',
        'notes',
        'cancellation_reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subOrders()
    {
        return $this->hasMany(SubOrder::class);
    }

    public function orderItems()
    {
        return $this->hasManyThrough(OrderItem::class, SubOrder::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function shipments()
    {
        return $this->hasManyThrough(Shipment::class, SubOrder::class);
    }

    public function returns()
    {
        return $this->hasMany(ReturnModel::class, 'order_id');
    }
}
