<?php

namespace App\Models;

use App\Entity\GoLink;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @property $id
 * @property $name
 * @property $params_from
 * @property $domain_from
 * @property $domain_to
 * @property $path_ending_from
 * @property $path_ending_to
 * @property $params_to
 * @property $active
 * @property $created_at
 * @property $updated_at
 *
 * @method active()
 */
class GoRewrite extends Model
{
    protected $table = 'go__rewrites';

    protected $guarded = [];

    public $timestamps = true;

    protected $casts = [
        'params_from' => 'array',
        'params_to' => 'array',
    ];

    public const CACHE_KEY = 'go_rewrites';

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }

    protected function validateParams(array $params): void
    {
        foreach (GoLink::REQUIRED_PARAMS as $required_key) {
            if (!array_key_exists($required_key, $params)) {
                throw new \InvalidArgumentException('Incorrect Go params array');
            }
        }
    }

    public function matchesParams(array $params): bool
    {
        $this->validateParams($params);
        $matched_params = [];

        foreach ($this->params_from as $params_from_key => $params_from_value) {
            if (array_key_exists($params_from_key, $params) && $params[$params_from_key] === $params_from_value) {
                $matched_params[$params_from_key] = $params_from_value;
            }
        }

        return count($matched_params) === count($this->params_from); // all matched exactly (key + value)
        //        return count(array_intersect($params, $this->params_from)) === count($this->params_from); // all value matched
        //        return count(array_intersect($params, $this->params_from)) > 0; // at least one value match
    }

    public function rewriteDomain(string $url): string
    {
        if (empty($this->domain_from) || empty($this->domain_to)) {
            return $url;
        }

        $domain_from = $this->domain_from;
        $domain_to = $this->domain_to;

        $parsed_url = parse_url($url);

        if (!empty($parsed_url['host']) && str_ends_with($parsed_url['host'], $domain_from)) {
            $parsed_url['host'] = substr($parsed_url['host'], 0, -1 * (strlen($domain_from)));
            $parsed_url['host'] .= $domain_to;

            $url = build_url($parsed_url);
        }

        return $url;
    }

    public function rewritePathEnding(string $url): string
    {
        if (empty($this->path_ending_from) || empty($this->path_ending_to)) {
            return $url;
        }

        $ending_from = '/' . $this->path_ending_from;
        $ending_to = '/' . $this->path_ending_to;

        $parsed_url = parse_url($url);
        if (!empty($parsed_url['path']) && str_ends_with($parsed_url['path'], $ending_from)) {
            $parsed_url['path'] = substr($parsed_url['path'], 0, -1 * (strlen($ending_from)));
            $parsed_url['path'] .= $ending_to;

            $url = build_url($parsed_url);
        }

        return $url;
    }

    public function rewriteParams(array $params): array
    {
        $this->validateParams($params);

        return array_merge($params, $this->params_to);
    }

    public static function getCachedRewrites(int $minutes = 5): Collection
    {
        return Cache::remember(static::CACHE_KEY, 60 * $minutes, function () {
            return GoRewrite::active()
                ->get();
        });
    }
}
