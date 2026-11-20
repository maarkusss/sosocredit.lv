<?php

namespace App\Services\OfferPage;

use App\Contracts\Consumerable;
use App\Contracts\OfferPageLinkStrategyInterface;
use App\Models\LoanType;
use Illuminate\Support\Facades\App;

class DefaultLoantypeLinkStrategy implements OfferPageLinkStrategyInterface
{
    public function buildLink(Consumerable $consumer, array $additional_params = []): string
    {
        $locale = !empty($consumer->lang) && in_array($consumer->lang, available_locales()) ? $consumer->lang : default_locale();
        $fbclid = !empty(request()->cookie('_fbc')) ? request()->cookie('_fbc') : $consumer->fbc;
        $loantype = $this->fetchLoantype($locale);

        if (empty($loantype->id)) {
            // Fallback to /offer page strategy
            return (App::make(OfferPageLinkStrategy::class))->buildLink($consumer, $additional_params);
        }

        /** @var LoanType $loantype */

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

        $link = '/' . $loantype->lang. '/' . $loantype->route_name;
        $link .= '?' . $params;

        return $link;
    }

    protected function fetchLoantype(string $locale): ?Loantype
    {
        $loantype = LoanType::active()->where('lang', $locale)->orderBy('id', 'ASC')->first();

        if (empty($loantype->id) && $locale != default_locale()) {
            $loantype = LoanType::active()->where('lang', default_locale())->orderBy('id', 'ASC')->first();
        }

        return $loantype;
    }
}
