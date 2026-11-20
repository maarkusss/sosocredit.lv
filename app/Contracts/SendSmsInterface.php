<?php

namespace App\Contracts;

interface SendSmsInterface
{
    public function sendSms(string $country_code, string $phone, string $alpha_name, string $message): bool;
}
