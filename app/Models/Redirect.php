<?php

namespace App\Models;

use Database\Factories\RedirectFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $from
 * @property $to
 * @property $code
 */
class Redirect extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'redirects';

    public static $CODES = [
        '301',
        '302',
        '307',
    ];

    protected static function newFactory(): Factory
    {
        return RedirectFactory::new();
    }
}
