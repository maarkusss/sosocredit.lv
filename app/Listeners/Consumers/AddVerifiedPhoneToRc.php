<?php

namespace App\Listeners\Consumers;

use App\Events\ConsumerEvent;
use App\Services\RcService;

class AddVerifiedPhoneToRc
{
    public function __construct(protected RcService $rc_service)
    {
    }

    public function handle(ConsumerEvent $event): void
    {
        if (empty(config('services.rc.project_token'))) {
            return;
        }

        $consumer = $event->consumer;

        $can_export = false;

        if (config('hoi_fm.export_phone_numbers') === 'all') {
            $can_export = true;
        } elseif (config('hoi_fm.export_phone_numbers') === 'verified' && $consumer->hasVerifiedPhone()) {
            $can_export = true;
        } elseif (config('hoi_fm.export_phone_numbers') === 'subscribed' && $consumer->isSubscribedToEmailsAndSms()) {
            $can_export = true;
        }

        if ($can_export === true) {
            $this->rc_service->subscribe($consumer->phone, $consumer->first_name);
        }
    }
}
