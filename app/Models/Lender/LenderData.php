<?php

namespace App\Models\Lender;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $slogan
 * @property array $benefits
 */
class LenderData extends Model
{
    use BelongsToLender;

    protected $guarded = [];
    protected $casts = [
        'benefits' => 'array',
    ];
    protected $table = 'lenders__data';
}
