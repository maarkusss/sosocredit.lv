<?php

namespace App\Services\OfferPage;

use App\Contracts\Consumerable;
use App\Contracts\OfferPageLinkStrategyInterface;

class OfferPageLinkStrategy implements OfferPageLinkStrategyInterface
{
    public function buildLink(Consumerable $consumer, array $additional_params = []): string
    {
        $locale = !empty($consumer->lang) && in_array($consumer->lang, available_locales()) ? $consumer->lang : default_locale();
        $fbc = !empty(request()->cookie('_fbc')) ? request()->cookie('_fbc') : $consumer->fbc;
        $fbclid = convert_fbc_to_fbclid($fbc);

        $params = [
            'utm_source' => $consumer->utm_source,
            'utm_medium' => $consumer->utm_medium,
            'utm_campaign' => $consumer->utm_campaign,
            'utm_content' => $consumer->utm_content,
            'gclid' => $consumer->gclid,
            'fbclid' => $fbclid,
        ];
        $params = array_merge($params, $additional_params);
        $params = http_build_query($params);

        $link = '/' . $locale . '/offer';
        $link .= '?' . $params;

        return $link;
    }
}
