<?php

namespace App\Listeners\Consumers;

use App\Events\ConsumerEvent;
use App\Mail\LenderOffer;
use Illuminate\Support\Facades\Mail;

class SendOfferEmailToVerifiedAndSubscribed
{
    public function handle(ConsumerEvent $event): void
    {
        if (!$this->shouldSendEmail($event)) {
            return;
        }

        $lender_offer = new LenderOffer($event->consumer);

        Mail::to($event->consumer)
            ->send($lender_offer);

        $event->consumer->sendings()->create([
            'type' => 'email',
            'subject' => $lender_offer->generateSubject(),
        ]);
    }

    protected function shouldSendEmail(ConsumerEvent $event): bool
    {
        $consumer = $event->consumer;

        return $consumer && $consumer->email && $consumer->hasVerifiedEmail() && $consumer->isSubscribedToEmailsAndSms();
    }
}
