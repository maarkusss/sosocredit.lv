<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DeeplinkController extends Controller
{
    /**
     * Handle GoCardless completed deeplink fallback for web/non-app users.
     *
     * If the user has the app installed, the OS intercepts this URL and opens the app.
     * If the user does not have the app installed, this web route is hit and redirects
     * the user to the web application page.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function goCardlessCompleted(Request $request): RedirectResponse
    {
        $country = $request->query('country', config('app.country_code', 'lv'));
        $applicationToken = $request->query('application_token')
            ?? $request->query('applicationToken')
            ?? $request->query('token');

        if (!empty($applicationToken)) {
            $application = Application::where('token', $applicationToken)->first();
            $locale = $application->lang ?? $country ?? config('app.country_code', 'lv');

            return redirect("/{$locale}/consumers/applications/{$applicationToken}");
        }

        return redirect("/{$country}/consumers/dashboard");
    }
}
