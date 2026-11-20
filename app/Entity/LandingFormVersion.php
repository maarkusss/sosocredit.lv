<?php

namespace App\Entity;

use Illuminate\Support\Facades\Cookie;

class LandingFormVersion
{
    public const DEFAULT_HASH = 'c4ca4238a0b923820dcc509a6f75849b';
    public const SECOND_HASH = 'c81e728d9d4c2f636f067f89cc14862c';

    public const DEFAULT = 1;
    public const SECOND = 2;

    public static function getByHash(string $hash): int
    {
        if ($hash === static::SECOND_HASH) {
            return static::SECOND;
        }

        return static::DEFAULT;
    }

    public static function retrieve(): int
    {
        return LandingFormVersion::DEFAULT;

        if (request()->has('version') && in_array(request()->version, [
                static::DEFAULT_HASH,
                static::SECOND_HASH
            ])) {
            $version = static::getByHash(request()->version);
            cookie()->forever('version', $version);

            return $version;
        }

        return !empty(Cookie::get('version')) && is_numeric(Cookie::get('version'))
            ? (int) Cookie::get('version')
            : LandingFormVersion::DEFAULT;
    }
}
