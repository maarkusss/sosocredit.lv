<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Traits\WorksWithLanguageLocale;
use Illuminate\Http\JsonResponse;

class AppPrivacyController extends Controller
{
    use WorksWithLanguageLocale;

    protected string $locale;

    public function __construct() {
        $country_locale = $this->getLanguageLocaleFromHeaders();

        if (!empty($country_locale)) {
            app()->setLocale($country_locale);
        }

        $this->locale = $country_locale ?? app()->getLocale();
    }

    public function index(): JsonResponse
    {
        $privacy = Section::where('lang', $this->locale)->where('route_name', 'mobile-privacy-policy')->select('text')->first();
        $fullPrivacyPolicy = Section::where('lang', $this->locale)->where('template', 'privacy_policy')->select('route_name')->first();
        $fullLink = config('app.url') . '/' . $this->locale . '/' . $fullPrivacyPolicy->route_name;

        return new JsonResponse([
            'data' => [
                'privacy_policy' => $privacy->text ?? null,
                'link' => $fullLink ?? null,
            ]
        ]);
    }
}