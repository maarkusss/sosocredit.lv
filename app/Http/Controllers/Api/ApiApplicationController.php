<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationStatus;
use App\Models\Lender;
use App\Services\LenderConfigService;
use App\ValueObjects\LenderConfig;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * Lenders are partners added only on landing page, locally, without integration.
 * Providers are lenders who are integrated via API.
 * Providers can have lender instance.
 *
 * @property  array $provider_config
 */
class ApiApplicationController extends Controller
{
    const PROVIDER_DEFAULT_PRIORITY = 50;
    const DEFAULT_MULTIPLIER = 1.0;

    public function __construct()
    {
        $preferredLanguage = request()->getPreferredLanguage(['lv', 'ru']);
        if (in_array($preferredLanguage, ['lv', 'ru'])) {
            app()->setLocale($preferredLanguage);
        } else {
            app()->setLocale('lv'); // fallback to Latvian
        }
        $this->provider_config = config('providers');
    }

    /**
     * GET status of last export of the application
     *
     * @param string $application_token
     * @param string $hash
     * @return JsonResponse
     * @throws Throwable
     */
    public function getStatus(string $application_token, string $hash): JsonResponse
    {
        $common_status = "Pending";

        /** @var Application $application */
        $application = Application::query()->where('token', $application_token)->first();

        if (empty($application->id)) {
            Log::error('Application not found');
            abort(404);
        }

        if (!hash_equals($hash, $application->hash)) {
            Log::error('Hash mismatch');
            abort(app()->isProduction() ? 404 : 401);
        }

        if (!app()->isLocal() && now()->diffInDays($application->created_at) > 91) {
            abort(app()->isProduction() ? 400 : 403);
        }

        $page = str_replace("www.", "", Request::getHost());
        $app_id = $application->id;

        try {
            $baseUrl = config('services.goodaff.api_data_url');
            $url = $baseUrl . $page . '/' . $app_id;

            $response = Http::connectTimeout(3)
                ->timeout(30)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Api-Key' => config('services.goodaff.api_data_key'),
                ])->get($url);

