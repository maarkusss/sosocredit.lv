<?php

namespace App\Http\Controllers;

use App\Models\MobileApp;

class AppLinkController extends Controller
{
    public function index()
    {
        $os = get_os(request()->server('HTTP_USER_AGENT'));

        $galaxy_store = env('GALAXY_STORE', false);
        $galaxy_store_app_link = env('GALAXY_STORE_APP_LINK');

        $android_app = MobileApp::query()->where('platform', MobileApp::PLATFORM_ANDROID)->where('active', 1)
            ->select([
                'id',
                'link',
            ])
            ->first();

        $ios_app = MobileApp::query()->where('platform', MobileApp::PLATFORM_IOS)->where('active', 1)
            ->select([
                'id',
                'link',
            ])->first();

        if (!empty($android_app->id)) {
            $android_link = $android_app->link . getMobileParameters();
        }

        if (!empty($ios_app)) {
            $ios_link = $ios_app->link;
        }

        if (!empty($galaxy_store)) {
            $android_link = $galaxy_store_app_link;
        }

        $link = '/' . app()->getLocale();

        if (!empty($os) && $os === 'Android' && !empty($android_link)) {
            $link = $android_link;
        } elseif (!empty($os) && ($os === 'iOS' || $os === 'macOS') && !empty($ios_link)) {
            $link = $ios_link;
        }

        return redirect($link);
    }
}
