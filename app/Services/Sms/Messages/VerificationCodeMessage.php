<?php

namespace App\Services\Sms\Messages;

use App\Models\Consumer\ConsumerSMSVerificationCode;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class VerificationCodeMessage extends Message
{
    public function __construct(ConsumerSMSVerificationCode $verification_code)
    {
        $this->sender = config('app.sms_alpha_name');
        $this->message = $verification_code->code . __('sms.your_verification_code');
    }
}
