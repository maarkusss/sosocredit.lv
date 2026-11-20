<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationGocardlessAccess extends Model
{
    protected $table = 'applications__gocardless_access';

    protected $visible = [
        'access_token_updated_at',
    ];

    public $timestamps = true;

    public static function getRefreshToken(): string
    {
        $access_data = static::getAccessData();

        return $access_data->refresh_token;
    }

    public static function updateAccessToken(string $access_token): void
    {
        $access_data = static::getAccessData();

        $access_data->access_token = $access_token;
        $access_data->access_token_updated_at = now();
        $access_data->save();
    }

    public static function updateBothTokens(string $access_token, string $refresh_token): void
    {
        $access_data = static::getAccessData();

        if ($access_token) {
            $access_data->access_token = $access_token;
            $access_data->access_token_updated_at = now();
        }
        if ($refresh_token) {
            $access_data->refresh_token = $refresh_token;
            $access_data->refresh_token_updated_at = now();
        }

        $access_data->save();
    }

    public static function getAccessData(): ?ApplicationGocardlessAccess
    {
        return self::find(1);
    }
}
