<?php

namespace App\Http\Middleware;

use App\Models\Visitor;
use Closure;

class LogVisitor
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
        $userAgent = $request->header('user-agent', '');

        if (stripos($userAgent, 'uptime') !== false || stripos($userAgent, 'bot') !== false) {
            return $next($request);
        }

        $response = $next($request);
        $referrerHost = get_referrer_host($request);

        if ($request->s2) {
            $response->withCookie(cookie()->forever('utm_campaign', $request->s2));
        }

        if ($request->s3) {
            $response->withCookie(cookie()->forever('gclid', $request->s3));
        }

        if ($request->s4) {
            $response->withCookie(cookie()->forever('utm_content', $request->s4));
        }

        if ($request->s5) {
            $response->withCookie(cookie()->forever('utm_source', $request->s5));
        }

        if ($request->s8) {
            $response->withCookie(cookie()->forever('s8', $request->s8));
        }

        if ($request->utm_source) {
            $response->withCookie(cookie()->forever('utm_source', $request->utm_source));
        }

        if (!$request->s5 && !$request->utm_source && !$request->cookie('utm_source') && $referrerHost === 'google.com') {
            $response->withCookie(cookie()->forever('utm_source', 'google_organic'));
        }

        if ($request->utm_medium) {
            $response->withCookie(cookie()->forever('utm_medium', $request->utm_medium));
        }

        if ($request->utm_campaign) {
            $response->withCookie(cookie()->forever('utm_campaign', $request->utm_campaign));
        }

        if ($request->utm_content) {
            $response->withCookie(cookie()->forever('utm_content', $request->utm_content));
        }

        if ($request->gclid) {
            $response->withCookie(cookie()->forever('gclid', $request->gclid));
        } elseif ($request->yclid) {
            $response->withCookie(cookie()->forever('gclid', $request->yclid));
        } elseif ($request->msclkid) {
            $response->withCookie(cookie()->forever('gclid', $request->msclkid));
        } elseif ($request->ttclid) {
            $response->withCookie(cookie()->forever('gclid', $request->ttclid));
        }

        if ($request->fbclid) {
            $response->withCookie(cookie()->forever('fbclid', $request->fbclid));
        }

        if ($request->hasCookie('visitor_id')) {
            return $response;
        } else {
            $visitor = new Visitor();
            $visitor->user_agent = $request->header('user-agent');
            $visitor->ip = $request->ip();
            $visitor->utm_source = $request->utm_source;
            $visitor->utm_medium = $request->utm_medium;
            $visitor->utm_campaign = $request->utm_campaign;
            $visitor->utm_content = $request->utm_content;
            $visitor->gclid = $request->gclid;
            $visitor->from = $request->header('referer');
            $visitor->to = $request->fullUrl();
            $visitor->save();

            return $response->withCookie(cookie()->forever('visitor_id', $visitor->id));
        }
    }
}
