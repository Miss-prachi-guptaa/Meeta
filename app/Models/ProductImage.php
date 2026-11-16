<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Mass Assignable Attributes
     */
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'image_url',
        'alt_text',
        'sort_order',
        'is_primary',
    ];

    /**
     * Attribute Casting
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_primary' => 'boolean',
        ];
    }

    public function getPublicUrlAttribute(): ?string
    {
        if (! $this->image_url) {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $this->image_url) || str_starts_with($this->image_url, '/')) {
            return $this->image_url;
        }

        return Storage::disk('public')->url($this->image_url);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Product Variant
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
