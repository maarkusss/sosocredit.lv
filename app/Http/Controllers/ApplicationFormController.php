<?php

namespace App\Http\Controllers;

use App\Events\ApplicationPhoneVerified;
use App\Events\ApplicationRequiresSmsVerificationCode;
use App\Events\ConsumerRequiresEmailVerificationLink;
use App\Events\ConsumerRequiresSmsVerificationCode;
use App\Events\ConsumerSignup;
use App\Http\Requests\ApplicantForm\ApplicantFormCreateRequest;
use App\Http\Requests\ApplicantForm\ApplicantFormUpdateExtraDetailsRequest;
use App\Http\Requests\ApplicantForm\ApplicantFormUpdateRequest;
use App\Models\Application;
use App\Models\ApplicationGocardlessSession;
use App\Models\Consumer;
use App\Models\Consumer\ConsumerConsent;
use App\Models\Consumer\ConsumerSMSVerificationCode;
use App\Models\Section;
use App\Services\ApplicationService;
use App\Services\NordigenService;
use App\Services\GoodAff\GoodAffDataService;
use App\Traits\WorksWithHeaderMenu;
use App\Traits\WorksWithMeta;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Throwable;

/**
 * Controller for Blade Application Form
 *
 * Based on APPLICATION idea (i.e. one applicant can have multiple applications)
 *
 * @version 0.3
 */
class ApplicationFormController extends Controller
{
    use WorksWithHeaderMenu;
    use WorksWithMeta;

    public function __construct(
        protected ApplicationService $application_service,
        protected NordigenService    $nordigen,
        protected GoodAffDataService $dataservice
    )
    {
        override_locale_if_provided();
    }

