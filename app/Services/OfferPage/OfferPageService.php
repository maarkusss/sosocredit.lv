<?php

namespace App\Services\OfferPage;

use App\Contracts\Consumerable;
use App\Contracts\OfferPageLinkStrategyInterface;

class OfferPageService
{
    public function __construct(protected OfferPageLinkStrategyInterface $strategy)
    {
    }

    public function buildLink(Consumerable $consumer, array $additional_params = []): string
    {
        return $this->strategy->buildLink($consumer, $additional_params);
    }
}
