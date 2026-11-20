<?php

namespace App\Http\Controllers\Consumers;

use App\Http\Controllers\ApplicationFormController;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationGocardlessSession;
use App\Models\Lender;
use App\Models\ProviderLinkVisit;
use App\Models\Section;
use App\Services\ApplicationService;
use App\Services\ConsumerService;
use App\Services\GoodAff\GoodAffDataService;
use App\Services\LenderConfigService;
use App\Traits\WorksWithMeta;
use App\ValueObjects\LenderConfig;
use Exception;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request as HttpRequest;
use Throwable;

class ConsumerDashboardController extends Controller
{
    use WorksWithMeta;

    public function __construct(
        protected ConsumerService $consumer_service,
        protected ApplicationService $application_service,
        protected GoodAffDataService $data_service,
    ){}

    public function dashboard()
    {
        $consumer = Auth::guard('consumers')->user();

        $homepage = Section::active()
            ->where('lang', app()->getLocale())
            ->where('main_page', 1)
            ->firstOrFail();

        $applications = $consumer->applications()
            ->with(['section'])
            ->where('created_at', '>=', now()->subDays(3)->format('Y-m-d 00:00:00'))
            ->orderBy('id', 'DESC')
            ->limit(10)
            ->get();

        $applicant_form_types = Application::getApplicationTypes(app()->getLocale());
        $default_form_type = $applicant_form_types[0];

        return view('consumers.dashboard', [
            'amounts' => $homepage->getAmounts($this->application_service),
            'term_months' => $homepage->getTermMonths($this->application_service),
            'applications' => $applications,
            'consumer' => $consumer,
            'section' => $homepage,
            'meta' => $this->metaTitle(__('My applications')),
            'applicant_form_types' => Application::getApplicationTypes(app()->getLocale()),
            'default_form_type' => $default_form_type,
        ]);
    }

    public function updatePhone(HttpRequest $request)
    {
        $request->validate([
            'phone' => 'nullable|string|min:6|max:20',
            'subscribed_email' => 'nullable|in:0,1',
            'subscribed_sms' => 'nullable|in:0,1',
        ]);

        $consumer = Auth::guard('consumers')->user();

        if ($request->has('phone')) {
            $phone = trim((string) $request->phone);
            $phone = preg_replace('/[^0-9]/', '', $phone);
            if (str_starts_with($phone, '371') && strlen($phone) === 11) {
                $phone = substr($phone, 3);
            }
            $consumer->phone = $phone ?: null;

            // Ja lietotājs papildina tālruni, atjaunojam to arī pieteikumos, kur tas bija tukšs
            if (!empty($consumer->phone)) {
                $consumer->applications()->whereNull('phone')->orWhere('phone', '')->update([
                    'phone' => $consumer->phone,
                ]);
            }
        }

        $consumer->subscribed_email = $request->has('subscribed_email') && $request->subscribed_email ? 1 : 0;
        $consumer->subscribed_sms = $request->has('subscribed_sms') && $request->subscribed_sms ? 1 : 0;
        $consumer->subscribed = ($consumer->subscribed_email || $consumer->subscribed_sms) ? 1 : 0;
        $consumer->save();

        return redirect()->back()->with('success', __('Profila informācija veiksmīgi atjaunināta!'));
    }

