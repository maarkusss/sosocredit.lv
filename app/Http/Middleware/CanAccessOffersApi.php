<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;

/**
 * Ensure that request has the project-specific security header for API access
 */
class CanAccessOffersApi
{
    protected const MASTER_HEADER = 'apikey';
    protected const PROJECT_HEADER = 'Api-Key';

    /**
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle($request, Closure $next)
    {
        $has_master_key = $request->hasHeader(static::MASTER_HEADER);
        $has_project_key = $request->hasHeader(static::PROJECT_HEADER);

        if (!$has_master_key && !$has_project_key) {
            return response()->json([
                'message' => 'Not Found'
            ], 404);
        }

        if (($has_master_key && !$this->validMasterKey($request)) ||
            $has_project_key && !$this->validProjectKey($request)) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        return $next($request);
    }

    protected function validMasterKey($request): bool
    {
        return Setting::query()
            ->where('name', 'api_key')
            ->where('value', $request->header(static::MASTER_HEADER))
            ->exists();
    }

    protected function validProjectKey($request): bool
    {
        return $request->header(static::PROJECT_HEADER) === config('api.project_key');
    }
}
