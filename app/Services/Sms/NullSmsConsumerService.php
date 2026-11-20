<?php

namespace App\Services\Sms;

use App\Contracts\Consumerable;
use App\Contracts\ConsumerSmsServiceInterface;

class NullSmsConsumerService implements ConsumerSmsServiceInterface
{
    public function sendVerificationCodeToConsumer(Consumerable $consumer): void
    {
    }

    public function sendOfferLinkToConsumer(Consumerable $consumer): void
    {
    }
}
