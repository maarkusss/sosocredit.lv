<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;

class IsSetupComplete
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
        if (app()->isProduction()) {
            return $next($request);
        }

        if ($this->isSetupComplete()) {
            return $next($request);
        }

        return redirect(route('setup.index'));
    }

    public function isSetupComplete(): bool
    {
        return Setting::query()
            ->where('name', 'lang')
            ->exists();
    }
}
