<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Mass Assignable Attributes
     */
    protected $fillable = [
        'product_id',
        'sku',
        'barcode',
        'name',
        'price',
        'sale_price',
        'weight',
        'length',
        'width',
        'height',
        'is_active',
    ];

    /**
     * Attribute Casting
     */
    protected function casts(): array
    {
        return [
            'price'      => 'decimal:2',
            'sale_price' => 'decimal:2',
            'weight'     => 'decimal:2',
            'length'     => 'decimal:2',
            'width'      => 'decimal:2',
            'height'     => 'decimal:2',
            'is_active'  => 'boolean',
        ];
    }



    /**
     * Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems()
{
    return $this->hasMany(OrderItem::class);
}
    /**
 * Inventory
 */
public function inventory()
{
    return $this->hasOne(Inventory::class);
}
  
public function attributeValues()
{
    return $this->belongsToMany(
        AttributeValue::class,
        'variant_attribute_values'
    );
}

/**
 * Variant Images
 */
public function images()
{
    return $this->hasMany(ProductImage::class);
}

    /**
     * Active Variants
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
 * Cart Items
 */
public function cartItems()
{
    return $this->hasMany(CartItem::class);
}
 
}