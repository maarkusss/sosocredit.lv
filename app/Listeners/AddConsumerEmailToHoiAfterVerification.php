<?php

namespace App\Listeners;

use App\Contracts\HoiFmServiceInterface;
use App\Events\ConsumerEmailVerified;

class AddConsumerEmailToHoiAfterVerification
{
    public function __construct(protected HoiFmServiceInterface $hoi_fm)
    {
    }

    public function handle(ConsumerEmailVerified $event): void
    {
        $user_agent = get_consumer_user_agent_data();

        $this->hoi_fm->addEmail(
            $event->consumer->first_name,
            $event->consumer->email,
            $event->consumer->hoi_token,
            $user_agent,
            $event->consumer->fingerprint
        );
    }
}
