<?php

namespace App\Http\Middleware;

use Closure;

class HttpProtocol
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (app()->isProduction() || app()->environment('staging')) {
            if ($this->isCloudflare($request)) {
                if (!$this->isCloudflareHttps($request)) {
                    // Flag Cloudflare not connecting through https?
                }
            } elseif (!$request->secure()) {
                if ($request->wantsJson()) {
                    return abort(400, "SSL must be used");
                }

                return redirect()->secure($request->getRequestUri(), 301);
            }
        }

        return $next($request);
    }

    protected function isCloudflare($request): bool
    {
        return !empty($request->header('CF-Visitor'));
    }

    protected function isCloudflareHttps($request): bool
    {
        return $request->header('CF-Visitor') && json_decode($request->header('CF-Visitor'))->scheme === 'https';
    }
}
