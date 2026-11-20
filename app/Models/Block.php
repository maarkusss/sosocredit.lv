<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $table = 'blocks';

    protected $fillable = [
        'lang',
        'key',
        'value',
        'tag',
    ];

    public static array $PRELOADED = [];

    public static function getValue(string $key, ?string $lang = ''): string
    {
        if (empty($lang)) {
            $lang = app()->getLocale();
        }

        if (!empty(static::$PRELOADED[$lang]) && array_key_exists($key, static::$PRELOADED[$lang])) {
            return static::$PRELOADED[$lang][$key] ?? '';
        }

        $block = Block::query()
            ->where('lang', $lang)
            ->where('key', $key)
            ->select(['id', 'value'])
            ->first();

        if (
            empty($block->id) &&
            (app()->isLocal() || (!app()->isProduction() && request()->SHOW_BLOCKS))
        ) {
            return '[ADD ' . $key . ']';
        }

        return !empty($block->value) ? $block->value : '';
    }

    public static function preloadTags(array $tags, ?string $lang = ''): void
    {
        if (empty($lang)) {
            $lang = app()->getLocale();
        }

        static::$PRELOADED[$lang] = Block::query()
            ->where('lang', $lang)
            ->whereIn('tag', $tags)
            ->pluck('value', 'key')
            ->toArray();
    }
}
