<?php

namespace App\Models\Lender;

use Goodday\Common\Casts\ArrayedObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * @property $id
 * @property $active
 * @property $utm_source
 * @property $utm_medium
 * @property $utm_campaign
 * @property $utm_content
 * @property $token
 * @property $rules
 * @property $hits
 * @property $source
 * @property $short_url
 * @property $created_at
 * @property $updated_at
 *
 * @property $url
 *
 * @method active()
 */
class LenderFilter extends Model
{
    use HasFactory;

    protected $table = 'lenders__filters';

    protected $fillable = [
        'active',
        'rules',
        'utm_campaign',
        'utm_source',
        'token',
    ];

    public $timestamps = true;

    protected $appends = [
        'url',
    ];

    protected $casts = [
        'rules' => ArrayedObject::class,
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public const CACHE_KEY = 'lenders_filters';

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }

    public function getUrlAttribute(): string
    {
        return Str::replaceFirst('/api/', '/' . default_locale() . '/', route('offer', [
            'utm_source' => $this->utm_source,
            'utm_campaign' => $this->utm_campaign,
            'token' => $this->token,
        ]));
    }

    public static function getCachedFilters(int $minutes = 10): array
    {
        return Cache::remember(static::CACHE_KEY, 60 * $minutes, function () {
            return LenderFilter::active()
                ->get()
                ->toArray();
        });
    }

    public static function flushCache(): void
    {
        Cache::forget(LenderFilter::CACHE_KEY);
    }
}
