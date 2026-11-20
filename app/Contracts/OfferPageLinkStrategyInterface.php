<?php

namespace App\Contracts;

interface OfferPageLinkStrategyInterface
{
    public function buildLink(Consumerable $consumer, array $additional_params = []): string;
}
