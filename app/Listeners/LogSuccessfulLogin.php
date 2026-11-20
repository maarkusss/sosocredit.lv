<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\User\UserLoginAttempt;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     *
     * @param Login $event
     * @return void
     */
    public function handle(Login $event): void
    {
        if ($event->user instanceof User) {
            $attempt = new UserLoginAttempt();
            $attempt->user_id = $event->user->id;
            $attempt->username = $event->user->username;
            $attempt->ip_address = request()->getClientIp();
            $attempt->is_success = 1;
            $attempt->save();
        }
    }
}