    /**
     * Render page that shows corresponding step of an Applicant form
     *
     * @param Application $application
     * @param Request $request
     * @param Section|null $section
     * @return Renderable|RedirectResponse
     */
    public function show(Application $application, Request $request, ?Section $section): Renderable|RedirectResponse
    {
        $parsed = parse_url(request()->getRequestUri());
        $parsed_path = $parsed['path'] ?? '';
        $exploded = explode('/', $parsed_path);
        $last_part = end($exploded);
        $last_part = str_replace('step', '', $last_part);
        $is_business = !empty($section->id) && $section->parent?->common_name === 'kredits-uznemumam';

        $step = 0;
        if (is_numeric($last_part) && $last_part <= Application::MAX_STEPS) {
            $step = (int)$last_part;
        }

        if ($step === 0 || ($step === 1 && empty($application->id))) {
            return redirect($this->getStepUrl($application, 0));
        }

        if ($step === 1 && Auth::guard('consumers')->id()) {
            $application = $this->application_service->isVerifiedConsumer($application, Auth::guard('consumers')->user());
            $application = $this->application_service->update(application: $application, request: $request);

            if ($application->phone_verified_at) {
                $this->application_service->storeInSession($application, $request);
                $this->application_service->updateCompletionPercentage($application, 40);
                return redirect($this->getStepUrl($application, 3));
            }
        }

        if (($step === 1 || $step === 2) && $application->phone_verified_at) {
            return redirect($this->getStepUrl($application, 3));
        }

        if ($step === 3 && empty($application->phone_verified_at)) {
            return redirect($this->getStepUrl($application, 1));
        }

        if ($step === 3 && $application->completion_percentage >= 80) {
            return redirect($this->getStepUrl($application, 4));
        }

        // because of sporadic rare cases of apps >60% with consumer_id == 0
        if ($step === 3 && empty($application->consumer_id)) {
            Log::error("Application with 0 consumer ID at Step3!: AppID: $application->id");
            return redirect($this->getStepUrl($application, 0));
        }

        if ($step === 3) {
            if (Auth::guard('consumers')->id()) {
                $application = $this->application_service->ensureUtmSourceGoodaff($application);

                $application = $this->application_service->autoPopulate(
                    $application,
                    Auth::guard('consumers')->id()
                );
            } else {
                return redirect($this->getStepUrl($application, 0));
            }
        }

        if ($step === 4) {
            if (!empty($is_business)) {
                $application->will_send_docs_via_email = 1;
                $application->completion_percentage = 80;
                $application->save();

                return redirect($this->getStepUrl($application, Application::MAX_STEPS));
            }

            if (empty($application->id)) {
                return redirect($this->getStepUrl($application, 0));
            }

            if (empty($application->phone)) {
                return redirect($this->getStepUrl($application, 1));
            }

            if (empty($application->first_name)) {
                return redirect($this->getStepUrl($application, 3));
            }

            // Check if there has been recent (last 24h) Gocardless session for this phone
            $sister_application_ids = Application::query()
                ->where('phone', $application->phone)
                ->where('id', '!=', $application->id)
                ->orderBy('id', 'DESC')
                ->limit(25)
                ->pluck('id')
                ->toArray();

            if (!empty($sister_application_ids)) {
                $last_completed_gc_session = ApplicationGocardlessSession::query()
                    ->whereIn('application_id', $sister_application_ids)
                    ->whereNotNull('completed_at')
                    ->orderBy('id', 'DESC')
                    ->first();

                if (!empty($last_completed_gc_session->id) && !empty($last_completed_gc_session->completed_at) && now()->diffInHours($last_completed_gc_session->completed_at) < 24) {
                    /** @var ApplicationGocardlessSession $last_completed_gc_session */
                    $new_completed_gc_session = $last_completed_gc_session->replicate();
                    $new_completed_gc_session->application_id = $application->id;
                    $new_completed_gc_session->created_at = now()->format('Y-m-d H:i:s');
                    $new_completed_gc_session->save();

                    $this->application_service->updateCompletionPercentage($application, 100);

                    return redirect($this->getStepUrl($application, Application::MAX_STEPS));
                }
            }

            try { // Check if there are no completed GoCardless against api.data
                $gc_req_id = $this->dataservice->getGcRequisitionFromApiData($application->phone);
            } catch (Exception $e) {
                Log::error('Failed to retrieve gc_req_id at ApplicationFormController: ', [$e->getMessage()]);
            }

            if (!empty($gc_req_id)) { // application is updated in GoodAffService->postApplication at 70% app
                $this->application_service->updateCompletionPercentage($application, 100);
                return redirect($this->getStepUrl($application, Application::MAX_STEPS));
            }
        }

        if ($step === Application::MAX_STEPS && empty($application->id)) {
            return redirect($this->getStepUrl($application, 0));
        }

        $go_back_link = match ($step) {
            1 => $this->getStepUrl($application, 0),
            2 => $this->getStepUrl($application, 1),
            // skip verification step entirely if logged in
            3 => $application->phone_verified_at ? $this->getStepUrl($application, 0) : $this->getStepUrl($application, 2),
            4 => $this->getStepUrl($application, 3),
            default => '',
        };

        if ($application->completion_percentage >= 80) {
            $go_back_link = $this->getStepUrl($application, Application::MAX_STEPS);
        }

        if ($step === Application::MAX_STEPS) {
            // Process Thankyou step
            $application = $this->application_service->getFromSession();

            // Process GoCardless error response
            if ($request->error || $request->details) {
                $error_message = __('Something went wrong, please try manual PDF upload.');
                if ($request->error === 'UserCancelledSession') {
                    $error_message = __('User cancelled request.') . ' ' . __('Please try manual PDF upload.');
                }

                Log::error('[ApplicationFormController]: GoCardless error or details', [
                    'error' => $request->error ?? null,
                    'details' => $request->details ?? null,
                    'application_id' => !empty($application->id) ? $application->id : 0,
                ]);

                if ($request->error === 'SSNVerificationFailed') {
                    $error_message = __('SSN Verificationn failed. Please check Your SSN!');
                    return redirect($this->getStepUrl($application, 3))
                        ->with('error', $error_message);
                }

                return redirect($this->getStepUrl($application, 4))
                    ->with('error', $error_message);
            }

            // Process GoCardless success response
            if ($request->id && $request->hash) {
                $application = $this->application_service->getById($request->id);

                if (!empty($application->id)) {
                    if (!hash_equals($application->signature, $request->hash)) {
                        abort(400);
                    }

                    if ($request->ref && empty($request->error)) {
                        $gocardless_session = ApplicationGocardlessSession::where('reference', $request->ref)->first();

                        if (!empty($gocardless_session->id)) {
                            if ($gocardless_session->isCompleted()) {
                                return redirect($this->getStepUrl($application, 3));
                            }

                            $gocardless_session->complete();
                            $this->application_service->updateCompletionPercentage($application, 100);

                            if (!empty($application->token)) {
                                $country = config('app.country_code', 'lv');
                                return redirect()->to(url("/deeplink/go-cardless-completed?country={$country}&application_token={$application->token}"));
                            }
                        }
                    }
                }
            }

            if ($is_business) {

                // TODO Validate business loantype applications

            } else {
                if (empty($application->phone)) {
                    return redirect($this->getStepUrl($application, 1));
                }

                if (empty($application->first_name)) {
                    return redirect($this->getStepUrl($application, 3));
                }

                if (empty($application->has_bank_attachment) && empty($application->will_send_docs_via_email) && $application->completion_percentage < 100) {
                    return redirect($this->getStepUrl($application, Application::MAX_STEPS - 1));
                }
            }

            $this->application_service->deleteFromSession(request());
        }

        return view('sections.' . Section::TEMPLATE_APPLICANT_FORM_STEP)->with([
            'application' => $application,
            'applicant' => null,
            'applicant_form_types' => Application::getApplicationTypes(app()->getLocale(), true),
            'country' => app()->getLocale(),
            'employment_statuses' => Application::getEmploymentStatuses(),
            'education_levels' => Application::getEducationLevels(),
            'go_back_link' => $go_back_link,
            'header_menu' => $this->getHeaderMenu(),
            'hide_footer' => false,
            'is_applicant_page' => true,
            'marital_statuses' => Application::getMaritalStatuses(),
            'meta' => $this->metaFromSection($section),
            'redirect_url' => $this->getStepUrl($application, 0),
            'section' => $section,
            'step' => $step,
            'show_broker_phone_in_header' => true,
        ]);
    }

