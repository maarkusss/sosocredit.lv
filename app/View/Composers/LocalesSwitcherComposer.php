<?php

namespace App\View\Composers;

use App\Models\ConnectedUrl;
use Illuminate\View\View;

class LocalesSwitcherComposer
{
    public function compose(View $view): void
    {
        $locales_switcher = [];

        if (count(available_locales()) > 1) {
            foreach (inactive_locales() as $inactive_locale) {
                $locales_switcher[$inactive_locale] = [
                    'href' => '/' . $inactive_locale,
                    'name' => strtoupper($inactive_locale),
                ];
            }

            $request_uri = request()->getRequestUri();
            $is_blog_listing = !empty(request()->segments()) && count(request()->segments()) === 2 && request()->segment(2) === 'blog';

            if ($is_blog_listing) {
                foreach (inactive_locales() as $inactive_locale) {
                    $locales_switcher[$inactive_locale]['href'] = '/' . $inactive_locale . '/blog';
                }
            } else {
                $connected_url = ConnectedUrl::query()
                    ->where('url', $request_uri)
                    ->where('lang', app()->getLocale())
                    ->first();

                if ($connected_url && $connected_url->id && !empty($connected_url->related_urls)) {
                    foreach ($connected_url->related_urls as $locale => $related_url) {
                        $locales_switcher[$locale]['href'] = $related_url;
                    }
                }
            }
        }

        $view->with('locales_switcher', $locales_switcher);
    }
}
