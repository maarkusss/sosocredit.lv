<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $url
 * @property $lang
 * @property $related_urls
 */
class ConnectedUrl extends Model
{
    protected $table = 'connected_urls';

    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'related_urls' => 'array',
    ];

    public static function getRelatedUrlsForRoute(string $route): array
    {
        $url = str_replace(config('app.url'), '', $route);
        $connected_url = ConnectedUrl::query()
            ->where('url', $url)
            ->first();
        if ($connected_url && $connected_url->id) {
            return $connected_url->related_urls;
        }

        return [];
    }

    public function getFormattedRelatedUrlsAttribute()
    {
        $formatted = collect($this->related_urls)
            ->map(fn ($related_url) => config('app.url') . $related_url)
            ->implode(',');

        return $formatted;
    }
}
