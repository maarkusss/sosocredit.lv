<?php

namespace App\Models\News;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property $id
 * @property $name
 * @property $lang
 * @property $active
 * @property $order
 * @property $created_at
 * @property $updated_at
 * @property $deleted_at
 *
 * @method allCategories()
 */
class NewsCategory extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $table = 'news_categories';

    public function news(): HasMany
    {
        return $this->hasMany(News::class, 'category_id');
    }

    public function scopeAllCategories($query)
    {
        return $query->where('lang', app()->getLocale())
            ->orderBy('order', 'ASC')
            ->get(['id', 'name'])
            ->keyBy('id')
            ->map(function ($item) {
                return $item->name;
            });
    }
}
