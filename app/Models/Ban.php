<?php

namespace App\Models;

use Database\Factories\BanFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $ip
 */
class Ban extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'bans';

    protected static function newFactory(): Factory
    {
        return BanFactory::new();
    }
}