    public function applicationShow(string $token)
    {
        $consumer = Auth::guard('consumers')->user();

        /** @var Application $application */
        $application = Application::with(['section'])
            ->where('token', $token)
            ->where('consumer_id', $consumer->id)
            ->first();

        if (empty($application->id)) {
            abort(404);
        }

        $application = ApplicationService::syncExportSummaryWithData($application, $this->data_service);

        // Process GoCardless error response
        if (!empty(request()->error) || !empty(request()->details)) {
            $error_message = __('Something went wrong, please try manual PDF upload.');
            if (!empty(request()->error) && request()->error === 'UserCancelledSession') {
                $error_message = __('User cancelled request.') . ' ' . __('Please try manual PDF upload.');
            }

            Log::error('[ApplicationFormController]: GoCardless error or details', [
                'error' => request()->error ?? null,
                'details' => request()->details ?? null,
                'application_id' => $application->id,
                'in_dashboard' => 1,
            ]);

            $application_form_controller = App::make(ApplicationFormController::class);

            return redirect($application_form_controller->getStepUrl($application, 4))
                ->with('error', $error_message);
        }

        // Process GoCardless success response
        if (!empty(request()->id) && !empty(request()->hash)) {
            if ($application->id != request()->id) {
                Log::error('[ApplicationFormController]: GoCardless complete url incorrect app', [
                    'id' => request()->id ?? null,
                    'hash' => request()->hash ?? null,
                    'application_id' => $application->id,
                ]);
            } elseif (!hash_equals($application->signature, request()->hash)) {
                Log::error('[ApplicationFormController]: GoCardless complete url wrong hash', [
                    'id' => request()->id ?? null,
                    'hash' => request()->hash ?? null,
                    'application_id' => $application->id,
                ]);
            } elseif (request()->ref && empty(request()->error)) {
                $gocardless_session = ApplicationGocardlessSession::where('reference', request()->ref)->first();

                if (!empty($gocardless_session->id)) {
                    if ($gocardless_session->isCompleted()) {
                        Log::warning('[ApplicationFormController]: GoCardless already completed', [
                            'id' => request()->id ?? null,
                            'hash' => request()->hash ?? null,
                            'application_id' => $application->id,
                        ]);
                    } else {
                        $gocardless_session->complete();
                        $this->application_service->updateCompletionPercentage($application, 100);

                        $this->application_service->deleteFromSession(request());
                    }

                    $country = config('app.country_code', 'lv');

                    return redirect()->to(url("/deeplink/go-cardless-completed?country={$country}&application_token={$application->token}"));
                }
            }

            $country = config('app.country_code', 'lv');

            return redirect()->to(url("/deeplink/go-cardless-completed?country={$country}&application_token={$application->token}"));
        }

        if ($application->completion_percentage >= 80) {
            $session_application = $this->application_service->getFromSession();

            if (!empty($session_application->id) && $session_application->id === $application->id) {
                $this->application_service->deleteFromSession(request());
            }
        }

        if (empty($application->complete_opened_once)) {
            Application::where('id', $application->id)->update(['complete_opened_once' => 1]);
        }

        return view('consumers.application', [
            'application' => $application,
            'applicant_form_types' => Application::getApplicationTypes(app()->getLocale()),
            'consumer' => $consumer,
            'meta' => $this->metaTitle(__('My application') . ' ' . $application->created_at->format('d.m.Y')),
        ]);
    }

    public function applicationOpenLink(HttpRequest $request, string $token, string $provider, string $source = 'web')
    {
        $consumer = Auth::guard('consumers')->user();

        $lender = Lender::where(function ($q) use ($provider) {
            $q->where('slug', $provider)
                ->orWhere('abbreviation', $provider);
        })->first();

        $application = Application::where('consumer_id', $consumer->id)
            ->checkToken($token)
            ->latest('id')
            ->first();

        if (empty($application->id) || empty($lender->id) || empty($lender->slug)) {
            abort(404);
        }

        if (!in_array($source, ['web', 'email', 'sms', 'cs', 'wa'])) {
            Log::warning('[ConsumerDashboardController]: Someone mingled with source?: ', [$source]);
            abort(404);
        }

        $hoursSinceApplicationUpdated = $application->updated_at->diffInHours(now());
        $sectionExpired = Section::where('common_name', 'offer-expired')
            ->where('lang', $application->lang)
            ->first();

        $provider = $lender->slug; // If abbreviation was passed in $provider, then need to rewrite it to slug value of lender

        $application = ApplicationService::syncExportSummaryWithData($application, $this->data_service);

        if ($application->exports_summary &&
            isset($application->exports_summary[$provider]) &&
            $lender->isRejected($application->exports_summary[$provider])
        ) {
            if ($sectionExpired) {
                return redirect()->to($sectionExpired->lang . '/' . $sectionExpired->route_name);
            }
            abort(404);
        }

        if ($lender->offer_expiration && $lender->offer_expiration - $hoursSinceApplicationUpdated < 0) {
            if ($sectionExpired) {
                return redirect()->to($sectionExpired->lang . '/' . $sectionExpired->route_name);
            }
            abort(404);
        }

        $page = Request::getHost();
        $page = str_replace("www.", "", $page);
        $app_id = $application->id;

        try {
            $url = config('services.goodaff.api_data_url') . $page. '/' . $app_id;
            $response = Http::connectTimeout(3)
                ->timeout(30)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Api-Key' => config('services.goodaff.api_data_key'),
                ])->get($url);

