<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleReview extends Model
{
    protected $fillable = [
        'google_review_id',
        'author_name',
        'author_url',
        'profile_photo_url',
        'rating',
        'text',
        'lang',
        'review_time',
    ];

    protected $casts = [
        'review_time' => 'datetime',
    ];
}
