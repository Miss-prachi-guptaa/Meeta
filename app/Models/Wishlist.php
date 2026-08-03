<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    /**
     * Mass Assignable Attributes
     */
    protected $fillable = [
        'user_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Customer
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Wishlist Items
     */
    public function items()
    {
        return $this->hasMany(WishlistItem::class);
    }
}