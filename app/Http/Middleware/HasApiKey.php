<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;

/**
 * Ensure that request has the security header for API access
 */
class HasApiKey
{
    protected const HEADER_KEY = 'apikey';

    /**
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->hasHeader(static::HEADER_KEY)) {
            return response()->json([
                'message' => 'Not Found'
            ], 404);
        }

        if (false === Setting::query()
                ->where('name', 'api_key')
                ->where('value', $request->header(static::HEADER_KEY))
                ->exists()) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        return $next($request);
    }
}
