<?php

namespace App\Http\Controllers\Api;

use App\Entity\UtmDataDTO;
use App\Events\ApplicationPhoneVerified;
use App\Events\ConsumerRequiresSmsVerificationCode;
use App\Http\Controllers\Consumers\ConsumerDashboardController;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationJsonObjectModel;
use App\Http\Resources\GoCardlessOptionJsonObjectModel;
use App\Http\Resources\UserJsonObjectModel;
use App\Models\Application;
use App\Models\Consumer;
use App\Services\NordigenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Consumer\ConsumerSMSVerificationCode;
use App\Models\Lender;
use App\Models\ProviderLinkVisit;
use App\Models\Section;
use App\Services\ApplicationService;
use App\Services\ConsumerService;
use App\Services\GoodAff\GoodAffDataService;
use App\Traits\WorksWithLanguageLocale;
use Exception;
use Goodday\Goodwall\Services\GoodwallService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Str;
use stdClass;
use Throwable;

class AppApplicationController extends Controller
{
    use WorksWithLanguageLocale;
    protected string $locale;
    public ?UtmDataDTO $utm_data;

    public function __construct(protected ApplicationService $application_service, protected ConsumerService $consumer_service, protected GoodAffDataService $data_service) {

        $country_locale = $this->getLanguageLocaleFromHeaders();

        if (!empty($country_locale)) {
            app()->setLocale($country_locale);
        }

        $this->locale = $country_locale ?? app()->getLocale();
        $this->utm_data = UtmDataDTO::fromAppRequest();
    }

    public function appCalculator(): JsonResponse
    {
        $applicationTypes = Section::active()
            ->where('lang', $this->locale)
            ->where('template', Section::TEMPLATE_APPLICANT_FORM_LANDING_PAGE)
            ->select('common_name', 'name', 'json')
            ->get();

        $data = $applicationTypes->map(function (Section $section) {
            $json = $section->json;

            $toDouble = fn($v) => isset($v) ? (float) $v : null;
            $toInt    = fn($v) => isset($v) ? (int)   $v : null;

            return [
                'id'   => $section->common_name,
                'name' => $section->name,
                'percent_for_calculations' => $toDouble($json['slider_percent_amount'] ?? null),
                'amount' => [
                    'min'     => $toDouble($json['slider_min_amount']   ?? null),
                    'max'     => $toDouble($json['slider_max_amount']   ?? null),
                    'initial' => $toDouble($json['slider_start_amount'] ?? null),
                    'step'    => $toDouble($json['slider_step_amount']  ?? null),
                ],
                'term' => [
                    'min'     => $toInt($json['slider_min_term']   ?? null),
                    'max'     => $toInt($json['slider_max_term']   ?? null),
                    'initial' => $toInt($json['slider_start_term'] ?? null),
                    'step'    => $toInt($json['slider_step_term']  ?? 1),
                ],
            ];
        });

        return new JsonResponse([
            'data' => [
                'calculator_by_credit_types' => $data->values(),
            ]
        ]);
    }

    public function appCreateApplication(Request $request): JsonResponse
    {
        $token = Str::random(55);
        $token .= Str::random(7);

        $validated = $request->validate([
            'credit_type' => ['required', 'string', 'max:63'],
            'credit_amount' => ['required', 'numeric'],
            'credit_term' => ['required', 'integer'],
        ]);

        auth('sanctum')->check();
        $consumer = auth('sanctum')->user();

        $section = Section::where('common_name', $validated['credit_type'])
            ->where('lang', $this->locale)
            ->where('template', Section::TEMPLATE_APPLICANT_FORM_LANDING_PAGE)
            ->first();

        if (!$section) {
            return $this->errorResponse(['unknown_credit_type'], 404);
        }

        $application = Application::create([
            'consumer_id' => $consumer->id ?? 0,
            'email' => $consumer->email ?? null,
            'phone' => $consumer->phone ?? null,
            'loantype' => $section->common_name,
            'amount' => (int) $validated['credit_amount'],
            'term_months' => $validated['credit_term'],
            'user_agent' => RequestFacade::userAgent(),
            'ip' => $request->ip(),
            'lang' => $this->locale,
            'token' => $token,
            'agree_to_terms' => 0,
            'subscribed' => 0,
            'section_id' => $section->id,
            'utm_source' => $this->utm_data->utm_source,
        ]);

        $this->application_service->updateCompletionPercentage($application, 20);

        return new JsonResponse([
            'data' => [
                'application' => new ApplicationJsonObjectModel($application),
            ]
        ]);
    }