    /**
     * (step0) Create Application basing on web form request
     *
     * @param ApplicantFormCreateRequest $request
     * @return RedirectResponse
     */
    public function store(ApplicantFormCreateRequest $request): RedirectResponse
    {

        if (Auth::guard('consumers')->id()) {
            $consumer = Consumer::find(Auth::guard('consumers')->id());
            if (ApplicationService::consumerOverApplicationLimit($consumer)) {
                return redirect()->back()
                    ->with('error', __('Consumer exceeds application limit'));
            }
        }

        $application = $this->application_service->getFromSession();

        if (!empty($application->id)) {
            // Session has a leftover 70% application that does not belong to unauthenticated
            if ( ! Auth::guard('consumers')->check()) {
                $this->application_service->deleteFromSession($request);
                $application = $this->application_service->create(request: $request);
                $this->application_service->updateCompletionPercentage($application, 20);
                $this->application_service->storeInSession($application, $request);
            } else { // normal flow
                $application = $this->application_service->update(application: $application, request: $request);
            }
        } else {
            $application = $this->application_service->create(request: $request);

            $this->application_service->updateCompletionPercentage($application, 20);

            $this->application_service->storeInSession($application, $request);
        }

        if (Auth::guard('consumers')->id()) // skip verification step if logged in (verified)
        {
            return redirect($this->getStepUrl($application, 3));
        }

        return redirect($this->getStepUrl($application, 1));
    }

