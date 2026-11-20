<?php

namespace App\Listeners\Consumers;

use App\Contracts\HoiFmServiceInterface;
use App\Events\ConsumerEvent;

class AddVerifiedEmailToHoiFm
{
    public function __construct(protected HoiFmServiceInterface $hoi_fm)
    {
    }

    public function handle(ConsumerEvent $event): void
    {
        if ($this->hoi_fm->shouldExportEmailToHoi($event->consumer)) {
            $this->hoi_fm->reInsertConsumerEmail($event->consumer);
        }
    }
}