    public function appGetApplications(Request $request): JsonResponse
    {
        $consumer = $request->user();

        if (empty($consumer->id)) {
            return $this->errorResponse(['consumer_not_found'], 404);
        }

        $applications = Application::where('consumer_id', $consumer->id)
            ->where('created_at', '>=', now()->subDays(3)->format('Y-m-d 00:00:00'))
            ->where('completion_percentage', '>=', 80)
            ->orderBy('id', 'DESC')
            ->limit(10)
            ->get();

        $apiController = new ApiApplicationController();

        $mappedApplications = $applications->map(function (Application $application) use ($apiController, $request) {
            $statusData = $apiController->getApplicationStatusData($application);

            return (new ApplicationJsonObjectModel($application))
                ->additional(['status' => $statusData['status']])
                ->toArray($request);
        });

        return new JsonResponse([
            'data' => [
                'applications' => $mappedApplications,
            ]
        ]);
    }

    public function appGetUnfinishedApplication(Request $request, Application $application): JsonResponse
    {
        $consumer = $request->user();

        if ($application->consumer_id !== $consumer->id) {
            return $this->errorResponse(['application_not_found'], 404);
        }

        $statusData = (new ApiApplicationController())->getApplicationStatusData($application);

        return new JsonResponse([
            'data' => [
                'application' => (new ApplicationJsonObjectModel($application))->additional([
                    'status' => $statusData['status'],
                    'offers' => $statusData['offers'] ?? [],
                ])->toArray($request),
            ]
        ]);
    }

    public function appTokenApplication(Request $request, ?string $token = null): JsonResponse
    {
        $token = $token
            ?? $request->query('applicationToken')
            ?? $request->query('application_token')
            ?? $request->query('token');

        if (empty($token)) {
            return $this->errorResponse(['token_required'], 400);
        }

        $consumer = $request->user();

        $application = Application::where('token', $token)
            ->where('consumer_id', $consumer->id)
            ->first();

        if (empty($application->id)) {
            return $this->errorResponse(['application_not_found'], 404);
        }

        $statusData = (new ApiApplicationController())->getApplicationStatusData($application);

        return new JsonResponse([
            'data' => [
                'application' => (new ApplicationJsonObjectModel($application))->additional([
                    'status' => $statusData['status'],
                    'offers' => $statusData['offers'] ?? [],
                ])->toArray($request),
            ]
        ]);
    }

