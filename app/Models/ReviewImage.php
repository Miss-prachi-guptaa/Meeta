<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReviewImage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'review_id',
        'image_path',
        'mime_type',
        'file_size',
        'alt_text',
        'sort_order',
    ];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }
}