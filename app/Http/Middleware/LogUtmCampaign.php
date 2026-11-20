<?php

namespace App\Http\Middleware;

use App\Models\Lender\LenderSorting;
use Closure;

class LogUtmCampaign
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->utm_campaign) {
            LenderSorting::firstOrCreate([
                'campaign_name' => $request->utm_campaign
            ]);
        } elseif ($request->s2) {
            LenderSorting::firstOrCreate([
                'campaign_name' => $request->s2,
            ]);
        }

        return $next($request);
    }
}
