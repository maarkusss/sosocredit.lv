<?php

namespace App\Http\Middleware;

use App\Models\Application;
use App\Models\Consumer;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    protected function authenticate($request, array $guards)
    {
        if (in_array('consumers', $guards) && !Auth::guard('consumers')->check()) {
            if ($request->id && $request->hash) {
                $application = Application::where('id', $request->id)->first();
                if ($application && hash_equals($application->signature, $request->hash) && !empty($application->consumer_id)) {
                    $consumer = Consumer::find($application->consumer_id);
                    if ($consumer) {
                        Auth::guard('consumers')->login($consumer);
                    }
                }
            }
        }

        parent::authenticate($request, $guards);
    }

    protected function unauthenticated($request, array $guards)
    {
        if (in_array('consumers', $guards)) {
            throw new AuthenticationException(
                'Unauthenticated.',
                $guards,
                $this->redirectToConsumers($request)
            );
        }

        parent::unauthenticated($request, $guards);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    protected function redirectToConsumers($request)
    {
        if (! $request->expectsJson()) {
            return route('consumers.login');
        }
    }
}
