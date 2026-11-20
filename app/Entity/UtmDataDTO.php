<?php

namespace App\Entity;

use App\Contracts\Consumerable;

/**
 * UtmDataDTO
 *
 * Object to store UTM data
 */
class UtmDataDTO
{
    public const UTM_SOURCE_APP = 'app';
    public const UTM_CAMPAIGN_ANDROID = 'android-app';
    public const UTM_CAMPAIGN_IOS = 'ios-app';

    public function __construct(
        public ?string $utm_source = null,
        public ?string $utm_medium = null,
        public ?string $utm_campaign = null,
        public ?string $utm_content = null,
        public ?string $gclid = null,
        public ?string $s7 = null,
        public ?string $s8 = null
    ) {
    }

    public static function fromRequest(): UtmDataDTO
    {
        $utm_data = new static();
        $utm_data->utm_source = request()->cookie('utm_source');
        $utm_data->utm_campaign = request()->cookie('utm_campaign');
        $utm_data->utm_content = request()->cookie('utm_content');
        $utm_data->gclid = request()->cookie('gclid');
        $utm_data->s7 = request()->cookie('client_id');
        $utm_data->s8 = request()->cookie('s8');

        if (request()->s2) {
            $utm_data->utm_campaign = request()->s2;
        }

        if (request()->s3) {
            $utm_data->gclid = request()->s3;
        }

        if (request()->s4) {
            $utm_data->utm_content = request()->s4;
        }

        if (request()->s5) {
            $utm_data->utm_source = request()->s5;
        }

        if (request()->s7) {
            $utm_data->s7 = request()->s7;
        }

        if (request()->s8 || request()->s8 === '0') {
            $utm_data->s8 = request()->s8;
        }

        if (request()->utm_source) {
            $utm_data->utm_source = request()->utm_source;
        }

        if (request()->utm_campaign) {
            $utm_data->utm_campaign = request()->utm_campaign;
        }

        if (request()->utm_content) {
            $utm_data->utm_content = request()->utm_content;
        }

        if (request()->gclid) {
            $utm_data->gclid = request()->gclid;
        }

        // Override for Facebook
        if ($utm_data->utm_source === 'facebook') {
            $utm_data->gclid = request()->cookie('_fbc');
            $utm_data->utm_content = request()->cookie('_fbp');
        }

        return $utm_data;
    }

    public static function fromConsumer(Consumerable $consumer): UtmDataDTO
    {
        $utm_data = new static();
        $utm_data->utm_source = $consumer->utm_source;
        $utm_data->utm_medium = $consumer->utm_medium;
        $utm_data->utm_campaign = $consumer->utm_campaign;
        $utm_data->utm_content = $consumer->utm_content;

        return $utm_data;
    }

    public static function fromDeeplink(string $deeplink): UtmDataDTO
    {
        $utm_data = new static();

        $deeplink = base64_decode($deeplink);
        $parsed = parse_url($deeplink);
        if (!empty($parsed) && !empty($parsed['query'])) {
            parse_str($parsed['query'], $query);
            $utm_data->utm_source = $query['utm_source'] ?? null;
            $utm_data->utm_campaign = $query['utm_campaign'] ?? null;
            $utm_data->utm_medium = $query['utm_medium'] ?? null;
            $utm_data->utm_content = $query['utm_content'] ?? null;
            $utm_data->gclid = $query['gclid'] ?? null;
        }

        return $utm_data;
    }

    public static function fromAppRequest(): UtmDataDTO
    {
        $utm_data = new static();

        $utm_data->utm_source = static::UTM_CAMPAIGN_ANDROID;

        if (request()->hasHeader('Application-Version-iOS')) {
            $utm_data->utm_source = static::UTM_CAMPAIGN_IOS;
        }

        return $utm_data;
    }
}
