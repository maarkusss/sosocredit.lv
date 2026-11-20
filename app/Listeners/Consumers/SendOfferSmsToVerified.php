<?php

namespace App\Listeners\Consumers;

use App\Contracts\ConsumerSmsServiceInterface;
use App\Events\ConsumerEvent;
use App\Models\Consumer;
use App\Services\Sms\Messages\OfferLinkMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SendOfferSmsToVerified
{
    public function __construct(protected ConsumerSmsServiceInterface $consumer_sms_service)
    {
    }

    public function handle(ConsumerEvent $event): void
    {
        if (!$this->shouldSendSms($event)) {
            return;
        }

        if ($this->isSpammer($event->consumer)) {
            return;
        }

        $this->consumer_sms_service->sendOfferLinkToConsumer($event->consumer);

        $offer_link_message = new OfferLinkMessage($event->consumer->hoi_token);
        $event->consumer->sendings()->create([
            'type' => 'sms',
            'subject' => $offer_link_message->sender,
            'message' => Str::length($offer_link_message->message) > 191 ? Str::substr($offer_link_message->message, 0, 191) : $offer_link_message->message,
        ]);
    }

    protected function shouldSendSms(ConsumerEvent $event): bool
    {
        return $event->consumer && $event->consumer->phone && $event->consumer->hasVerifiedPhone();
    }

    public function isSpammer(Consumer $consumer): bool
    {
        if ($consumer->sendings()
                ->where('type', 'sms')
                ->where('created_at', '>=', now()->subMinutes(10)->format('Y-m-d H:i:00'))
                ->count() > 3) {
            Log::warning('SendOfferSmsToVerified isSpammer', [
                'consumer_id' => $consumer->id,
                'phone' => $consumer->phone,
                'now_check' => now()->subMinutes(10)->format('Y-m-d H:i:00'),
                'limit' => 3,
            ]);

            return true;
        }

        return false;
    }
}