            if ($response->notFound()) {
                abort(app()->isProduction() ? 400 : 404);
            }
        } catch (HttpClientException $e) {
            abort(503);
        }

        $json = $response->json();

        $exports_summary = $json['data']['exports_summary'] ?? [];

        if ( ! array_key_exists($lender->slug, $exports_summary)) {
            abort(404);
        }

        $provider_details = $json['data'][$lender->abbreviation . '_details'];

        try { // This must be present if provider is in exports_summary
            $lenderConfig = new LenderConfig($json['data'][$lender->abbreviation . '_config']);
        } catch (Throwable $e) {
            Log::warning('[ConsumerDashboardController]: Lender configuration error: ' . $e->getMessage());
            abort(404);
        }

        if (LenderConfigService::requiresAdditionalFieldUpdate($lenderConfig, $json))
        { // check required extra details and if they are already present in application from api.data
            if ($request->method() == 'POST')
            { // comes from web with additional_fields must be updated
                try { // update application country data
                    $url = config('services.goodaff.api_data_url') . $json['data']['id'];
                    $response = Http::connectTimeout(3)
                        ->timeout(30)
                        ->withHeaders([
                            'Accept' => 'application/json',
                            'Api-Key' => config('services.goodaff.api_data_key'),
                        ])->put($url, $request);
                    if ($response->status() !== 200) {
                        Log::error('[ConsumerDashboardController]: Failed to update application country data',
                            [$response]
                        );
                    }
                } catch (Exception $e) {
                    Log::error('[ConsumerDashboardController]: Failed to update application country data: '
                        . $e->getMessage()
                    );
                }
            }

            if ($request->method() == 'GET')
            { // comes from Wa, SMS, e-mail, CS and must be recycled
                return view('includes.applicant-form.additional_fields_requested', [
                    'application' => $application,
                    'source' => $source,
                    'provider' => $lenderConfig,
                    'lender' => $lender,
                    'required_fields' => $lenderConfig->additional_required_fields,
                    'offer_details' => $provider_details
                ]);
            }
        }

        if (in_array($lender->slug, ['dalidali', 'finea', 'bondora', 'minibank', 'esto', 'nordcredit', 'lateko', 'holmbank'])) {
            $api_application_id = $json['data']['id'];
            $is_offer_accepted = collect($json['data']['exports_' . $lender->slug])
                    ->where('action', 'acceptOffer')
                    ->where('decision', 'Approved')
                    ->count() > 0;

            if (!$is_offer_accepted) {
                // Try accept offer
                Log::info('[ConsumerDashboardController]: Accepting offer', [$application->id, $lender->slug]);
                try {
                    $url = 'https://www.api.data.goodday.group/api/v1/broker/export-application/' . $lender->slug . '/accept-offer';
                    $acceptance_response = Http::connectTimeout(3)
                        ->timeout(30)
                        ->withHeaders([
                            'Accept' => 'application/json',
                            'Api-Key' => config('services.goodaff.api_data_key'),
                        ])->post($url, [
                            'application_id' => $api_application_id,
                        ]);
                } catch (Exception $e) {
                    Log::error('[ConsumerDashboardController]: Error accepting offer', [$lender->slug, ' Application ID: ' . $app_id . ' ', $e->getMessage()]);
                }
            }
        }

        if ($provider_details['link'] == 'pending_acceptance' && $lender->slug == 'lateko') {
            if (isset($acceptance_response) && $acceptance_response->successful()) {
                $provider_details['link'] = $acceptance_response->json('redirect_url') ?? '';
            }
        }

        if ($lender->slug == 'holmbank') {
            if (isset($acceptance_response) && $acceptance_response->successful()) {
                $providerLink = $acceptance_response->json('redirect_url') ?? '';
                $provider_details['link'] = ! empty($providerLink) ? $providerLink : $provider_details['link'];
            }
        }

        if (!empty($provider_details['link'])) {
            ProviderLinkVisit::create([
                'lender_id' => $lender->id,
                'application_id' => $application->id,
                'link' => $provider_details['link'],
                'source' => $source,
            ]);

            try { // register provider click
                $url = config('services.goodaff.api_data_url') . $page. '/' . $app_id;
                $response = Http::connectTimeout(3)
                    ->timeout(30)
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'Api-Key' => config('services.goodaff.api_data_key'),
                    ])->post($url, [
                        'remote_id' => $application->id,
                        'page' => $page,
                        'provider' => $provider,
                        'source' => $source,
                    ]);
                if ($response->status() !== 200) {
                    Log::error('[ApplicationFormController]: Failed to save provider link in history', [$response]);
                }
            } catch (Exception $e) {
                Log::error('[ConsumerDashboardController]: Failed to save provider link in history: ' . $e->getMessage());
            }

            return redirect($provider_details['link']);
        }

        return redirect(route('consumers.applications.show', $token));
    }
}
