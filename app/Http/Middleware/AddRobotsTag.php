<?php

namespace App\Http\Middleware;

use Closure;

class AddRobotsTag
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        return $this->addRobotsTag($response);
    }

    /**
     * @param \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse $response
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    protected function addRobotsTag($response)
    {
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');

        return $response;
    }
}