            if ($response->notFound()) {
                Log::error('Response not found from api.data');
                abort(app()->isProduction() ? 400 : 404);
            }
        } catch (Throwable $e) {
            Log::error('ApiData req Failure: ' . $e);
            abort(503);
        }

        $json = $response->json();

        if (empty($json['data']['exports_summary'])) {
            return new JsonResponse([
                'data' => [
                    'status' => 'Pending',
                    'status_html' => '',
                    'empty_data' => true,
                ],
            ]);
        }

        $exports_summary = $json['data']['exports_summary'];
        $application->updateExportsSummary($exports_summary);

        $lenders_info = self::collectLenderInfo();
        $providers = self::collectProviderData($application, $exports_summary, $json, $application_token, $application->updated_at);
        $just_sorted_providers = self::sortProviders($providers);

        // Determine if all providers are rejected
        $all_providers_rejected = collect($exports_summary)
            ->filter(fn($decision) => in_array($decision, [
                "Refused",
                "Rejected",
                "Cancelled",
                "Error",
                "Cannot",
                "Invalid",
                "Expired",
                "PendingGC"
            ]))->count() === count($just_sorted_providers);

        $skipMergeLendersForLoanTypes = ['kredits-pret-kilu', 'kredits-pret-auto-kilu'];

        // Only merge lenders if all providers are rejected
        if ($all_providers_rejected && ! in_array($application->loantype, $skipMergeLendersForLoanTypes)) {
            $lenders = self::getLenders($application);
            if ($lenders) {
                $providers = self::mergeLendersToProviders($lenders, $providers);
            }
        }

        // Sort again after possibly adding lenders
        $lenders = self::sortProviders($providers);

        $count = count($just_sorted_providers);
        if (app()->isLocal()) {
            $count = 1;
        }

        $are_all_rejected = collect($exports_summary)->count() >= $count
            && collect($exports_summary)
                ->filter(fn($decision) => in_array($decision, [
                    "Refused",
                    "Rejected",
                    "Cancelled",
                    "Error",
                    "Cannot",
                    "Invalid",
                    "Expired",
                    "PendingGC"
                ]))->count() === collect($exports_summary)->count();

        $are_all_complete = collect($exports_summary)->count() >= $count
            && collect($exports_summary)
                ->filter(fn($decision) => in_array($decision, [
                    "",
                    "Pending",
                    "Waiting",
                    "Registered",
                    "Reviewing"
                ]))->count() < 1;

        $has_at_least_one_done = collect($exports_summary)->contains('Done');
        $waiting_for_links = false;
        $has_at_least_one_link = false;
        foreach ($just_sorted_providers as $provider) {
            if (!$provider['data']['link'] && !$are_all_complete) {
                $waiting_for_links = true;
            }
            if ($provider['data']['link'] && $provider['data']['status'] == 'Approved') {
                $has_at_least_one_link = true;
            }
        }

        if ($has_at_least_one_link && ! self::delayOfferExposure($json['data'], $just_sorted_providers)) {
            $common_status = "Ready";
        }
        if ($are_all_complete || $are_all_rejected) {
            if (!$waiting_for_links) {
                $common_status = "Completed";
            }
        }

        // check for cs processing
        if ( ! $are_all_rejected && ! $has_at_least_one_done &&
            ($json['data']['cs_processing'] == true || $json['data']['cs_deal_status'] == true)
        ) {
            $status_html = view('includes.applicant-form.step5-waiting-cs-disclaimer', [
                'cs_deal_status' => $json['data']['cs_deal_status'],
                'cs_processing' => $json['data']['cs_processing'],
            ])->render();

            return new JsonResponse([
                'data' => [
                    'status' => "Completed",
                    'status_html' => $status_html,
                ],
            ]);
        }

        // check for too many applications
        if ($json['data']['too_many_applications'] == true) {
            $status_html = view('includes.applicant-form.step5-waiting-applicationsoverlimit-disclaimer')
                ->render();

            return new JsonResponse([
                'data' => [
                    'status' => "Completed",
                    'status_html' => $status_html,
                ],
            ]);
        }

        if ($common_status == "Completed" || $common_status == "Ready") {
            $status_html = view('includes.applicant-form.step5-waiting-results', [
                'lenders' => $common_status == "Ready" ? $just_sorted_providers : $lenders,
                'are_all_rejected' => $are_all_rejected,
                'common_status' => $common_status,
                'lenders_info' => $lenders_info,
                'cs_deal_status' => $json['data']['cs_deal_status'],
            ])->render();
        } else {
            $status_html = '';
        }

        return new JsonResponse([
            'data' => [
                'status' => $common_status,
                'status_html' => $status_html,
                'has_approved' => $has_at_least_one_link,
            ],
        ]);
    }

    public function getApplicationStatusData(Application $application): array
    {
        $common_status = "in_review";
        $page = str_replace("www.", "", Request::getHost());
        $app_id = $application->id;

        try {
            $baseUrl = config('services.goodaff.api_data_url');
            $url = $baseUrl . $page . '/' . $app_id;

            $response = Http::connectTimeout(3)
                ->timeout(30)
                ->withHeaders([
                    'Accept'  => 'application/json',
                    'Api-Key' => config('services.goodaff.api_data_key'),
                ])->get($url);

            if ($response->notFound()) {
                return ['status' => 'pending', 'offers' => []];
            }
        } catch (Throwable $e) {
            Log::error('[Offers-api]: ApiData req Failure: ' . $e);
            return ['status' => 'pending', 'offers' => []];
        }

        $json = $response->json();

        if (empty($json['data']['exports_summary'])) {
            return ['status' => 'pending', 'offers' => []];
        }

        $exports_summary = $json['data']['exports_summary'];
        $application->updateExportsSummary($exports_summary);

        $providers = self::collectProviderData($application, $exports_summary, $json, $application->token, $application->updated_at);
        $just_sorted_providers = self::sortProviders($providers);

        $all_providers_rejected = collect($exports_summary)
        ->filter(fn($decision) => in_array($decision, [
            "Refused",
            "Rejected",
            "Cancelled",
            "Error",
            "Cannot",
            "Invalid",
            "Expired",
            "PendingGC"
        ]))->count() === count($just_sorted_providers);

        $skipMergeLendersForLoanTypes = ['kredits-pret-kilu', 'kredits-pret-auto-kilu'];

        if ($all_providers_rejected && ! in_array($application->loantype, $skipMergeLendersForLoanTypes)) {
            $lenders = self::getLenders($application);
            if ($lenders) {
                $providers = self::mergeLendersToProviders($lenders, $providers);
            }
        }

        $lenders = self::sortProviders($providers);

        $count = count($just_sorted_providers);
        if (app()->isLocal()) $count = 1;

        $are_all_rejected = collect($exports_summary)->count() >= $count
            && collect($exports_summary)
                ->filter(fn($decision) => in_array($decision, [
                    "Refused",
                    "Rejected",
                    "Cancelled",
                    "Error",
                    "Cannot",
                    "Invalid",
                    "Expired",
                    "PendingGC"
                ]))->count() === collect($exports_summary)->count();

        $are_all_complete = collect($exports_summary)->count() >= $count
            && collect($exports_summary)
                ->filter(fn($decision) => in_array($decision, [
                    "",
                    "Pending",
                    "Waiting",
                    "Registered",
                    "Reviewing"
                ]))->count() < 1;


        $waiting_for_links = false;
        $has_at_least_one_link = false;
        foreach ($just_sorted_providers as $provider) {
            if (!$provider['data']['link'] && !$are_all_complete) {
                $waiting_for_links = true;
            }
            if ($provider['data']['link'] && $provider['data']['status'] == 'Approved') {
                $has_at_least_one_link = true;
            }
        }

        if ($has_at_least_one_link) $common_status = "ready";
        if (($are_all_complete || $are_all_rejected) && !$waiting_for_links) $common_status = "completed";

        return [
            'status'       => $common_status,
            'has_approved' => $has_at_least_one_link,
            'offers'       => self::formatOffers($lenders, $application),
        ];
    }

    public function getToken(string $application_id): JsonResponse
    {
        /** @var Application $application */
        $application = Application::find($application_id);
        if (!$application) {
            return response()->json(['error' => 'Application not found'], 404);
        }

        return response()->json(['token' => $application->token]);
    }

    private static function collectProviderData(
        Application $application,
        mixed $exports_summary,
        mixed $json,
        string $application_token,
        string $application_date
    ): array
    {
        $providers = self::collectProviders($application);
        $result = [];

        foreach ($providers as $provider) {
            $abbreviation = $provider['abbreviation'];
            $slug = $provider['slug'];
            $status = $exports_summary[$slug] ?? 'Pending';
            $name = $provider['name'] ?? '';

            $link = $json['data'][$abbreviation . '_link']
                ? route('consumers.applications.open-link', ['token' => $application_token, 'provider' => $slug])
                : '';

            $details = $json['data'][$abbreviation . '_details'] ?? [];

            // can be empty at this point !
            $lenderConfig = new LenderConfig($json['data'][$abbreviation . '_config'] ?? []);

            if ($provider['offer_expiration']) {
                $expiration_timestamp = strtotime($application_date . ' + ' . $provider['offer_expiration'] . ' hours');
                if (time() > $expiration_timestamp && $status == 'Approved') {
                    $status = 'Expired';
                }
            }

            if ($status == 'Expired') {
                $link = 'expired';
            }

            $result[$abbreviation] = [
                'name' => $name,
                'status' => $status,
                'link' => $link,
                'expiration' => $provider['offer_expiration'] ? $expiration_timestamp : null,
                'details' => $details,
                'lender_config' => $lenderConfig,
                'requires_additional_field_update' => LenderConfigService::requiresAdditionalFieldUpdate($lenderConfig, $json),
                'base_priority' => $provider['base_priority'] ?? null,
            ];

            if (in_array($slug, ['mogo', 'primero'])) {
                if (isset($json['data']['exports_' . $slug][0]['response']['alternate_offer'])) {
                    $alternate_offer = $json['data']['exports_' . $slug][0]['response']['alternate_offer'];
                    $result[$abbreviation]['alternates_to'] = str_contains($alternate_offer, 'car') ? 'car' : 'installment';
                }
            }

            if ($slug == 'bino') {
                if (isset($json['data']['exports_' . $slug][0]['response']['offer']['discount_applied'])
                    && $json['data']['exports_' . $slug][0]['response']['offer']['discount_applied'])
                {
                    $discount_periods = $json['data']['exports_' . $slug][0]['response']['offer']['discount_periods'] ?? null;
                    $discount_values = $json['data']['exports_' . $slug][0]['response']['offer']['discount_values'] ?? null;
                    $result[$abbreviation]['discount'] = [
                        'period' => $discount_periods ?? null,
                        'type' => $discount_values[0]['type'] ?? null,
                        'unit' => $discount_values[0]['unit'] ?? null,
                        'value' => $discount_values[0]['value'] ?? null,
                    ];
                }
            }
        }

        return $result;
    }

    private static function sortProviders(array $providers): array
    {
        $providers_with_priority = [];
        foreach ($providers as $key => $provider) {
            $base_priority = $provider['base_priority'] ?? self::PROVIDER_DEFAULT_PRIORITY;
            $status = ApplicationStatus::byName($provider['status']);
            $status_multiplier = $status->multiplier ?? self::DEFAULT_MULTIPLIER;
            $providers_with_priority[$key] = [
                'data' => $provider,
                'priority' => $base_priority * $status_multiplier
            ];
        }

        uasort($providers_with_priority, fn($a, $b) => $b['priority'] <=> $a['priority']);
        return $providers_with_priority;
    }

    private static function collectProviders(Application $application): array
    {
        $shouldExportTo = array_keys($application->exports_summary);
        return Lender::query()
            ->with(['data'])
            ->where('is_provider', 1)
            ->whereIn('slug', $shouldExportTo)
            ->get()
            ->keyBy('name')
            ->toArray();
    }

    private static function collectLenderInfo(): array
    {
        return Lender::query()
            ->with(['data'])
            ->orderBy('position', 'ASC')
            ->get()
            ->map(function ($lender) {
                $lenderArray = $lender->toArray();
                $lenderArray['rejected_statuses'] = $lender->rejected_statuses
                    ? json_decode($lender->rejected_statuses, true)
                    : [];
                return $lenderArray;
            })
            ->keyBy('name')
            ->toArray();
    }

    private static function collectLenders(): Collection
    {
        return Lender::active()
            ->with(['data'])
            ->where('is_provider', 0)
            ->where('is_broker', 0)
            ->orderBy('position', 'ASC')
            ->limit(5)
            ->get();
    }

    private static function getLenders(Application $application): ?array
    {
        $lenders = [];
        $lenders_model = self::collectLenders();
        foreach ($lenders_model as $lender) {
            $amount_example = $application->amount;
            if ($lender->max_amount < $amount_example) {
                $amount_example = $lender->max_amount;
            }
            $term = $application->term_months;
            $max_apr = !empty($lender->data->max_apr) ? (float)$lender->data->max_apr : 51;
            $monthly_payment_example = round($amount_example * (100 + $max_apr) / 100 / $term, 2);

            if ($lender->position && $lender->active) {
                $lender->amount_example = $amount_example;
                $lender->monthly_payment_example = $monthly_payment_example;
                if ($lender->frequency) {
                    if (rand(1, $lender->frequency) == $lender->frequency) {
                        $lenders[$lender->position] = $lender;
                    }
                } else {
                    $lenders[$lender->position] = $lender;
                }
            }
        }
        return $lenders;
    }

    private static function mergeLendersToProviders(array $lenders, array $providers): array
    {
        if (empty($lenders)) {
            return $providers;
        }

        foreach ($lenders as $lender) {
            if (empty($lender['id']) || empty($lender['name'])) {
                continue;
            }
            $providers[] = [
                'name' => $lender['name'],
                'status' => 'Lender',
                'link' => route('go', [$lender['id'], $lender['position']]),
                'details' => [
                    'link' => '',
                    'gpl' => $lender['data']['max_apr'] ?? '',
                    'monthly_payment' => (string)($lender['monthly_payment_example'] ?? '0'),
                    'max_amount' => (int)($lender['max_amount'] ?? 0)
                ],
                'config' => [
                    'key' => $lender['name'] ?? '',
                    'logo' => $lender['image'] ?? '',
                    'rejected_statuses' => [],
                ],
            ];
        }

        return $providers;
    }

    private static function formatOffers(array $lenders, Application $application): array
    {
        $providers = collect(self::collectProviders($application))->keyBy('abbreviation');
        $result = [];

        foreach ($lenders as $abbreviation => $lender) {
            $data = $lender['data'];

            if (empty($data['status'])) {
                continue;
            }

            $details = $data['details'] ?? [];

            $expiration_datetime = null;
            if (!empty($data['expiration'])) {
                $expiration_datetime = \Carbon\Carbon::createFromTimestamp($data['expiration'])->format('Y-m-d H:i');
            }

            $rawStatus = $data['status'];
            $status = match(strtolower($rawStatus)) {
                'reviewing', 'registered', 'waiting'  => 'in_review',
                'approved'                            => 'approved',
                'rejected', 'refused', 'error'        => 'rejected',
                'cannot', 'invalid', 'pendinggc'      => 'cannot',
                'expired'                             => 'expired',
                'done'                                => 'done',
                'lender'                              => 'lender',
                default                               => strtolower($rawStatus),
            };

            if ($status === 'lender') {
                $result[] = [
                    'id'                  => isset($data['config']['key']) ? (string) $data['config']['key'] : null,
                    'provider_type'       => 'lender',
                    'status'              => 'lender',
                    'affiliate_link'      => !empty($data['link']) ? $data['link'] : null,
                    'image_url'           => isset($data['config']['logo']) ? config('app.url') . $data['config']['logo'] : null,
                    'is_highlighted'      => false,
                    'slogan'              => null,
                    'amount'              => isset($details['max_amount']) && (int) $details['max_amount'] > 0 ? (int) $details['max_amount'] : null,
                    'monthly_payment'     => isset($details['monthly_payment']) && (float) $details['monthly_payment'] > 0 ? (float) $details['monthly_payment'] : null,
                    'apr'                 => isset($details['gpl']) && (float) $details['gpl'] > 0 ? (float) $details['gpl'] : null,
                    'term'                => isset($details['term']) && (int) $details['term'] > 0 ? (int) $details['term'] : null,
                    'benefits'            => [],
                    'expiration_datetime' => null,
                ];
                continue;
            }

            $provider = $providers[$abbreviation] ?? null;

            $is_provider = !empty($provider) && !empty($provider['is_provider']) && empty($provider['is_broker']);
            $is_highlighted = $status === 'approved' && $is_provider;

            $gpl = isset($details['gpl']) && (float) $details['gpl'] > 0 ? (float) $details['gpl'] : null;
            $monthly_payment = isset($details['monthly_payment']) && (float) $details['monthly_payment'] > 0 ? (float) $details['monthly_payment'] : null;
            $amount = isset($details['max_amount']) && (int) $details['max_amount'] > 0 ? (int) $details['max_amount'] : null;
            $term = isset($details['term']) && (int) $details['term'] > 0 ? (int) $details['term'] : null;

            if ($status === 'expired' && ($amount === null || $monthly_payment === null || $gpl === null)) {
                continue;
            }

            $result[] = [
                'id'                  => isset($provider['id']) ? (string) $provider['id'] : null,
                'provider_type'       => $is_provider ? 'provider' : 'lender',
                'status'              => $status,
                'affiliate_link'      => !empty($data['link']) && $data['link'] !== 'expired'
                                       ? route('offers-api.applications.open-link', ['application' => $application->id, 'provider' => $abbreviation])
                                       : null,
                'image_url'           => isset($provider['image']) ? config('app.url') . $provider['image'] : null,
                'is_highlighted'      => $is_highlighted,
                'slogan'              => $provider['data']['slogan'] ?? null,
                'amount'              => $amount,
                'monthly_payment'     => $monthly_payment,
                'apr'                 => $gpl,
                'term'                => $term,
                'benefits'            => $provider['data']['benefits'] ?? [],
                'expiration_datetime' => $expiration_datetime,
            ];
        }

        return $result;
    }

    private static function delayOfferExposure(array $jsonData, array $providers): bool
    {
        if ( ! isset($jsonData['delay_offer_exposure'])) {
            return false;
        }

        $delayMinutes = (int) $jsonData['delay_offer_exposure'];
        $exportTimes = [];

        foreach ($providers as $provider) {
            $lenderConfig = $provider['data']['lender_config'];
            $exports = $jsonData['exports_' . $lenderConfig->name] ?? [];

            if (empty($exports)) {
                continue;
            }

            $approvedExport = $lenderConfig->getApprovedExport($exports);
            if ($approvedExport) {
                $exportTimes[] = Carbon::createFromFormat('d.m.Y H:i:s', $approvedExport['created_at']);
            }
        }

        if (empty($exportTimes)) {
            return false;
        }

        $earliest = min($exportTimes);

        return $earliest->copy()->addSeconds($delayMinutes)->isFuture();
    }
}
