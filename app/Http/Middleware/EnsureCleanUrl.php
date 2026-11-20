<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCleanUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!empty($request->getBaseUrl()) && $request->getBaseUrl() === '/index.php') {
            $redirect_to = str_replace('/index.php', '', $request->url());
            $parsed = parse_url($redirect_to);
            if (!empty($parsed) && !empty($parsed['path']) && !$this->hasLocale($parsed['path'])) {
                $redirect_to = $this->injectLocaleIntoUri($parsed);
            }

            return redirect()->to($redirect_to, 301);
        }

        return $next($request);
    }

    protected function hasLocale(string $url): bool
    {
        $exploded = explode('/', $url);
        $exploded = collect($exploded)
            ->filter(fn ($item) => !empty($item))
            ->values()
            ->toArray();

        return count($exploded) > 0 && strlen($exploded[0]) === 2;
    }

    public function injectLocaleIntoUri(array $parsed): string
    {
        return $parsed['scheme'] . '://' . $parsed['host'] . '/' . default_locale() . $parsed['path'];
    }
}
