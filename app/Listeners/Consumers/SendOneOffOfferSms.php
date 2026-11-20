<?php

namespace App\Listeners\Consumers;

use App\Contracts\SendSmsInterface;
use App\Events\ApplicationEvent;
use App\Models\Application;
use App\Models\Consumer;
use App\Services\Sms\Messages\ApplicationFirstOfferLinkMessage;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Throwable;

/**
 * @deprecated
 */
class SendOneOffOfferSms
{
    public function __construct(protected SendSmsInterface $sms_sender)
    {
    }

    public function handle(ApplicationEvent $event): void
    {
        if (!$this->shouldSendNotification($event)) {
            return;
        }

        try {
            $application = $event->application;
            $consumer = $event->application->consumer;

            /** @var Application $application */
            /** @var Consumer $consumer */

            $first_offer_hash = $application->first_offer_hash;

            $offer_link_message = new ApplicationFirstOfferLinkMessage($first_offer_hash);

            $this->sms_sender->sendSms(
                country_code: config('app.country_code'),
                phone: $consumer->phone,
                alpha_name: $offer_link_message->sender,
                message: $offer_link_message->message,
            );

            $consumer->offer_sms_sent_at = now();
            $consumer->save();

            $consumer->sendings()->create([
                'type' => 'sms',
                'subject' => $offer_link_message->sender,
                'message' => Str::length($offer_link_message->message) > 191 ? Str::substr($offer_link_message->message, 0, 191) : $offer_link_message->message,
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    protected function shouldSendNotification(ApplicationEvent $event): bool
    {
        $consumer = $event->application->consumer;

        $can_send_sms = empty($consumer->offer_sms_sent_at);
        if (!$can_send_sms) {
            $last_sms_at = Carbon::createFromFormat('Y-m-d H:i:s', $consumer->offer_sms_sent_at);

            $can_send_sms = now()->diffInHours($last_sms_at) > 23;
        }

        return $consumer && $consumer->phone && $can_send_sms;
    }
}
