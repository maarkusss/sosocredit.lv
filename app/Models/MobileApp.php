<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileApp extends Model
{
    protected $table = 'mobile_apps';
    protected $fillable = [
        'platform',
        'link',
        'name',
        'names',
        'link_parameter',
        'link_parameters',
        'qr_title',
        'qr_titles',
        'qr_description',
        'qr_descriptions',
        'rating',
    ];
    public $timestamps = true;
    protected $casts = [
        'names' => 'json',
        'link_parameters' => 'json',
        'qr_titles' => 'json',
        'qr_descriptions' => 'json',
    ];
    public const PLATFORM_ANDROID = 'Android';
    public const PLATFORM_IOS = 'iOS';
    public const PLATFORM_GALAXY = 'Galaxy';
}
