<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckRedirect
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
        $from = $request->getRequestUri();
        $from = str_replace('?' . $request->getQueryString(), '', $from);

        if (str_starts_with($from, '/ru/offer&token=')) {
            $to = str_replace('/ru/offer&token=', '/ru/offer?token=', $from);

            return redirect()->to($to, 301);
        }

        if (str_starts_with($from, '/lv/offer&token=')) {
            $to = str_replace('/lv/offer&token=', '/lv/offer?token=', $from);

            return redirect()->to($to, 301);
        }

        $stop_list = [
            '/robots.txt',
            '/manifest.json',
        ];

        if (!in_array($from, $stop_list) && !$request->wantsJson() && strlen($from) > 3) {
            // dd(DB::connection()->getConfig());


            $redirect = Redirect::whereFrom($from)->first();
            if (empty($redirect->id)) {
                if ($from != $request->getRequestUri()) {
                    $redirect = Redirect::whereFrom($request->getRequestUri())->first();
                } else {
                    $parsed = parse_url($request->getRequestUri());
                    if (!empty($parsed['path'])) {
                        $url_path = $parsed['path'];

                        $redirect = Redirect::whereFrom($url_path)->first();
                    }
                }
            }

            if (!empty($redirect->id)) {
                /** @var Redirect $redirect */
                if (
                    !empty($redirect->to) &&
                    trim($redirect->from) != trim($redirect->to) &&
                    !empty($redirect->code) &&
                    in_array($redirect->code, Redirect::$CODES)
                ) {
                    $redirect->increment('hits');

                    $to = $redirect->to;

                    if (!empty($request->getQueryString()) && !str_contains($redirect->from, '?')) {
                        $to .= '?' . $request->getQueryString();
                    }

                    return redirect()
                        ->to($to, $redirect->code);
                }
            }
        }

        return $next($request);
    }
}
