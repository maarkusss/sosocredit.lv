<?php

namespace App\Services\Sms\Messages;

class VerificationLinkMessage extends Message
{
    public function __construct(string $verification_link)
    {
        $this->sender = config('app.sms_alpha_name');
        $this->message = __("Please click this link to verify your number:");
        $this->message .= ' ' . $verification_link;
    }
}
