<?php

namespace App\Models;

use App\Contracts\SettingStorage;
use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $value
 */
class Setting extends Model implements SettingStorage
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'settings';

    public static function retrieve(string $name): string
    {
        $setting = Setting::query()
            ->where('name', $name)
            ->select('value')
            ->first();

        if ($setting && $setting->value) {
            return $setting->value;
        }

        return '';
    }

    public function get(string $key): string
    {
        return static::retrieve($key);
    }

    protected static function newFactory(): Factory
    {
        return SettingFactory::new();
    }
}