    /**
     * (step0 or step1) Update existing Application form basing on session
     *
     * @param ApplicantFormUpdateRequest $request
     * @return RedirectResponse
     */
    public function submitStep1(ApplicantFormUpdateRequest $request): RedirectResponse
    {
        $application = $this->application_service->getFromSession();

        if (empty($application->id)) {
            abort(404);
        }

        if ( ! Auth::guard('consumers')->id() &&  ! empty($request->phone)) {
            $consumer  = Consumer::where('phone', $request->phone)->first();
            if ($consumer && ApplicationService::consumerOverApplicationLimit($consumer)) {
                return redirect()->back()
                    ->with('error', __('Consumer exceeds application limit'));
            }
        }

        $application = $this->application_service->update(application: $application, request: $request);

        $this->application_service->storeInSession($application, $request);
        $this->application_service->updateCompletionPercentage($application, 40);

        $consumer = $application->consumer;

        // Ja ievadīta parole un patērētājam tā vēl nav, saglabājam patērētāja profilā
        if (!empty($request->password) && $consumer) {
            $consumer->password = \Illuminate\Support\Facades\Hash::make($request->password);
            $consumer->save();
        }

        // Ģenerējam un nosūtām e-pasta verifikācijas kodu
        if (!empty($application->email)) {
            $emailCode = \App\Models\Consumer\ConsumerEmailVerificationCode::generateForEmail($application->email);
            try {
                \Illuminate\Support\Facades\Mail::to($application->email)->send(new \App\Mail\SendEmailVerificationCodeMail($emailCode->code));
            } catch (\Throwable $e) {
                Log::error('[ApplicationFormController] Failed to send email verification code: ' . $e->getMessage());
            }
        }

        if (!empty($application->phone) && empty($application->phone_verified_at)) {
            $should_verify_phone = true;

            if (Auth::guard('consumers')->id()) {
                // Consumer logged in - so has recently verified himself
                $application->phone_verified_at = Auth::guard('consumers')->user()?->updated_at;
                $application->save();
                $should_verify_phone = false;
            }

            $application->refresh(); //in case if consumer changed due to incorrect phone number
            $consumer = $application->consumer;

            if ( // prevent duplicate schedules
                $should_verify_phone === true &&
                !$this->application_service->isSpammer($application) &&
                !$this->application_service->requestedTooManyVerificationCodes($application) &&
                $consumer && !empty($consumer->phone)
            ) {
                $recentSms = ConsumerSMSVerificationCode::where('phone_number', $consumer->phone)
                    ->where('created_at', '>', now()->subSeconds(3))
                    ->exists();
                if (!$recentSms) {
                    event(new ConsumerRequiresSmsVerificationCode($consumer, resend: false));
                }
            }
        }

        if ($consumer && !empty($consumer->email) && empty($consumer->email_verified_at)) {
            event(new ConsumerRequiresEmailVerificationLink($consumer));
        }

        if ($consumer) {
            $consumer->subscribed_email = $request->has('subscribed_email') ? ($request->subscribed_email ? 1 : 0) : ($application->subscribed_email ?? 0);
            $consumer->subscribed_sms = $request->has('subscribed_sms') ? ($request->subscribed_sms ? 1 : 0) : ($application->subscribed_sms ?? 0);
            $consumer->subscribed = ($consumer->subscribed_email || $consumer->subscribed_sms || $request->subscribe) ? 1 : 0;
            $consumer->save();
        }

        if (!empty($application->phone_verified_at)) {
            event(new ApplicationPhoneVerified($application));

            return redirect($this->getStepUrl($application, 3));
        }

        return redirect($this->getStepUrl($application, 2));

    }

