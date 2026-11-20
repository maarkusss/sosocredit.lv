<?php

namespace App\Listeners\Applications;

use App\Contracts\SendSmsInterface;
use App\Events\ApplicationEvent;
use App\Models\Consumer\ConsumerSMSVerificationCode;
use App\Services\Sms\Messages\VerificationCodeMessage;

class SendVerificationSmsToApplication
{
    public function __construct(protected SendSmsInterface $sms_sender)
    {
    }

    public function handle(ApplicationEvent $event): void
    {
        $application = $event->application;

        $sms_verification_code = new ConsumerSMSVerificationCode();
        $sms_verification_code->phone_number = $application->phone;
        $sms_verification_code->code = rand(100000, 999999);
        $sms_verification_code->expires_at = now()->addMinutes(15);
        $sms_verification_code->save();

        $verification_code_message = new VerificationCodeMessage($sms_verification_code);

        $this->sms_sender->sendSms(
            country_code: config('app.country_code'),
            phone: $application->phone,
            alpha_name: $verification_code_message->sender,
            message: $verification_code_message->message,
        );

        if (!empty($application->consumer)) {
            $application->consumer->sendings()->create([
                'type' => 'sms',
                'subject' => $verification_code_message->sender,
                'message' => $verification_code_message->message,
            ]);
        }
    }
}
