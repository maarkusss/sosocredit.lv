<?php

namespace App\Http\Controllers\Api\OffersApi;

use App\Entity\UtmDataDTO;
use App\Http\Controllers\Controller;
use App\Services\LenderService;
use Illuminate\Http\JsonResponse;

class OfferController extends Controller
{
    public function __construct(protected LenderService $lender_service)
    {
        if (!empty(request()->getPreferredLanguage())) {
            app()->setLocale(request()->getPreferredLanguage());
        }
    }

    public function index(): JsonResponse
    {
        $locale = !empty(request()->getPreferredLanguage()) ? request()->getPreferredLanguage() : default_locale();
        $offers = $this->lender_service->getAll($locale);
        $utm_data = UtmDataDTO::fromAppRequest();

        $response = new JsonResponse([
            'data' => [
                'offers' => $offers,
            ],
        ]);

        if (!empty($utm_data->utm_source)) {
            $response = $response->header('X-Utm-Source', $utm_data->utm_source);
        }

        if (!empty($utm_data->utm_campaign)) {
            $response = $response->header('X-Utm-Campaign', $utm_data->utm_campaign);
        }

        return $response;
    }
}
