<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'product_variant_id',
        'transaction_type',
        'quantity',
        'reference_type',
        'reference_id',
        'remarks',
        'performed_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Inventory
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    /**
     * Product Variant
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Performed By
     */
    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}