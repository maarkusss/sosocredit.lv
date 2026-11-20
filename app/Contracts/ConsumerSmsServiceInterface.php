<?php

namespace App\Contracts;

interface ConsumerSmsServiceInterface
{
    public function sendVerificationCodeToConsumer(Consumerable $consumer, bool $sendWhatsApp = false);

    public function sendOfferLinkToConsumer(Consumerable $consumer);
}
