<?php

namespace App\Listeners\Consumers;

use App\Events\ApplicationEvent;
use App\Mail\FirstLenderOffer;
use App\Models\Consumer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * @deprecated
 */
class SendOneOffOfferEmail
{
    public function handle(ApplicationEvent $event)
    {
        if (!$this->shouldSendNotification($event)) {
            return;
        }

        try {
            $consumer = $event->application->consumer;

            /** @var Consumer $consumer */

            $first_lender_offer = new FirstLenderOffer($consumer);

            Mail::to($consumer->email)
                ->send($first_lender_offer);

            $consumer->offer_email_sent_at = now();
            $consumer->save();

            $consumer->sendings()->create([
                'type' => 'email',
                'subject' => $first_lender_offer->generateSubject(),
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    protected function shouldSendNotification(ApplicationEvent $event): bool
    {
        $consumer = $event->application->consumer;

        $can_send_email = empty($consumer->offer_email_sent_at);
        if (!$can_send_email) {
            $last_email_at = Carbon::createFromFormat('Y-m-d H:i:s', $consumer->offer_email_sent_at);

            $can_send_email = now()->diffInHours($last_email_at) > 23;
        }

        return $consumer && $consumer->email && $can_send_email;
    }
}