    /**
     * (step 2) Submit SMS verification code against application
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function submitStep2(Request $request): RedirectResponse
    {
        $application = $this->application_service->getFromSession();

        if (empty($application->id)) {
            abort(404);
        }

        if ($application->consumer) {
            $application->consumer->createConsent(
                (bool) $application->agree_to_terms,
                (bool) $application->subscribed,
                app()->getLocale(), // to take from what he saw
                $application->ip,
                $application->user_agent,
            );
        }

        if (!empty($application->email_verified_at) || !empty($application->phone_verified_at)) {
            return redirect($this->getStepUrl($application, 3));
        }

        $isEmailVerified = !empty($application->email) && \App\Models\Consumer\ConsumerEmailVerificationCode::verify($application->email, $request->verification_code);

        $isSmsVerified = !empty($application->phone) && ConsumerSMSVerificationCode::query()
            ->where('phone_number', $application->phone)
            ->where('code', $request->verification_code)
            ->where('expires_at', '>=', date('Y-m-d H:i:s'))
            ->exists();

        if (!$isEmailVerified && !$isSmsVerified) {
            return redirect()->back()
                ->with('error', __('Verification code invalid or expired!'));
        }

        // Pārliecināmies, ka pieteikumam ir piesaistīts patērētājs
        if (empty($application->consumer_id)) {
            $application->fillConsumerId();
            if (empty($application->consumer_id)) {
                $consumer = app(\App\Services\ConsumerService::class)->createFromApplication($application);
                $application->consumer_id = $consumer->id;
            }
        }

        if ($isEmailVerified) {
            $application->email_verified_at = now();
            if ($application->consumer) {
                $application->consumer->markEmailAsVerified();
            }
        }

        if ($isSmsVerified) {
            $application->phone_verified_at = now();
            if ($application->consumer) {
                $application->consumer->markPhoneAsVerified();
            }
        }

        // Ja verificēts e-pasts, atzīmējam arī kā phone_verified_at, lai turpinātu plūsmu
        if (empty($application->phone_verified_at)) {
            $application->phone_verified_at = now();
        }

        $application->save();
        $this->application_service->storeInSession($application, $request);

        event(new ApplicationPhoneVerified($application));

        $consumer = $application->consumer;
        if (!empty($consumer) && empty(Auth::guard('consumers')->id())) {
            Auth::guard('consumers')->login($consumer);
        }

        if (config('app.rc_enabled') && isset($consumer)) {
            event(new ConsumerSignup($consumer));
        }

        return redirect($this->getStepUrl($application, 3));
    }


    /**
     * (step 3) Update extra details in Application
     *
     * @param ApplicantFormUpdateExtraDetailsRequest $request
     * @return RedirectResponse
     */
    public function submitStep3(ApplicantFormUpdateExtraDetailsRequest $request): RedirectResponse
    {
        $is_business = $request->loan_type == 'kredits-uznemumam';
        $application = $this->application_service->getFromSession();

        if (empty($application->id)) {
            abort(404);
        }

        if ($request->has('cadastral_number')) {
            $cadastralNumber = preg_replace(
                '/\s+/u',
                '',
                (string) $request->input('cadastral_number')
            );

            $request->merge([
                'cadastral_number' => $cadastralNumber,
            ]);
        }

        if ($request->has('car_number')) {
            $carNumber = mb_strtoupper(
                preg_replace(
                    '/[^a-zA-Z0-9]/u',
                    '',
                    (string) $request->input('car_number')
                ),
                'UTF-8'
            );

            $request->merge([
                'car_number' => $carNumber,
            ]);
        }

        $application = $this->application_service->updateExtraDetails(application: $application, request: $request);

        if (!empty($application->first_name) && !preg_match('/^[A-ZĀČĒĢĪĶĻŅŠŪŽa-zāčēģīķļņšūž\s\-\']+$/u', $application->first_name)) {
            throw ValidationException::withMessages([
                'first_name' => __('Name must contain only latin letters'),
            ]);
        }

        if (!empty($application->last_name) && !preg_match('/^[A-ZĀČĒĢĪĶĻŅŠŪŽa-zāčēģīķļņšūž\s\-\']+$/u', $application->last_name)) {
            throw ValidationException::withMessages([
                'last_name' => __('Name must contain only latin letters'),
            ]);
        }

        if (!empty($application->personal_code) && Str::contains($application->personal_code, '-')) {
            $personal_code = Str::replace('_', '', $application->personal_code);
            $exploded = explode('-', $personal_code, 2);
            $first = $exploded[0];
            if (!empty($exploded[1])) {
                $second = $exploded[1];
            }

            if (!empty($first) && Str::length($first) !== 6) {
                throw ValidationException::withMessages([
                    'personal_code' => __('Please provide valid personal code') . ': ' . $application->personal_code,
                ]);
            }

            if (!empty($second) && Str::length($second) !== 5) {
                throw ValidationException::withMessages([
                    'personal_code' => __('Please provide valid personal code') . ': ' . $application->personal_code,
                ]);
            }

            if( ! ApplicationService::validateSSNChecksum($application->personal_code)) {
                throw ValidationException::withMessages([
                    'personal_code' => __('Please provide valid personal code') . ': ' . $application->personal_code,
                ]);
            }

            if(!$is_business){
                if ( ! ApplicationService::verifyAgeWithSsn($application)) {
                    throw ValidationException::withMessages([
                        'personal_code' => __('Please provide valid age') . ': ' . $application->age . ' (' . $application->personal_code . ')',
                    ]);
                }
            }
        }

        // fill exports_summary at step3 because in some countries we skip step4
        $application = ApplicationService::fillExportsSummaryWithActiveProviders($application);

        $this->application_service->updateCompletionPercentage($application, 70);

        if (!empty($application->phone_verified_at)) {
            event(new ApplicationPhoneVerified($application));
        }

        return redirect($this->getStepUrl($application, 4));
    }

