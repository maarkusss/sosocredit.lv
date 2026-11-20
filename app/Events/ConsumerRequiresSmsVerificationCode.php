<?php

namespace App\Events;

use App\Models\Consumer;

class ConsumerRequiresSmsVerificationCode extends ConsumerEvent
{
    public function __construct(public Consumer $consumer, public bool $resend = false)
    {}
}
