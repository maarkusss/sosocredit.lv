<?php

namespace App\Services\Sms\Messages;

class ApplicationSecondOfferLinkMessage extends Message
{
    public function __construct(string $offer_hash)
    {
        $random_beginning = $this->getBeginning();
        $offer_link = $this->getApplicationOfferLink($offer_hash);

        $this->sender = config('app.sms_alpha_name');
        $this->message = $random_beginning . " \n" . $offer_link;
    }

    public function getBeginning(): string
    {
        $random_beginnings = [
            __('sms.second_offer_link_random_beginning_1'),
            __('sms.second_offer_link_random_beginning_2'),
            __('sms.second_offer_link_random_beginning_3'),
        ];

        return $random_beginnings[rand(0, count($random_beginnings) - 1)];
    }

    public function getApplicationOfferLink(string $offer_hash): string
    {
        return config('app.site_url_sms') . '/a/' . $offer_hash;
    }
}