    /**
     * (step 4) Process POST request - that will either contain PDF file attachment
     * that has to be attached to Application, or an "institution_id" param
     * that will be an input param for GoCardless integration to auto retrieve
     * bank details from GoCardless
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function submitStep4(Request $request): RedirectResponse
    {
        $application = $this->application_service->getFromSession();

        if (!$request->hasFile('pdf') && empty($request->institution_id) && empty($request->will_send_docs_via_email)) {
            return redirect()->back()
                ->with('error', __('Please upload bank attachment PDF.'));
        }

        if (empty($application->id)) {
            abort(404);
        }

        if (!empty($request->institution_id)) {
            return $this->submitRequestToGocardless($request, $application);
        }

        if ($request->hasFile('pdf')) {
            $attachment = $request->file('pdf');
            $application = $this->application_service->attachPdf(application: $application, attachment: $attachment);
            $application = $this->application_service->updateCompletionPercentage($application, 100);
        } elseif ($request->will_send_docs_via_email) {
            if ( ! Auth::guard('consumers')->check()) {
                session(
                    [
                        'url.intended' => route('applications.continue', $application->token),
                        'will_send_docs_via_email' => true,
                    ]);
                return redirect()->route('consumers.login');
            }

            if ($application->consumer_id !== Auth::guard('consumers')->id()) {
                return redirect()->route('consumers.dashboard');
            }

            $application = $this->application_service->updateCompletionPercentage($application, 80);
            $application->will_send_docs_via_email = 1;
            $application->save();
        }

        if ($application->has_bank_attachment || $application->will_send_docs_via_email || $application->completion_percentage > 99) {
            $this->application_service->deleteFromSession($request);

            return redirect($this->getStepUrl($application, Application::MAX_STEPS));
        }

        return redirect($this->getStepUrl($application, 4));
    }

    /**
     * Generate URL of a specific step of an Application Form
     *
     * @param Application $application
     * @param int $force_step
     * @param bool $full_url
     * @return string
     */
    public function getStepUrl(Application $application, int $force_step, bool $full_url = false): string
    {
        if ($force_step > Application::MAX_STEPS) {
            throw new InvalidArgumentException('We do not have this step');
        }

        if ($force_step === Application::MAX_STEPS) {
            if($application->token){
                return route('consumers.applications.show', $application->token);
            }
        }

        if ($force_step === 0) {
            $section = Section::active();

            if (!empty($application->section_id)) {
                $section = $section->where('id', $application->section_id);
            }

            $section = $section->where('lang', app()->getLocale())
                ->where('template', Section::TEMPLATE_APPLICANT_FORM_LANDING_PAGE)
                ->first();
        } else {

            $section = Section::active();
            if (!empty($application->section_id)) {
                $section = $section->where('parent_section_id', $application->section_id);
            } else {
                $section = $section->where('lang', app()->getLocale());
            }

            $section = $section->where('template', Section::TEMPLATE_APPLICANT_FORM_STEP)
                ->where('route_name', 'like', 'step' . $force_step)
                ->first();
        }

        if (!empty($section->id)) {
            /** @var Section $section */
            $url = '/' . app()->getLocale();

            if (!empty($section->parent_section_id)) {
                $url .= '/' . $section->parent->route_name;
            }

            $url .= '/' . $section->route_name;

            if ($full_url) {
                $url = config('app.url') . $url;
            }

            return $url;
        }

        Log::info('ApplicantFormController->getStepUrl Failed to find section ID');
        return '';
    }


