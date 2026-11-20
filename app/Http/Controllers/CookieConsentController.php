<?php

namespace App\Http\Controllers;

use App\Models\CookieConsent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CookieConsentController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        if (empty($request->cookie('visitor_id'))) {
            return response()->json(['status' => 'ok']);
        }
    
        $validated = $request->validate([
            'analytics'  => ['required', 'boolean'],
            'marketing'  => ['required', 'boolean'],
            'functional' => ['required', 'boolean'],
        ]);
    
        CookieConsent::create([
            ...$validated,
            'visitor_id' => $request->cookie('visitor_id'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    
        return response()->json(['status' => 'ok']);
    }
}