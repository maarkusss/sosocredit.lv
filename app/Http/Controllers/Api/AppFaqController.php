<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Section;
use App\Traits\WorksWithLanguageLocale;
use Illuminate\Http\JsonResponse;

class AppFaqController extends Controller
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
        $faqs = Faq::where('lang', $this->locale)->where('active', 1)->select('question', 'answer')->get();

        return new JsonResponse([
            'data' => [
                'faq' => $faqs,
            ]
        ]);
    }
}