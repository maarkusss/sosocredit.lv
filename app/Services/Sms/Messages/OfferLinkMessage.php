<?php

namespace App\Services\Sms\Messages;

class OfferLinkMessage extends Message
{
    public function __construct(string $hoi_token)
    {
        $random_beginning = $this->getBeginning();
        $offer_link = $this->getOfferLink($hoi_token);

        $this->sender = config('app.sms_alpha_name');
        $this->message = $random_beginning . " \n" . $offer_link;
    }

    public function getBeginning(): string
    {
        $random_beginnings = [
            __('sms.offer_link_random_beginning_1'),
            __('sms.offer_link_random_beginning_2'),
            __('sms.offer_link_random_beginning_3'),
        ];

        return $random_beginnings[rand(0, count($random_beginnings) - 1)];
    }

    public function getOfferLink(string $hoi_token): string
    {
        return config('app.site_url_sms') . '/u/' . $hoi_token;
    }
}
