<?php

namespace App\Traits;

use Illuminate\Support\Facades\Config;

trait WorksWithLanguageLocale
{
    public function getLanguageLocaleFromHeaders(): string
    {
        $language_site_locale = '';
    
        if (request()->header('Accept-Language')) {
            try {
                $allowed = available_locales();
                $acceptLanguage = str_replace('_', '-', request()->header('Accept-Language', ''));
                request()->headers->set('Accept-Language', $acceptLanguage);
                $preferred = request()->getPreferredLanguage($allowed);
                $language_site_locale = in_array($preferred, $allowed) ? $preferred : '';
            } catch (\Throwable $e) {
                $language_site_locale = '';
            }
        }
    
        if (empty($language_site_locale) && request()->header('Country')) {
            $language_site_locale = strtolower(trim(request()->header('Country')));
        }
    
        return $language_site_locale;
    }

    public function getCountryFromHeaders(): string
    {
        $country = request()->header('Country') ?? '';

        if (empty($country) && !empty(Config::get('offers-api.default_country_header'))) {
            $country = Config::get('offers-api.default_country_header');
        }

        return $country;
    }
}
