<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPreference extends Model
{
    protected $fillable = ['order_updates', 'promotional_emails', 'artisan_stories', 'language', 'currency'];

    protected $casts = [
        'order_updates' => 'boolean',
        'promotional_emails' => 'boolean',
        'artisan_stories' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
