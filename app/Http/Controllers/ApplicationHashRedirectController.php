<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Services\ApplicationService;
use App\Services\OfferPage\OfferPageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class ApplicationHashRedirectController extends Controller
{
    public function __construct(
        protected ApplicationService $application_service,
        protected OfferPageService   $offer_page_service
    ) {
    }

    /**
     * Redirect a consumer based on hoi token.
     *
     * @param string|null $hoi_token
     * @return RedirectResponse
     */
    public function index(string $hash = null): RedirectResponse
    {
        if (!$hash) {
            $hash = request()->cookie('hoi_token') ?? '';
        }

        if (Str::length($hash) === Application::FIRST_HASH_LENGTH) {
            $application = $this->application_service->getByFirstOfferHash($hash);
            $token = 'first-offer';
        } else {
            $application = $this->application_service->getBySecondOfferHash($hash);
            $token = 'second-offer';
        }

        if ($application) {
            if (!empty($application->lang) && in_array($application->lang, available_locales())) {
                App::setLocale($application->lang);
            }

            $link = $this->offer_page_service->buildLink($application, [
                'token' => $token,
            ]);

            $expiry_time_in_minutes = 60 * 24 * 30;

            return redirect($link)
                ->withCookie(cookie('offer_redirect', true, $expiry_time_in_minutes));
        }

        return redirect('/' . default_locale());
    }
}
