<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $signature
 * @property $description
 * @property $active
 * @property $settings
 * @property $created_at
 * @property $updated_at
 */
class Cron extends Model
{
    protected $table = 'crons';

    protected $fillable = [
        'description',
        'active',
        'settings',
    ];

    public $timestamps = true;

    protected $casts = [
        'settings' => 'array',
    ];
}
