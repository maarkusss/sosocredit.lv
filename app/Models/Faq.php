<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property $id
 * @property $lang
 * @property $question
 * @property $answer
 * @property $active
 * @property $order
 * @property $created_at
 * @property $updated_at
 *
 * @method active()
 */
class Faq extends Model
{
    protected $guarded = [];

    protected $table = 'faqs';

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }
}
