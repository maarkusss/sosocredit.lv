<?php

namespace App\Listeners\Consumers;

use App\Contracts\HoiFmServiceInterface;
use App\Events\ConsumerEvent;

class UpdateHoiTokenForPhone
{
    public function __construct(protected HoiFmServiceInterface $hoi_fm)
    {
    }

    public function handle(ConsumerEvent $event): void
    {
        if (!$this->hoi_fm->shouldExportPhoneToHoi($event->consumer)) {
            return;
        }

        $this->hoi_fm->updateHoiTokenForPhoneNumber(
            phone_number: $event->consumer->phone,
            new_hoi_token: $event->consumer->hoi_token
        );
    }
}