    /**
     * (step4) When GoCardless integration is active and applicant used it - submit data to GoCardless
     *
     * @param Request $request
     * @param Application $application
     * @return RedirectResponse
     */
    private function submitRequestToGocardless(Request $request, Application $application): RedirectResponse
    {
        $institutionIdentifier = $request->institution_id;
        $redirect = $this->getStepUrl($application, 5, true);
        $redirect .= '?' . http_build_query([
                'id' => $application->id,
                'hash' => $application->signature,
            ]);
        $reference = 'app' . '.' . sha1($application->id) . '.' . uniqid();

        $maxHistoricalDays = 180;
        // These intstitutions only support up to 90 days xtr hist
        if (in_array($institutionIdentifier, ['PAYSERA_EVIULT21XXX', 'BLUEORANGE_CBBRLV22'])) {
            $maxHistoricalDays = 90;
        }

        // These institutions support SSN verification
        $ssn = null;
        if (in_array($institutionIdentifier, ['SWEDBANK_HABALV22', 'SEB_UNLALV2X', 'LUMINOR_RIKOLV2X'])) {
            $ssn = $application->personal_code;
        }

        try {
            if (app()->isLocal() || $institutionIdentifier === NordigenService::$SANDBOX_INSTITUTION_ID) {
                $session = $this->nordigen->getClient()->initSession(
                    institutionIdentifier: NordigenService::$SANDBOX_INSTITUTION_ID,
                    redirect: $redirect,
                    reference: $reference,
                    userLanguage: $application->lang
                );
            } else {
                $session = $this->nordigen->getClient()->initSession(
                    institutionIdentifier: $institutionIdentifier,
                    redirect: $redirect,
                    maxHistoricalDays: $maxHistoricalDays,
                    reference: $reference,
                    userLanguage: $application->lang,
                    ssn: $ssn
                );
            }
        } catch (Throwable $e) {
            Log::error('[ApplicationFormController]: GoCardless session init error', [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'application_id' => !empty($application->id) ? $application->id : 0,
            ]);

            return redirect()->back()
                ->with('error', __('Something went wrong, please try manual PDF upload.'));
        }

        $link = $session["link"] ?? "";
        $requisitionId = $session["requisition_id"] ?? "";

        if (empty($link)) {
            Log::error('[ApplicationFormController]: GoCardless returned empty link', [
                'application_id' => !empty($application->id) ? $application->id : 0,
            ]);

            return redirect()->back()
                ->with('error', __('Something went wrong, please try manual PDF upload.'));
        }

        $application->gocardless_sessions()->create([
            'link' => $link,
            'requisition_id' => $requisitionId,
            'agreement_id' => $session['agreement_id'] ?? null,
            'reference' => $reference,
        ]);

        // $this->application_service->updateCompletionPercentage($application, 80);

        return redirect($link);
    }

