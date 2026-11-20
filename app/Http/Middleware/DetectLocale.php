<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Closure;
use Illuminate\Support\Str;

class DetectLocale
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $available_locales = $this->availableLocales();
        $default_locale = $this->defaultLocale();
        $selected_locale = $request->segment(1) ?? '';

        if ($request->path() === '/' && app()->environment('production')) {
            return redirect("/{$default_locale}", 301);
        }

        if (in_array($selected_locale, $available_locales)) {
            App::setLocale($selected_locale);
        } elseif (empty($available_locales)) {
            return redirect(route('setup.index'));
        } else {
            App::setLocale($default_locale);

            $segments_without_locale = collect($request->segments())
                ->slice($this->hasLocaleOrApiPrefix($request->getRequestUri()) ? 1 : 0)
                ->map(fn ($segment) => $segment)
                ->values()
                ->toArray();

            $link = '/' . $default_locale . '/' . implode('/', $segments_without_locale);

            if (!empty($request->getQueryString())) {
                $link .= '?' . $request->getQueryString();
            }

            return redirect($link);
        }

        return $next($request);
    }

    protected function hasLocaleOrApiPrefix(string $url): bool
    {
        $exploded = explode('/', $url);
        $exploded = collect($exploded)
            ->filter(fn ($item) => !empty($item))
            ->values()
            ->toArray();

        if (!empty($exploded[0]) && Str::contains($exploded[0], '?')) {
            $exploded_first = explode('?', $exploded[0]);
            $exploded[0] = $exploded_first[0];
        }

        return count($exploded) > 0 && (strlen($exploded[0]) === 2 || $exploded[0] === 'api');
    }

    protected function defaultLocale(): string
    {
        return default_locale();
    }

    public function availableLocales(): array
    {
        return available_locales();
    }
}
