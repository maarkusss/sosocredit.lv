<?php

namespace App\Http\Middleware;

use App\Models\Ban;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIpBans
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $is_ip_banned = Ban::query()
            ->where('ip', $request->ip())
            ->select('id')
            ->exists();

        if ($is_ip_banned) {
            return abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
