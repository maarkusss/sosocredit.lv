<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class HasOfferRedirectCookie
{
    /**
     * Handle an incoming request.
     *
     * @param  Request $request
     * @param  Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasCookie('offer_redirect')) {
            return $next($request);
        }

        $default_locale = default_locale();

        return redirect('/' . $default_locale);
    }
}
