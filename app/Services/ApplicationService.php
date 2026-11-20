<?php

namespace App\Services;

use App\Contracts\SendSmsInterface;
use App\Entity\UtmDataDTO;
use App\Events\ApplicationSubmitted;
use App\Http\Requests\ApplicantForm\ApplicantFormCreateRequest;
use App\Http\Requests\ApplicantForm\ApplicantFormUpdateRequest;
use App\Models\Application;
use App\Models\Consumer;
use App\Models\Consumer\ConsumerEmailVerificationRequest;
use App\Models\Consumer\ConsumerPhoneVerificationRequest;
use App\Models\Consumer\ConsumerSMSVerificationCode;
use App\Models\Lender;
use App\Models\Setting;
use App\Services\GoodAff\GoodAffDataService;
use Goodday\Common\Helpers\CommonHelpers;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ApplicationService
{
    public ?UtmDataDTO $utm_data;
    public const SESSION_KEY = 'application_id';

    public function __construct(protected ConsumerService $consumer_service, protected SendSmsInterface $sms_sender)
    {
        $this->utm_data = UtmDataDTO::fromRequest();
    }

    public function getFromSession(): ?Application
    {
        $application = null;

        if (request()->session()->get(static::SESSION_KEY)) {
            $application_id = (int) request()->session()->get(static::SESSION_KEY);
            $application = Application::find($application_id);
        }

        return $application;
    }

    public function getById($id): ?Application
    {
        return Application::find($id);
    }

    public function getByToken(string $token): ?Application
    {
        /** @var Application|null */

        return Application::query()->where('token', $token)->first();
    }

    public function getByFirstOfferHash(string $first_offer_hash): ?Application
    {
        /** @var Application|null */

        return Application::query()->where('first_offer_hash', $first_offer_hash)->first();
    }

    public function getBySecondOfferHash(string $second_offer_hash): ?Application
    {
        /** @var Application|null */

        return Application::query()->where('second_offer_hash', $second_offer_hash)->first();
    }

    public function create(ApplicantFormUpdateRequest|ApplicantFormCreateRequest $request): Application
    {
        $token = Str::random(55);
        $token .= Str::random(7);

        $personal_code = '';
        if ($request->personal_code !== '______-_____') {
            $personal_code = $request->personal_code;
        }

        $utm_source = $request->cookie('utm_source');
        $utm_campaign = $request->cookie('utm_campaign');
        $utm_medium = $request->cookie('utm_medium');
        $utm_content = $request->cookie('utm_content');
        $gclid = $request->cookie('gclid');
        $automated = 0;

        if ($utm_source === 'google') {
            $automated = 1;
        }

        $email = $request->email ?: null;
        $phone = $request->phone ?: null;

        if (empty($email) && empty($phone)) {
            $consumer = Auth::guard('consumers')->user();

            if (!empty($consumer->phone)) {
                $email = $consumer->email;
                $phone = $consumer->phone;
            }
        }

        $application = Application::create([
            'token' => $token,
            'fingerprint' => $request->cookie('fingerprint'),
            'email' => $email,
            'phone' => $phone,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'personal_code' => $personal_code,
            'lang' => app()->getLocale() ?? null,
            'amount' => $request->amount,
            'term_months' => $request->term_months ?? 0,
            'loantype' => formatLoanTypeName($request->loantype),
            'section_id' => $request->section_id ?: 0,
            'company_name' => $request->company_name ?: null,
            'company_reg_nr' => $request->company_reg_nr ?: null,
            'company_contact_person' => $request->company_contact_person ?: null,
            'cadastral_number' => $request->cadastral_number ?: null,
            'car_number' => $request->car_number ?: null,
            'subscribed' => $request->subscribed && $request->subscribed !== 'false' ? 1 : 0,
            'automated' => $automated,
            'agree_to_terms' => $request->agree_to_terms && $request->agree_to_terms !== 'false' ? 1 : 0,
            'ip' => CommonHelpers::getIp(),
            'user_agent' => Str::limit($request->userAgent(), 500, ''),
            'utm_source' => $utm_source,
            'utm_campaign' => $utm_campaign,
            'utm_medium' => $utm_medium,
            'utm_content' => $utm_content,
            'gclid' => $gclid,
        ]);

        /** @var Application $application */
        $application = $this->fillOfferHashes($application);

        event(new ApplicationSubmitted($application));

        return $application;
    }

    public function update(Application $application, ApplicantFormUpdateRequest|Request $request): Application
    {
        $update = [];
        $update['lang'] = app()->getLocale();
        $update['ip'] = $request->ip();
        $update['referrer'] = $request->header('referer');

        if (!empty($request->userAgent()) && Str::length($request->userAgent()) < 255) {
            $update['user_agent'] = $request->userAgent();
        }

        if (!empty($request->phone)) {
            $update['phone'] = $request->phone;
        }

        if (!empty($request->email)) {
            $update['email'] = $request->email;
        }

        if (!empty($request->cookie('fingerprint'))) {
            $update['fingerprint'] = $request->cookie('fingerprint');
        }

        if (!empty($request->first_name)) {
            $update['first_name'] = $request->first_name;
        }

        if (!empty($request->last_name)) {
            $update['last_name'] = $request->last_name;
        }

        if (!empty($request->personal_code) && $request->personal_code !== '______-_____') {
            $update['personal_code'] = $request->personal_code;
        }

        if ($request->has('amount')) {
            $update['amount'] = $request->amount;
        }

        if ($request->has('term_months') && $request->term_months > 0) {
            $update['term_months'] = $request->term_months;
        }

        if (!empty($request->loantype)) {
            $update['loantype'] = formatLoanTypeName($request->loantype);
        }

        if (!empty($request->section_id)) {
            $update['section_id'] = (int) $request->section_id;
        }

        if (!empty($request->company_name)) {
            $update['company_name'] = Str::limit($request->company_name, 100, '');
        }

        if (!empty($request->company_reg_nr)) {
            $update['company_reg_nr'] = Str::limit($request->company_reg_nr, 31, '');
        }

        if (!empty($request->company_contact_person)) {
            $update['company_contact_person'] = Str::limit($request->company_contact_person, 63, '');
        }

        if (!empty($request->cadastral_number)) {
            $update['cadastral_number'] = Str::limit($request->cadastral_number, 63, '');
        }

        if (!empty($request->car_number)) {
            $update['car_number'] = Str::limit($request->car_number, 63, '');
        }

        if ($request->has('subscribe')) {
            $update['subscribed'] = $request->subscribe && $request->subscribe !== 'false' ? 1 : 0;
        }

        if ($request->has('agree_to_terms')) {
            $update['agree_to_terms'] = $request->agree_to_terms && $request->agree_to_terms !== 'false' ? 1 : 0;
        }

        if ($request->has('is_political_person')) {
            $update['is_political_person'] = $request->is_political_person && $request->is_political_person !== 'false' ? 1 : 0;
        }

        if (!empty($request->coupon_code)) {
            $update['coupon_code'] = Str::limit($request->coupon_code, 63, '');
        }

        $application->update($update);

        $application = $this->fillOfferHashes($application);

        if ($application->completion_percentage == 20) { // he has verified, stay linked
            $this->linkWithConsumers($application);
        }

        return $application;
    }

    public function updateExtraDetails(Application $application, ApplicantFormUpdateRequest|Request $request): Application
    {
        $update = [];

        if (!empty($request->first_name)) {
            $application->first_name = preg_replace('/^[^\p{L}]+|[^\p{L}]+$/u', '', $request->first_name);
        }

        if (!empty($request->last_name)) {
            $application->last_name = preg_replace('/^[^\p{L}]+|[^\p{L}]+$/u', '', $request->last_name);
        }

        if (!empty($request->personal_code)) {
            $application->personal_code = $request->personal_code;
        }

        $application->save();

        if (!empty($request->company_name)) {
            $update['company_name'] = $request->company_name;
        }

        if (!empty($request->company_reg_nr)) {
            $update['company_reg_nr'] = $request->company_reg_nr;
        }

        if (!empty($request->company_contact_person)) {
            $update['company_contact_person'] = $request->company_contact_person;
        }

        if (!empty($request->cadastral_number)) {
            $update['cadastral_number'] = $request->cadastral_number;
        }

        if (!empty($request->car_number)) {
            $update['car_number'] = $request->car_number;
        }

        if (!empty($request->employment_status)) {
            $update['employment_status'] = $request->employment_status;
        }

        if (!empty($request->marital_status)) {
            $update['marital_status'] = $request->marital_status;
        }

        if (!empty($request->education)) {
            $update['education'] = $request->education;
        }

        if (!empty($request->address_street)) {
            $update['address_street'] = Str::limit($request->address_street, 63, '');
        }

        if (!empty($request->address_city)) {
            $update['address_city'] = Str::limit($request->address_city, 31, '');
        }

        if (!empty($request->address_zipcode)) {
            $update['address_zipcode'] = $request->address_zipcode;
        }

        if (!empty($request->disbursement_iban)) {
            $iban = strtoupper( // make sure all letters uppercase
                str_replace( // remove spaces if any
                    ' ',
                    '',
                    Str::limit($request->disbursement_iban, 60, '') // no moret hen 60 chars
                )
            );
            $update['disbursement_iban'] = $iban;
        }

        if (!empty($request->salary)) {
            $update['salary'] = $request->salary;
        }

        if ($request->has('monthly_debit')) { // this is properly, so it can be 0
            $update['monthly_debit'] = $request->monthly_debit;
        }

        if ($request->has('age')) {
            $update['age'] = $request->age;
        }

        if (!empty($request->credit_history)) {
            $update['credit_history'] = $request->credit_history;
        }

        if (!empty($request->dependants)) {
            $update['dependants'] = $request->dependants;
        }

        if ($request->has('is_political_person')) {
            $update['is_political_person'] = CommonHelpers::isFalsy($request->is_political_person) ? 0 : 1;
        }

        if ($request->has('has_real_property')) {
            $update['has_real_property'] = $request->has_real_property !== '' ? $request->has_real_property : null;
        }

        if ($request->has('has_car')) {
            $update['has_car'] = $request->has_car !== '' ? $request->has_car : null;
        }

        $application->update($update);

        if (!empty($application->first_name) && !empty($application->consumer)) {
            $application->consumer->first_name = $application->first_name;
            $application->consumer->last_name = $application->last_name ?? null;
            $application->consumer->birth = $request->age ?? null;

            if ($application->consumer->isDirty(['first_name', 'last_name', 'birth'])) {
                $application->consumer->save();
            }
        }

        return $application;
    }

    public function attachPdf(Application $application, UploadedFile $attachment): Application
    {
        $attachment_filename = 'application_' . $application->id
            . '_' . $attachment->hashName();
        $attachment->storeAs(Application::$STORAGE_PATH, $attachment_filename);
        $application->pdf = $attachment_filename;
        $application->has_bank_attachment = 1;
        $application->save();

        return $application;
    }

    public function updateCompletionPercentage(Application $application, $completion_percentage): Application
    {
        if ($completion_percentage > $application->completion_percentage) {
            $application->update([
                'completion_percentage' => $completion_percentage,
            ]);
        }

        return $application;
    }

    public function storeInSession(Application $application, Request $request): void
    {
        $request->session()->put(static::SESSION_KEY, $application->id);
    }

    public function deleteFromSession(Request $request): void
    {
        $request->session()->forget(static::SESSION_KEY);
    }

    protected function linkWithConsumers(Application $application): void
    {
        $application->fillConsumerId(); // check for existent consumer

        // create new consumer from application
        if (empty($application->consumer_id) && !empty($application->phone)) {
            $consumer = $this->consumer_service->createFromApplication($application);
            $application->consumer_id = $consumer->id;
            $application->save();
        }

        if (empty($application->subscribed)) {
            return;
        }

        if (empty($application->consumer_id)) {
            return;
        }

        if (!ConsumerEmailVerificationRequest::query()->where('consumer_id', $application->consumer_id)->where('email', $application->email)->exists()) {
            ConsumerEmailVerificationRequest::query()
                ->create([
                    'consumer_id' => $application->consumer_id,
                    'application_id' => $application->id,
                    'email' => $application->email,
                ]);
        }

        if (!ConsumerPhoneVerificationRequest::query()->where('consumer_id', $application->consumer_id)->where('phone', $application->phone)->exists()) {
            ConsumerPhoneVerificationRequest::query()
                ->create([
                    'consumer_id' => $application->consumer_id,
                    'application_id' => $application->id,
                    'phone' => $application->phone,
                    'sms_verification_hash' => Str::random(9),
                ]);
        }
    }

    public function fillOfferHashes(Application $application): Application
    {
        if (empty($application->first_offer_hash)) {
            $application->first_offer_hash = Str::random(Application::FIRST_HASH_LENGTH);
            $application->second_offer_hash = Str::random(Application::SECOND_HASH_LENGTH);
            $application->save();
        }

        return $application;
    }

    public function isSpammer(Application $application): bool
    {
        if (ConsumerSMSVerificationCode::where('phone_number', $application->phone)->count() < 500) {
            return false;
        }

        Log::warning('ApplicationService isSpammer() check failed', [
            'application_id' => $application->id,
            'phone' => $application->phone,
        ]);

        return true;
    }

    public function requestedTooManyVerificationCodes(Application $application): bool
    {
        $last_verification_code = ConsumerSMSVerificationCode::where('phone_number', $application->phone)
            ->orderBy('id', 'DESC')
            ->first();

        if (empty($last_verification_code->id)) {
            return false;
        }

        if ($last_verification_code->created_at < now()->format('Y-m-d H:i:00')) {
            return false;
        }

        Log::warning('ApplicationService requestedTooManyVerificationCodes()', [
            'application_id' => $application->id,
            'phone' => $application->phone,
            'last_id' => $last_verification_code->id,
            'last_created_at' => $last_verification_code->created_at,
            'now_in_check' => now()->format('Y-m-d H:i:00'),
            'now_full' => now()->format('Y-m-d H:i:s'),
        ]);

        return true;
    }

    public function ensureUtmSourceGoodaff(Application $application): Application
    {
        if ($application->goodaff_click_id) {
            return $application;
        }

        // Create goodaff click
        try {
            $link = 'https://go.goodaff.com/sosocreditapi.lv/gd';

            $params = [
                'return_json' => 1,
                's1' => CommonHelpers::s1(),
                's2' => $this->utm_data->utm_campaign ?? $application->utm_campaign ?? '',
                's3' => $this->utm_data->gclid ?? $application->gclid ?? '',
                's4' => $this->utm_data->utm_content ?? $application->utm_content ?? '',
                's5' => $this->utm_data->utm_source ?? $application->utm_source ?? '',
                's6' => $this->utm_data->s6 ?? '',
                's7' => $this->utm_data->s7,
                's9' => $application->phone ? (phone_extension() . $application->phone) : '',
                's10' => $application->email ?? '',
                'ip' => CommonHelpers::getIp(),
                'user_agent' => request()->userAgent(),
            ];

            if (config('app.go_rewrites_enabled') === true) {
                $go_rewrites = \App\Models\GoRewrite::getCachedRewrites();

                if (!empty($go_rewrites)) {
                    /** @var \App\Models\GoRewrite $go_rewrite */
                    foreach ($go_rewrites as $go_rewrite) {
                        if ($go_rewrite->matchesParams($params)) {
                            $link = $go_rewrite->rewriteDomain($link);
                            $link = $go_rewrite->rewritePathEnding($link);
                            $params = $go_rewrite->rewriteParams($params);
                        }
                    }
                }
            }

            $url = $link . '?' . http_build_query($params);

            $response = Http::connectTimeout(3)
                ->withUserAgent(request()->userAgent())
                ->get($url);

            if ($response->successful()) {
                $json = $response->json();
                if (!empty($json['click']['click_id'])) {
                    $application->goodaff_click_id = $json['click']['click_id'];
                    $application->save();
                }
            }
        } catch (\Throwable $e) {
            Log::error('Goodaff Error (create click)', [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'utm_source' => $application->utm_source,
                'utm_campaign' => $application->utm_campaign,
                'utm_content' => $application->utm_content,
            ]);
        }

        return $application;
    }

    public function isVerifiedConsumer(Application $application, Consumer $consumer): Application
    {
        $application->email = $consumer->email;
        $application->phone = $consumer->phone;
        $application->phone_verified_at = $consumer->confirmed_at;
        $application->subscribed = $consumer->subscribed;
        $application->agree_to_terms = 1;
        $application->save();

        return $application;
    }

    public function autoPopulate(Application $application, int $id)
    {
        // When first name is already provided for application, or if consumer_id is 0, then return application.
        if (!empty($application->first_name) || empty($application->consumer_id)) {
            return $application;
        }

        // Check for the last application, based on valid consumer_id
        $last_application = Application::where('consumer_id', $id)
            ->where('id', '<', $application->id)
            ->where('completion_percentage', '>=', 80)
            ->where('created_at', '>=', now()->subMonths(6)) // in case if number has changed owner, providers are allowed to recirculate DID after 6 months
            ->orderBy('id', 'DESC')
            ->first();

        if (!empty($last_application->id)) {
            $application->first_name = $last_application->first_name;
            $application->last_name = $last_application->last_name;
            $application->personal_code = $last_application->personal_code;
            $application->age = $last_application->age;
            $application->education = $last_application->education;
            $application->employment_status = $last_application->employment_status;
            $application->salary = $last_application->salary;
            $application->disbursement_iban = $last_application->disbursement_iban ?? null;
            $application->credit_history = $last_application->credit_history;
            $application->monthly_debit = $last_application->monthly_debit;
            $application->address_street = $last_application->address_street;
            $application->address_city = $last_application->address_city;
            $application->address_zipcode = $last_application->address_zipcode;
            $application->marital_status = $last_application->marital_status;
            $application->is_political_person = $last_application->is_political_person;
            $application->cadastral_number = $last_application->cadastral_number;
            $application->car_number = $last_application->car_number;
            $application->has_real_property = $last_application->has_real_property;
            $application->has_car = $last_application->has_car;
            $application->dependants = $last_application->dependants;
        }

        return $application;
    }

    public static function syncExportSummaryWithData(Application $application, GoodAffDataService $data): Application
    {
        $exports_summary = $data->getExportsSummary($application);
        $application->updateExportsSummary($exports_summary);

        return $application;
    }

    public function fillExportsSummary(Application $application, $providers): Application
    {
        $summary = $providers->pluck('slug')->mapWithKeys(function ($slug) {
            return [$slug => ''];
        });

        $application->updateExportsSummary($summary->toArray());

        return $application;
    }

    public static function fillExportsSummaryWithActiveProviders(Application $application): Application
    {
        $currentProviders = Lender::getCurrentCategoryProviders($application);

        $blankSummary = $currentProviders->pluck('slug')->mapWithKeys(function ($slug) {
            return [$slug => ''];
        });

        $goCardlessRequiredFor = LenderConfigService::listWhereGcRequisitionIsRequired();
        foreach ($blankSummary as $key => $value) {
            if (in_array($key, $goCardlessRequiredFor)) {
                $blankSummary->put($key, 'PendingGC');
            }
        }

        $application->updateExportsSummary($blankSummary->toArray());

        return $application;
    }

    public function pluckFromExportsSummary(Application $application, string $providerSlug): Application
    {
        $currentSummary = $application->exports_summary ?? [];
        unset($currentSummary[$providerSlug]);

        $application->updateExportsSummary($currentSummary);

        return $application;
    }

    public function addToExportsSummary(Application $application, string $providerSlug): Application
    {
        $currentSummary = $application->exports_summary ?? [];
        $currentSummary[$providerSlug] = '';

        $application->updateExportsSummary($currentSummary);

        return $application;
    }

    public static function verifyAgeWithSsn(Application $application): bool
    {
        if (str_starts_with($application->personal_code, '32')) {
            return true; // Latvian new SSN does not contain DOB info
        }

        $personal_code = Str::replace('_', '', $application->personal_code);
        $exploded_ssn = explode('-', $personal_code, 2);
        $exploded_age = explode('-', $application->age);

        $first_part_ssn = $exploded_ssn[0]; // DDMMYY

        $year = $exploded_age[0];
        $month = $exploded_age[1];
        $day = $exploded_age[2];
        $year_last_two_digits = substr($year, 2);

        $compare_String = $day . $month . $year_last_two_digits; // DDMMYY

        return $first_part_ssn === $compare_String;
    }

    /**
     * Validate SSN checksum using official Latvian formula
     *
     * Given ABCDEF-XGHIZ:
     * Z must equal (1101 - (1*A + 6*B + 3*C + 7*D + 9*E + 10*F + 5*X + 8*G + 4*H + 2*I)) mod 11 mod 10
     */
    public static function validateSSNChecksum(string $ssn): bool
    {
        $clean = str_replace('-', '', $ssn);

        $weights = [1, 6, 3, 7, 9, 10, 5, 8, 4, 2];

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int)$clean[$i] * $weights[$i];
        }

        $expectedChecksum = (1101 - $sum) % 11 % 10;

        $actualChecksum = (int)$clean[10];

        return $expectedChecksum === $actualChecksum;
    }

    public static function consumerOverApplicationLimit(Consumer $consumer): bool
    {
        if (in_array($consumer->phone, [27485924, 20178002])) {
            return false;
        }

        $applicationLimit = Setting::retrieve('consumer_daily_application_limit');
        return $consumer->applications()
                ->whereDate('created_at', today())
                ->count() >= $applicationLimit;
    }
}
