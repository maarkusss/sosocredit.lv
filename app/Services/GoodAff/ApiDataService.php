<?php

namespace App\Services\GoodAff;

use App\Models\Application;
use App\Models\ApplicationGocardlessSession;
use App\Models\Consumer;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Throwable;

class ApiDataService
{
    private array $user_agent_data;
    private string $page_name;

    public function __construct()
    {
        $this->api_data_url = config('services.goodaff.api_data_url');
        $this->api_data_key = config('services.goodaff.api_data_key');
        $this->headers = [
            'Api-Key' => $this->api_data_key,
            'Accept' => 'application/json',
        ];
        $this->user_agent_data = get_consumer_user_agent_data();
        $this->page_name = strtolower(Setting::retrieve('name'));
    }

    /**
     * Post a consumer to api.data.goodday.group/v1/broker/applications/post endpoint.
     *
     * @param Consumer $consumer
     * @return int
     */
    public function postConsumer(Consumer $consumer): int
    {
        $endpoint = $this->api_data_url . 'post';

        $data = [
            'country' => config('services.goodaff.country'),
            'name' => $consumer->first_name,
            'surname' => $consumer->last_name,
            'fathers_name' => $consumer->fathers_name,
            'birthday' => $consumer->birth,
            'personal_code' => $consumer->personal_code,
            'email' => $consumer->email,
            'phone' => phone_extension() . $consumer->phone,
            'page' => Request::getHost(),
            'ip' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'hoi_token' => $consumer->hoi_token,
            'fingerprint' => $consumer->fingerprint,
            'lng' => app()->getLocale() !== 'en' ? app()->getLocale() : null,
            'device' => $this->user_agent_data['device'],
            'os' => $this->user_agent_data['os'],
            'browser' => $this->user_agent_data['browser'],
            'confirmed' => $consumer->confirmed_at ? 1 : null,
        ];

        try {
            $response = Http::connectTimeout(3)
                ->withHeaders($this->headers)
                ->post($endpoint, $data);
            return $response->status();
        } catch (Throwable $e) {
            Log::error('ApiData postConsumer error', [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'consumer_id' => $consumer->id ?? -1,
            ]);
            return 404;
        }
    }

    /**
     * Post an application to api.data.goodday.group/v1/broker/applications/create endpoint.
     *
     * @param Application $application
     * @return int
     */
    public function postApplication(Application $application): int
    {
        if (empty($application->phone)) {
            return 0;
        }

        $endpoint = $this->api_data_url . 'post';
        $gc_req_id = '';

        try {
            if ($application->phone && $application->completion_percentage == 70) {
                try {
                    $gc_req_id = $this->getGcRequisitionFromApiData($application->phone);
                } catch (Throwable $e) {
                    Log::error('[GoodAffDataService::postApplication]: GC from API failed', [$e->getMessage()]);
                }
            }

            $gc_session = $application->gocardless_sessions()->whereNotNull('completed_at')->first();
            if (!empty($gc_session->id)) {
                /** @var ApplicationGocardlessSession $gc_session */
                $gc_req_id = $gc_session->requisition_id;
            }

            $page = Request::getHost();

            $form_params = [
                'country' => config('services.goodaff.country'),
                'page' => $page,
                'webmaster_token' => $application->webmaster_token,
                'remote_id' => $application->id,
                'fingerprint' => $application->fingerprint,
                'lng' => $application->lang,
                'email' => $application->email,
                'phone' => phone_extension() . $application->phone,
                'amount' => $application->amount,
                'monthly_debit' => $application->monthly_debit, // added 05.2025
                'amount_currency' => currency(),
                'term_months' => (int) $application->term_months,
                'loan_type' => $application->loantype,
                'company_name' => $application->company_name,
                'company_reg_nr' => $application->company_reg_nr,
                'company_contact_person' => $application->company_contact_person,
                'cadastral_number' => $application->cadastral_number,
                'car_number' => $application->car_number,
                'has_real_property' => $application->has_real_property, // added 01.2026.
                'has_car' => $application->has_car, // added 07.2026.
                'first_name' => $application->first_name,
                'last_name' => $application->last_name,
                'personal_code' => $application->personal_code,
                'is_political_person' => $application->is_political_person,
                'coupon_code' => $application->coupon_code,
                'ip' => $application->ip,
                'user_agent' => $application->user_agent,
                'utm_source' => $application->utm_source,
                'utm_campaign' => $application->utm_campaign,
                'utm_content' => $application->utm_content,
                'gclid' => $application->gclid,
                'goodaff_click_id' => $application->goodaff_click_id,
                'completion_percentage' => $application->completion_percentage,
                'pdf_link' => !empty($application->pdf) ? route('api.applications.pdf-link', [
                    $application->token,
                    $application->pdf_hash,
                ]) : null,
                'gc_req_id' => $gc_req_id ?: null,
                'employment_status' => $application->employment_status,
                'marital_status' => $application->marital_status,
                'education' => $application->education, // added 05.2025
                'address_street' => $application->address_street,
                'address_city' => $application->address_city,
                'address_zipcode' => $application->address_zipcode,
                'disbursement_iban' => $application->disbursement_iban,
                'salary' => $application->salary,
                'age' => $application->age,
                'credit_history' => $application->credit_history,
                'dependants' => $application->dependants ?? 0,
                'automated' => $application->automated,
                'hoi_token' => !empty($application->consumer_id) && !empty($application->consumer) ? $application->consumer->hoi_token : null,
            ];

            if ( ! empty($application->exports_summary) && $application->completion_percentage == 70)
            {
                $summary = $this->getExportsSummary($application);
                if (empty($summary)) {
                    $form_params['exports_summary'] = $application->exports_summary;
                }
            }

            $response = Http::connectTimeout(3)
                ->withHeaders($this->headers)
                ->post($endpoint, $form_params);

            Application::query()
                ->where('id', $application->id)
                ->update([
                    'last_exported_at' => now(),
                ]);

            return $response->status();
        } catch (Throwable $e) {
            Log::error('ApiDataService postApplication error', [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'application_id' => $application->id ?? -1,
            ]);
            return 404;
        }
    }
    public function getGcRequisitionFromApiData(string $phone): string
    {
        $endpoint = $this->api_data_url . 'requisitions-check/371' . $phone;

        $response = $this->get($endpoint);
        $data = $response->json();

        if ($response->successful() && $data['success'] === true && !empty($data['data'])) {
            return $data['data'];
        }

        return '';
    }
    public function getExportsSummary($application): array
    {
        $url = $this->api_data_url . $this->page_name . '/' . $application->id;
        try {
            $response = $this->get($url);

            if ($response->notFound()) {
                Log::error('Response not found from api.data');
                abort(app()->isProduction() ? 400 : 404);
            }
        } catch (Throwable $e) {
            Log::error('ApiData req Failure: ' . $e);
            abort(503);
        }

        $array = $response->json();

        return $array['data']['exports_summary'] ?? [];
    }

    protected function get(string $url)
    {
        return Http::connectTimeout(3)
            ->timeout(30)
            ->withHeaders([
                'Accept' => 'application/json',
                'Api-Key' => $this->api_data_key,
            ])->get($url);
    }
}
