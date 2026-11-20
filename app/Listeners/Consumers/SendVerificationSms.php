<?php

namespace App\Listeners\Consumers;

use App\Contracts\ConsumerSmsServiceInterface;
use App\Events\ConsumerRequiresSmsVerificationCode;
use App\Models\Consumer\ConsumerSMSVerificationCode;
use App\Services\Sms\Messages\VerificationCodeMessage;
use Illuminate\Support\Str;

class SendVerificationSms
{
    public function __construct(protected ConsumerSmsServiceInterface $consumer_sms_service)
    {
    }

    public function handle(ConsumerRequiresSmsVerificationCode $event): void
    {
        $this->consumer_sms_service->sendVerificationCodeToConsumer($event->consumer, sendWhatsApp: $event->resend);

        $consumer_sms_verification_code = ConsumerSMSVerificationCode::query()
            ->where('phone_number', $event->consumer->phone)
            ->orderBy('id', 'DESC')
            ->first();

        if (empty($consumer_sms_verification_code->id)) {
            $consumer_sms_verification_code = new ConsumerSMSVerificationCode();
        }

        $verification_code_message = new VerificationCodeMessage($consumer_sms_verification_code);

        $event->consumer->sendings()->create([
            'type' => 'sms',
            'subject' => $verification_code_message->sender,
            'message' => Str::length($verification_code_message->message) > 191 ? Str::substr($verification_code_message->message, 0, 191) : $verification_code_message->message,
        ]);
    }
}