    public function appUpdateApplication(Request $request, Application $application): JsonResponse
    {
        $consumer = $request->user();

        if ($application->consumer_id !== $consumer->id) {
            return $this->errorResponse(['application_not_found'], 404);
        }

        $validated = $request->validate([
            'credit_type' => ['nullable', 'string', 'max:63'],
            'credit_amount' => ['nullable', 'numeric'],
            'credit_term' => ['nullable', 'integer'],
            'lgp_form_fields' => ['nullable', 'array'],
        ]);

        /*
        * Nosakām aktīvo sadaļu.
        *
        * Ja credit_type nav atsūtīts, izmantojam aplikācijas esošo sadaļu.
        */
            $section = null;

            if (!empty($validated['credit_type'])) {
                $section = Section::where('common_name', $validated['credit_type'])
                    ->where('lang', $application->lang)
                    ->where(
                        'template',
                        Section::TEMPLATE_APPLICANT_FORM_LANDING_PAGE
                    )
                    ->first();

                if (!$section) {
                    return $this->errorResponse(['unknown_credit_type'], 404);
                }

                $application->loantype = $section->common_name;
                $application->section_id = $section->id;
            } else {
                $section = Section::find($application->section_id);
            }

            $isMortgage = in_array(
                $section?->common_name,
                ['kredits-pret-kilu'],
                true
            );

            $isCarMortgage = in_array(
                $section?->common_name,
                ['kredits-pret-auto-kilu'],
                true
            );

            if (!empty($validated['credit_amount'])) {
                $application->amount = (int) $validated['credit_amount'];
            }

            if (!empty($validated['credit_term'])) {
                $application->term_months = $validated['credit_term'];
            }

            $fields = $request->input('lgp_form_fields', []);

            if (!empty($fields)) {
                $fields = collect($fields)
                    ->mapWithKeys(
                        fn($value, $key) => [trim($key, '"') => $value]
                    )
                    ->toArray();

                if (isset($fields['first_name'])) {
                    $application->first_name = $fields['first_name'];
                }

                if (isset($fields['last_name'])) {
                    $application->last_name = $fields['last_name'];
                }

                if (isset($fields['personal_code'])) {
                    $application->personal_code = $fields['personal_code'];
                }

                if (isset($fields['age'])) {
                    try {
                        $date = \Carbon\Carbon::createFromFormat(
                            'd.m.Y',
                            $fields['age']
                        ) ?? \Carbon\Carbon::parse($fields['age']);

                        $application->age = $date->format('Y-m-d');
                    } catch (\Exception $e) {
                        $application->age = $fields['age'];
                    }
                }

                if (isset($fields['employment_status'])) {
                    $application->employment_status =
                        $fields['employment_status'];
                }

                if (isset($fields['education'])) {
                    $application->education = $fields['education'];
                }

                if (isset($fields['marital_status'])) {
                    $application->marital_status = $fields['marital_status'];
                }

                if (isset($fields['dependants'])) {
                    $application->dependants = $fields['dependants'];
                }

                if (isset($fields['salary'])) {
                    $application->salary = $fields['salary'];
                }

                if (isset($fields['monthly_debit'])) {
                    $application->monthly_debit = $fields['monthly_debit'];
                }

                if (isset($fields['address_street'])) {
                    $application->address_street = $fields['address_street'];
                }

                if (isset($fields['address_city'])) {
                    $application->address_city = $fields['address_city'];
                }

                if (isset($fields['address_zipcode'])) {
                    $application->address_zipcode = $fields['address_zipcode'];
                }

                if (isset($fields['disbursement_iban'])) {
                    $application->disbursement_iban =
                        $fields['disbursement_iban'];
                }

                if (isset($fields['is_political_person'])) {
                    $application->is_political_person =
                        $fields['is_political_person'];
                }

                /*
                 * Parastām sadaļām vērtība nāk no aplikācijas formas.
                 */
                if (!$isMortgage && isset($fields['has_real_property'])) {
                    $application->has_real_property =
                        $fields['has_real_property'];
                }

                if (!$isCarMortgage && isset($fields['has_car'])) {
                    $application->has_car = $fields['has_car'];
                }

                if (isset($fields['cadastral_number'])) {
                    $application->cadastral_number =
                        $fields['cadastral_number'];
                }

                if (isset($fields['car_number'])) {
                    $application->car_number = $fields['car_number'];
                }

                if (isset($fields['has_bank_attachment'])) {
                    $application->has_bank_attachment =
                        $fields['has_bank_attachment'];
                }
            }

            /*
             * Speciālajiem kredītu veidiem vērtības uzliek serveris.
             * Aplikācijai šie lauki nav jārāda un nav jāsūta.
             */
            if ($isMortgage) {
                $application->has_real_property = 1;
            }

            if ($isCarMortgage) {
                $application->has_car = 1;
            }

            $application->save();

        try {
            $application = $this->application_service->fillExportsSummaryWithActiveProviders($application);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            // Log it but don't let a provider config failure break the update
            Log::warning('[Offers-api]: APP fillExportsSummaryWithActiveProviders failed', [
                'application_id' => $application->id,
                'message' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            Log::error('[Offers-api]: Unexpected error in fillExportsSummaryWithActiveProviders', [
                'application_id' => $application->id,
                'message' => $e->getMessage(),
            ]);
        }


        $this->application_service->updateCompletionPercentage($application, 70);

        return new JsonResponse([
            'data' => [
                'application' => new ApplicationJsonObjectModel($application),
            ]
        ]);
    }

    public function appContactInfo(Request $request, Application $application): JsonResponse
    {
        if ($request->filled('contact_phone_number')) {
            $phoneNumber = preg_replace('/\D+/', '', trim($request->input('contact_phone_number')));

            $request->merge([
                'contact_phone_number' => $phoneNumber,
            ]);

            if ($phoneNumber !== env('API_TEST_PHONE_NUMBER') && !preg_match('/^2\d{7}$/', $phoneNumber)) {
                return $this->errorResponse(['phone_number_format_invalid'], 404);
            }
        }

        $validated = $request->validate([
            'contact_phone_number' => ['nullable', 'string', 'max:20'],
            'contact_email' => ['nullable', 'email', 'max:191'],
        ]);

        $application->phone = $validated['contact_phone_number'] ?? null;
        $application->email = $validated['contact_email'] ?? null;
        $application->save();

        auth('sanctum')->check();
        $consumer = auth('sanctum')->user();

        // Ja application jau pieder consumerim - nav vajadzīga verifikācija
        if (!empty($application->consumer_id) && !empty($consumer)) {
            if($application->consumer_id === $consumer->id) {
                $application->phone_verified_at = now();
                $application = $this->application_service->update(application: $application, request: $request);
                $this->application_service->updateCompletionPercentage($application, 40);

                if (!empty($application->phone_verified_at)) {
                    event(new ApplicationPhoneVerified($application));
                }

                if (!empty($application->consumer) && !$application->hasVerifiedPhone() && $application->phone_verified_at) {
                    $application->consumer->markPhoneAsVerified();
                }

                $consumer = $this->consumer_service->update($consumer, $request, 'app');

                return new JsonResponse([
                    'data' => [
                        'verification_required' => false,
                    ]
                ]);
            }
        }

        $consumer = $this->consumer_service->getFromRequest($request);

        if (!empty($consumer->id)) {
            $consumer = $this->consumer_service->update($consumer, $request, 'app');
        } else{
            $consumer = $this->consumer_service->create($request, 'app');
        }

        $application = $this->application_service->update(application: $application, request: $request);
        $this->application_service->updateCompletionPercentage($application, 40);

        if (!empty($application->phone) && empty($application->phone_verified_at)) {
            $should_verify_phone = true;

            if ($should_verify_phone === true &&
                !$this->application_service->isSpammer($application) &&
                !$this->application_service->requestedTooManyVerificationCodes($application)
            ) {
                $recentSms = ConsumerSMSVerificationCode::where('phone_number', $application->consumer->phone)
                    ->where('created_at', '>', now()->subSeconds(3))
                    ->exists();
                if (!$recentSms) {
                    event(new ConsumerRequiresSmsVerificationCode($application->consumer, resend: false));
                }
            }
        }

        return new JsonResponse([
            'data' => [
                'verification_required' => true,
            ]
        ]);
    }

    public function appContactInfoVerification(Request $request, Application $application): JsonResponse
    {
        $validated = $request->validate([
            'contact_phone_number' => ['nullable', 'string', 'max:20'],
            'verification_code' => ['required', 'string', 'min:5', 'max:191'],
        ]);

        if (!ConsumerSMSVerificationCode::query()
            ->where('phone_number', $validated['contact_phone_number'])
            ->where('code', $validated['verification_code'])
            ->where('expires_at', '>=', date('Y-m-d H:i:s'))
            ->exists()) {

            return $this->errorResponse(['verification_code_invalid'], 404);
        }

        $application->phone_verified_at = now();
        $application->save();
        event(new ApplicationPhoneVerified($application));

        if (!empty($application->consumer) && !$application->hasVerifiedPhone() && $application->phone_verified_at) {
            $application->consumer->markPhoneAsVerified();
        }

        $consumer = $application->consumer;

        return $this->userRegisteredResponse($consumer);
    }

    public function appGetApplicationStatus(Request $request, Application $application): JsonResponse
    {
        $consumer = $request->user();

        if ($application->consumer_id !== $consumer->id) {
            return $this->errorResponse(['application_not_found'], 404);
        }

        Log::info('[Offers-api]: Fetching status', [
            'application_id' => $application->id,
            'consumer_id'    => $consumer->id,
        ]);

        $statusData = (new ApiApplicationController())->getApplicationStatusData($application);

        Log::info('[Offers-api]: Status result', [
            'application_id' => $application->id,
            'status'         => $statusData['status'],
            'has_approved'   => $statusData['has_approved'] ?? false,
        ]);

        return new JsonResponse([
            'data' => [
                'status' => $statusData['status'],
                'offers' => [],
            ]
        ]);
    }


    public function appGoCardless(Request $request, $application): JsonResponse
    {
        $application = $application instanceof Application
            ? $application
            : (is_numeric($application) ? Application::find($application) : Application::where('token', $application)->first());

        if (empty($application?->id)) {
            return $this->errorResponse(['application_not_found'], 404);
        }

        $consumer = $request->user();

        if ($application->consumer_id !== $consumer->id) {
            return $this->errorResponse(['application_not_found'], 404);
        }

        $options = $this->getGoCardlessInstitutions($application);

        return new JsonResponse([
            'data' => [
                'go_cardless' => [
                    'options' => GoCardlessOptionJsonObjectModel::collection($options)->toArray($request),
                ],
            ],
        ]);
    }

    public function getGoCardlessInstitutions(Application $application): array
    {
        $country = strtolower($application->section?->lang ?? config('app.country_code', 'lv'));

        if ($country === 'lt') {
            $institutions = [
                [
                    'id' => 'SWEDBANK_HABALT22',
                    'name' => 'Swedbank',
                    'bic' => 'HABALT22',
                    'transaction_total_days' => '730',
                    'countries' => ['LT'],
                    'logo_url' => asset('/images/logos/svg/swedbank.svg'),
                ],
                [
                    'id' => 'SEB_CBVILT2X',
                    'name' => 'SEB',
                    'bic' => 'CBVILT2X',
                    'transaction_total_days' => '730',
                    'countries' => ['LT'],
                    'logo_url' => asset('/images/logos/svg/seb.svg'),
                ],
                [
                    'id' => 'LUMINOR_AGBLLT2X',
                    'name' => 'Luminor',
                    'bic' => 'AGBLLT2X',
                    'transaction_total_days' => '365',
                    'countries' => ['LT'],
                    'logo_url' => asset('/images/logos/svg/luminor.svg'),
                ],
                [
                    'id' => 'CITADELE_INDLLT2X',
                    'name' => 'Citadele',
                    'bic' => 'INDLLT2X',
                    'transaction_total_days' => '730',
                    'countries' => ['LT'],
                    'logo_url' => asset('/images/logos/svg/citadele.svg'),
                ],
                [
                    'id' => 'REVOLUT_REVOLT21',
                    'name' => 'Revolut',
                    'bic' => 'REVOLT21XXX',
                    'transaction_total_days' => '730',
                    'countries' => ['LT'],
                    'logo_url' => asset('/images/logos/svg/revolut.svg'),
                ],
            ];
        } else {
            $institutions = [
                [
                    'id' => 'SWEDBANK_HABALV22',
                    'name' => 'Swedbank',
                    'bic' => 'HABALV22',
                    'transaction_total_days' => '730',
                    'countries' => ['LV'],
                    'logo_url' => asset('/images/logos/svg/swedbank.svg'),
                ],
                [
                    'id' => 'SEB_UNLALV2X',
                    'name' => 'SEB',
                    'bic' => 'UNLALV2X',
                    'transaction_total_days' => '730',
                    'countries' => ['LV'],
                    'logo_url' => asset('/images/logos/svg/seb.svg'),
                ],
                [
                    'id' => 'CITADELE_PARXLV22',
                    'name' => 'Citadele',
                    'bic' => 'PARXLV22',
                    'transaction_total_days' => '730',
                    'countries' => ['LV'],
                    'logo_url' => asset('/images/logos/svg/citadele.svg'),
                ],
                [
                    'id' => 'LUMINOR_RIKOLV2X',
                    'name' => 'Luminor',
                    'bic' => 'RIKOLV2X',
                    'transaction_total_days' => '365',
                    'countries' => ['LV'],
                    'logo_url' => asset('/images/logos/svg/luminor.svg'),
                ],
                [
                    'id' => 'REVOLUT_REVOLT21',
                    'name' => 'Revolut',
                    'bic' => 'REVOLT21XXX',
                    'transaction_total_days' => '730',
                    'countries' => ['LV'],
                    'logo_url' => asset('/images/logos/svg/revolut.svg'),
                ],
                [
                    'id' => 'WISE_EEA_TRWIGB22',
                    'name' => 'Wise',
                    'bic' => 'TRWIGB22XXX',
                    'transaction_total_days' => '730',
                    'countries' => ['LV'],
                    'logo_url' => asset('/images/logos/svg/wise.svg'),
                ],
                [
                    'id' => 'INDEXO_IDXOLV22',
                    'name' => 'INDEXO',
                    'bic' => 'IDXOLV22',
                    'transaction_total_days' => '730',
                    'countries' => ['LV'],
                    'logo_url' => asset('/images/logos/svg/indexo.svg'),
                ],
                [
                    'id' => 'BLUEORANGE_CBBRLV22',
                    'name' => 'BluOr',
                    'bic' => 'CBBRLV22',
                    'transaction_total_days' => '90',
                    'countries' => ['LV'],
                    'logo_url' => asset('/images/logos/svg/bluor.svg'),
                ],
            ];
        }

        if (!app()->isProduction()) {
            $institutions[] = [
                'id' => NordigenService::$SANDBOX_INSTITUTION_ID,
                'name' => 'Test Sandbox',
                'bic' => 'SANDBOXFINANCE_SFIN0000',
                'transaction_total_days' => '730',
                'countries' => [strtoupper($country)],
                'logo_url' => asset('/images/logos/png/test.jpg'),
            ];
        }

        foreach ($institutions as &$inst) {
            $inst['url'] = url("/applications/{$application->id}/go-cardless/{$inst['id']}?hash={$application->signature}");
            $inst['link'] = $inst['url'];
        }

        return $institutions;
    }

    public function appGoCardlessRedirect(Request $request, $application, string $institution_id): RedirectResponse
    {
        $application = $application instanceof Application
            ? $application
            : (is_numeric($application) ? Application::find($application) : Application::where('token', $application)->first());

        if (empty($application?->id)) {
            abort(404);
        }

        if (!hash_equals($application->signature, (string) $request->query('hash'))) {
            abort(403, 'Invalid signature');
        }

        if (!empty($application->consumer_id)) {
            $consumer = Consumer::find($application->consumer_id);
            if ($consumer) {
                Auth::guard('consumers')->login($consumer);
            }
        }

        /** @var NordigenService $nordigen */
        $nordigen = app(NordigenService::class);

        $locale = $application->lang ?? config('app.country_code', 'lv');
        $redirect = url("/{$locale}/consumers/applications/{$application->token}");
        $redirect .= '?' . http_build_query([
            'id' => $application->id,
            'hash' => $application->signature,
        ]);
        $reference = 'app.' . sha1($application->id) . '.' . uniqid();

        $maxHistoricalDays = 180;
        if (in_array($institution_id, ['PAYSERA_EVIULT21XXX', 'BLUEORANGE_CBBRLV22'])) {
            $maxHistoricalDays = 90;
        }

        $ssn = null;
        if (in_array($institution_id, ['SWEDBANK_HABALV22', 'SEB_UNLALV2X', 'LUMINOR_RIKOLV2X'])) {
            $ssn = $application->personal_code;
        }

        try {
            if (app()->isLocal() || $institution_id === NordigenService::$SANDBOX_INSTITUTION_ID) {
                $session = $nordigen->getClient()->initSession(
                    institutionIdentifier: NordigenService::$SANDBOX_INSTITUTION_ID,
                    redirect: $redirect,
                    reference: $reference,
                    userLanguage: $application->lang ?? 'lv'
                );
            } else {
                $session = $nordigen->getClient()->initSession(
                    institutionIdentifier: $institution_id,
                    redirect: $redirect,
                    maxHistoricalDays: $maxHistoricalDays,
                    reference: $reference,
                    userLanguage: $application->lang ?? 'lv',
                    ssn: $ssn
                );
            }
        } catch (Throwable $e) {
            Log::error('[AppApplicationController]: GoCardless session init error', [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'application_id' => $application->id,
                'institution_id' => $institution_id,
            ]);

            return redirect()->to("/" . ($application->lang ?? 'lv') . "/consumers/applications/{$application->token}")
                ->with('error', __('Something went wrong, please try manual PDF upload.'));
        }

        $link = $session["link"] ?? "";
        $requisitionId = $session["requisition_id"] ?? "";

        if (empty($link)) {
            Log::error('[AppApplicationController]: GoCardless returned empty link', [
                'application_id' => $application->id,
            ]);

            return redirect()->to("/" . ($application->lang ?? 'lv') . "/consumers/applications/{$application->token}")
                ->with('error', __('Something went wrong, please try manual PDF upload.'));
        }

        $application->gocardless_sessions()->create([
            'link' => $link,
            'requisition_id' => $requisitionId,
            'agreement_id' => $session['agreement_id'] ?? null,
            'reference' => $reference,
        ]);

        return redirect($link);
    }

    public function appInitReview(Request $request, Application $application): JsonResponse
    {
        $consumer = $request->user();

        if ($application->consumer_id !== $consumer->id) {
            return $this->errorResponse(['application_not_found'], 404);
        }

        $application = $this->application_service->updateCompletionPercentage($application, 80);
        $application->will_send_docs_via_email = 1;
        $application->save();

        ApplicationService::syncExportSummaryWithData($application, $this->data_service);

        $statusData = (new ApiApplicationController())->getApplicationStatusData($application);

        return new JsonResponse([
            'data' => [
                'application' => (new ApplicationJsonObjectModel($application))->additional([
                    'status' => $statusData['status'],
                    'offers' => $statusData['offers'] ?? [],
                ])->toArray($request),
            ]
        ]);
    }

    public function appOpenLink(Request $request, Application $application, string $provider): JsonResponse
    {
        $consumer = $request->user();

        if ($application->consumer_id !== $consumer->id) {
            abort(403);
        }

        $lender = Lender::where(function ($q) use ($provider) {
            $q->where('slug', $provider)
                ->orWhere('abbreviation', $provider);
        })->first();

        if (empty($lender->id) || empty($lender->slug)) {
            abort(404);
        }

        $source = 'app';

        $hoursSinceApplicationUpdated = $application->updated_at->diffInHours(now());
        $provider = $lender->slug;

        $application = ApplicationService::syncExportSummaryWithData($application, $this->data_service);

        if ($application->exports_summary &&
            isset($application->exports_summary[$provider]) &&
            $lender->isRejected($application->exports_summary[$provider])
        ) {
            return response()->json(['error' => 'offer_rejected'], 410);
        }

        if ($lender->offer_expiration && $lender->offer_expiration - $hoursSinceApplicationUpdated < 0) {
            return response()->json(['error' => 'offer_expired'], 410);
        }

        $page = str_replace("www.", "", $request->getHost());
        $app_id = $application->id;

        try {
            $url = config('services.goodaff.api_data_url') . $page . '/' . $app_id;
            $response = Http::connectTimeout(3)
                ->timeout(30)
                ->withHeaders([
                    'Accept'  => 'application/json',
                    'Api-Key' => config('services.goodaff.api_data_key'),
                ])->get($url);

            if ($response->notFound()) {
                abort(404);
            }
        } catch (Throwable $e) {
            abort(503);
        }

        $json = $response->json();
        $exports_summary = $json['data']['exports_summary'] ?? [];

        if (!array_key_exists($lender->slug, $exports_summary)) {
            abort(404);
        }

        $provider_details = $json['data'][$lender->abbreviation . '_details'];

        if (in_array($lender->slug, ['dalidali', 'finea', 'bondora', 'minibank', 'esto', 'nordcredit', 'lateko'])) {
            $api_application_id = $json['data']['id'];
            $is_offer_accepted = collect($json['data']['exports_' . $lender->slug])
                ->where('action', 'acceptOffer')
                ->where('decision', 'Approved')
                ->count() > 0;

            if (!$is_offer_accepted) {
                Log::info('[Offers-api]: Accepting offer', [$application->id, $lender->slug]);
                try {
                    $acceptance_response = Http::connectTimeout(3)
                        ->timeout(30)
                        ->withHeaders([
                            'Accept'  => 'application/json',
                            'Api-Key' => config('services.goodaff.api_data_key'),
                        ])->post('https://www.api.data.goodday.group/api/v1/broker/export-application/' . $lender->slug . '/accept-offer', [
                            'application_id' => $api_application_id,
                        ]);
                } catch (Exception $e) {
                    Log::error('[Offers-api]: Error accepting offer', [$lender->slug, ' Application ID: ' . $app_id . ' ', $e->getMessage()]);
                }
            }
        }

        if ($provider_details['link'] == 'pending_acceptance' && $lender->slug == 'lateko') {
            if (isset($acceptance_response) && $acceptance_response->successful()) {
                $provider_details['link'] = $acceptance_response->json('redirect_url') ?? '';
            }
        }

        if (!empty($provider_details['link'])) {
            ProviderLinkVisit::create([
                'lender_id'      => $lender->id,
                'application_id' => $application->id,
                'link'           => $provider_details['link'],
                'source'         => $source,
            ]);

            try {
                $response = Http::connectTimeout(3)
                    ->timeout(30)
                    ->withHeaders([
                        'Accept'  => 'application/json',
                        'Api-Key' => config('services.goodaff.api_data_key'),
                    ])->post(config('services.goodaff.api_data_url') . $page . '/' . $app_id, [
                        'remote_id' => $application->id,
                        'page'      => $page,
                        'provider'  => $provider,
                        'source'    => $source,
                    ]);

                if ($response->status() !== 200) {
                    Log::error('[Offers-api]: Failed to save provider link in history', [$response]);
                }
            } catch (Exception $e) {
                Log::error('[Offers-api]: Failed to save provider link in history: ' . $e->getMessage());
            }

            return response()->json(['data' => ['url' => $provider_details['link']]]);
        }

        return response()->json(['error' => 'no_link'], 404);
    }

         /**
     * Unified response for Consumer+ConsumerDevice to be used in all endpoints
     */
    protected function userResponse(Consumer $consumer): JsonResponse
    {
        return new JsonResponse([
            'data' => [
                'user' => (new UserJsonObjectModel($consumer))
                    ->additional(['show_fcm_token' => $this->shouldShowFcmToken()])
                    ->toArray(request()),
            ]
        ]);
    }

     /**
     * Unified response for Consumer+ConsumerDevice to be used in all endpoints
     */
    protected function userRegisteredResponse(Consumer $consumer): JsonResponse
    {
        return new JsonResponse([
            'data' => [
                'tokens' => $this->generateTokens($consumer),
                'user' => (new UserJsonObjectModel($consumer))
                    ->additional(['show_fcm_token' => $this->shouldShowFcmToken()])
                    ->toArray(request()),
            ]
        ]);
    }

    private function shouldShowFcmToken(): bool
    {
        if (!config('offers-api.uses_goodwall')) {
            return false;
        }

        $goodwall = App::make(GoodwallService::class);
        return $goodwall->isEnabled() && $goodwall->isIpWhitelisted(request()->ip());
    }

    private function generateTokens($consumer): array
    {
        return [
            'access_token' => $consumer->createToken('access_token', ['general-access'], now()->addHours(3))->plainTextToken,
            'refresh_token' => $consumer->createToken('refresh_token', ['refresh-token'], now()->addMonths(6))->plainTextToken,
        ];
    }

    protected function errorResponse(?array $errors_bag = [], int $status = 422): JsonResponse
    {
        $errors = [];
        foreach ($errors_bag as $error_key) {
            $errors[] = ['error_key' => $error_key];
        }

        return new JsonResponse(['errors' => $errors], $status);
    }
}
