<?php

namespace App\Listeners\Applications;

use App\Contracts\HoiFmServiceInterface;
use App\Events\ApplicationEvent;
use Goodday\Common\Services\UserAgent;

class AddVerifiedApplicationPhoneToHoiFm
{
    public function __construct(protected HoiFmServiceInterface $hoi_fm, protected UserAgent $user_agent)
    {
    }

    public function handle(ApplicationEvent $event): void
    {
        $application = $event->application;

        if (empty($application->phone_verified_at)) {
            return;
        }

        $user_agent = $this->user_agent->getUserAgentData();

        $this->hoi_fm->removePhoneNumber($application->phone);
        $this->hoi_fm->addPhoneNumber(
            name: $application->first_name,
            first_name: $application->first_name,
            last_name: $application->last_name,
            personal_code: $application->personal_code,
            phone_number: $application->phone,
            hoi_token: $application->getHoiToken() ?? '',
            user_agent: $user_agent,
            fingerprint: $application->fingerprint
        );
    }
}
