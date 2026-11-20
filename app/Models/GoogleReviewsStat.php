<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleReviewsStat extends Model
{
    protected $table = 'google_reviews_stats';

    protected $fillable = [
        'overall_rating',
        'ratings_total',
    ];
}
