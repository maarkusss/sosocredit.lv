<?php

namespace App\Contracts;

interface OfferServiceInterface
{
    public function getAll($locale): array;
}
