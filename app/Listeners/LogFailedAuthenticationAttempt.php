<?php

namespace App\Listeners;

use App\Models\User\UserLoginAttempt;
use Illuminate\Auth\Events\Failed;

class LogFailedAuthenticationAttempt
{
    /**
     * Handle the event.
     *
     * @param Failed $event
     * @return void
     */
    public function handle(Failed $event): void
    {
        $attempt = new UserLoginAttempt();
        if ($event->user) {
            $attempt->user_id = $event->user->id;
        }
        $attempt->username = $event->credentials['username'];
        $attempt->ip_address = request()->getClientIp();
        $attempt->is_success = 0;
        $attempt->save();
    }
}
