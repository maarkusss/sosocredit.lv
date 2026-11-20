<?php

namespace App\Contracts;

/**
 * @property $email
 * @property $phone
 * @property $first_name
 * @property $hoi_token
 * @property $fingerprint
 * @property $utm_source
 * @property $utm_medium
 * @property $utm_campaign
 * @property $utm_content
 * @property $gclid
 * @property $lang
 */
interface Consumerable
{
    public function hasVerifiedPhone();

    public function hasVerifiedEmail();

    public function isSubscribedToEmailsAndSms();

    public function hasSignedUpViaApp(): bool;
}
