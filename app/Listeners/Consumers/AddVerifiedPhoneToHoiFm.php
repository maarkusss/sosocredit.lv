<?php

namespace App\Listeners\Consumers;

use App\Contracts\HoiFmServiceInterface;
use App\Events\ConsumerEvent;

class AddVerifiedPhoneToHoiFm
{
    public function __construct(protected HoiFmServiceInterface $hoi_fm)
    {
    }

    public function handle(ConsumerEvent $event): void
    {
        if ($this->hoi_fm->shouldExportPhoneToHoi($event->consumer)) {
            $this->hoi_fm->reInsertConsumerPhone($event->consumer);
        }
    }
}
