<?php

namespace App\Models;

use App\Contracts\Seoable;
use App\Models\Lender\LenderCategory;
use Database\Factories\LoanTypeFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $lang
 * @property $order
 * @property $name
 * @property $route_name
 * @property $title
 * @property $description
 * @property $keywords
 * @property $text
 * @property $active
 * @property $h1
 * @property $h1_description
 *
 * @method active()
 */
class LoanType extends Model implements Seoable
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'loan_types';

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }

    public function categories()
    {
        return $this->hasMany(LenderCategory::class, 'loan_type_id');
    }

    protected static function newFactory(): Factory
    {
        return LoanTypeFactory::new();
    }

    public function h1_tag(): string
    {
        if (!empty($this->title)) {
            return $this->title;
        }

        return $this->name ?? '';
    }

    public function canonical_url(): string
    {
        if (empty($this->route_name)) {
            return '';
        }

        return route('section', ['name' => $this->route_name]);
    }
}