    /**
     * Handle "Continue" button click from application status page
     *
     * @param string|null $token
     * @return RedirectResponse
     */
    public function continue(string $token = null): RedirectResponse
    {
        $application = Application::checkToken($token)
            ->latest('id')
            ->first();

        $consumer = Auth::guard('consumers')->user();

        if (!$application) {
            return redirect()->route('consumers.dashboard');
        }

        if ($application->consumer_id != $consumer->id) {
            return redirect()->route('consumers.dashboard');
        }

        $checked_will_send_docs_via_email = session()->pull('will_send_docs_via_email');
        if (Auth::guard('consumers')->check() &&
            $checked_will_send_docs_via_email &&
            $application->completion_percentage == 70)
        {
            $application = $this->application_service->updateCompletionPercentage($application, 80);
            $application->will_send_docs_via_email = 1;
            $application->save();
        }

        // Check consumer always, except when 70% - allow 70% as public route for GC process
        if ($application->completion_percentage !== 70) {
            if ( ! Auth::guard('consumers')->check()) {
                // Set url.intended to redirect back here after user will be logged in
                session(['url.intended' => route('applications.continue', $token)]);

                return redirect()->route('consumers.login');
            }

            if ($application->consumer_id !== Auth::guard('consumers')->id()) {
                return redirect()->route('consumers.dashboard');
            }
        }

        $application = ApplicationService::syncExportSummaryWithData($application, $this->dataservice);

        if ($application->webmaster_token) { // webmaster customers just land into our landing
            $application->phone_verified_at = now(); // he logged in with SMS
            $application->save();
        }

        $this->application_service->storeInSession($application, request());

        return $this->redirectToAppropriateStep($application);
    }

    /**
     * Redirect client to appropriate step based on completion percentage
     *
     * @param Application $application
     * @return RedirectResponse
     */
    public function redirectToAppropriateStep(Application $application): RedirectResponse
    {
        // these applications are already exported
        if ($application->completion_percentage >= 70 &&
            ! empty(array_filter($application->exports_summary, fn($v) =>
                ! empty($v) && stripos($v, 'Pending') === false
            ))
        ) {
            $application = $this->application_service->updateCompletionPercentage($application, 80);
            $application->will_send_docs_via_email = 1;
            $application->save();
            return redirect($this->getStepUrl($application, Application::MAX_STEPS));
        }

        if ($application->completion_percentage < 40) {
            return redirect($this->getStepUrl($application, 2));
        }

        if ($application->completion_percentage < 70) {
            return redirect($this->getStepUrl($application, 3));
        }

        if ($application->completion_percentage < 80) {
            return redirect($this->getStepUrl($application, 4));
        }

        return redirect(route('consumers.dashboard'));
    }

    public function updateType(Request $request)
    {
        $application = $this->application_service->getFromSession();

        if (empty($application->id)) {
            abort(404);
        }

        $update = [];
        if ($request->has('amount') && $request->amount) {
            $update['amount'] = $request->amount;
        }

        if ($request->has('term_months') && $request->term_months) {
            $update['term_months'] = $request->term_months;
        }

        if ($request->has('loantype') && $request->loantype) {
            $section = Section::findOrFail($request->loantype);

            $update['loantype'] = $section->common_name;
            $update['section_id'] = $section->id;
        }

        $application->update($update);

        return redirect()->to(app()->getLocale() . '/' . $section->route_name . '/step' . $request->step)->withInput();
    }

    public function returnToSubmitGoCardless(string $token): RedirectResponse
    {
        $application = Application::where('token', $token)->firstOrFail();

        if (empty($application->id)) {
            abort(404);
        }

        $this->application_service->storeInSession($application, request());

        return redirect($this->getStepUrl($application, 4));
    }
}
