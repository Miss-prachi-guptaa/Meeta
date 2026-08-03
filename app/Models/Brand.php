<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Mass Assignable Attributes
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'website',
        'status',
    ];

    /**
     * Attribute Casting
     */
  protected function casts(): array
{
    return [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

    /**
     * Products
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Active Brands
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Inactive Brands
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}