<?php

namespace App\Listeners\Consumers;

use App\Events\ConsumerEvent;
use Illuminate\Support\Facades\Lang;
use Throwable;

class SendVerificationEmail
{
    public function handle(ConsumerEvent $event): void
    {
        if (!$this->shouldSendNotification($event)) {
            return;
        }

        try {
            $event->consumer->sendEmailVerificationNotification();

            $event->consumer->sendings()->create([
                'type' => 'email',
                'subject' => Lang::get('Verify Email Address'),
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    protected function shouldSendNotification(ConsumerEvent $event): bool
    {
        if (config('app.verify_email') !== true) {
            return false;
        }


        if($event->consumer->phone == env('API_TEST_PHONE_NUMBER') && $event->consumer->email == env('API_TEST_EMAIL')){
            return false;
        }

        return $event->consumer && $event->consumer->email && !$event->consumer->hasVerifiedEmail();
    }
}
