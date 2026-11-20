<?php

namespace App\Models;

use App\Contracts\Consumerable;
use App\Models\Consumer\BelongsToConsumer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use stdClass;

/**
 * @property $id
 * @property $consumer_id
 * @property $token
 * @property $webmaster_token
 * @property $fingerprint
 * @property $lang
 * @property $email
 * @property $phone
 * @property $phone_verified_at
 * @property $address_street
 * @property $address_city
 * @property $address_zipcode
 * @property $amount
 * @property $term_months
 * @property $loantype
 * @property $employment_status
 * @property $education // added 05.2025.
 * @property $marital_status // fixed from martial_status 05.2025.
 * @property $disbursement_iban
 * @property $salary
 * @property $monthly_debit // added 05.2025.
 * @property $age // this holds date of birth in YYYY-MM-DD
 * @property $credit_history
 * @property $company_name
 * @property $company_reg_nr
 * @property $company_contact_person
 * @property $cadastral_number
 * @property $car_number
 * @property $dependants
 * @property $first_name
 * @property $last_name
 * @property $personal_code
 * @property $is_political_person
 * @property $has_real_property // added 01.2026.
 * @property $has_car // added 07.2026.
 * @property $has_bank_attachment
 * @property $coupon_code
 * @property $agree_to_terms
 * @property $subscribed
 * @property $pdf
 * @property $referrer
 * @property $user_agent
 * @property $ip
 * @property $utm_source
 * @property $utm_medium
 * @property $utm_campaign
 * @property $utm_content
 * @property $goodaff_click_id
 * @property $gclid
 * @property $first_offer_hash
 * @property $second_offer_hash
 * @property $will_send_docs_via_email
 * @property $completion_percentage
 * @property $pdf_downloaded_from_ip
 * @property $pdf_downloaded_at
 * @property $last_exported_at
 * @property $created_at
 * @property $updated_at
 * @property $pdf_hash
 * @property $signature
 * @property $actual_age
 * @property $exports_summary // added 10.07.2025
 * @property $section_id
 */
class Application extends Model implements Consumerable
{
    use BelongsToConsumer;

    protected $table = 'applications';
    protected $guarded = [];

    protected $casts = [
        'last_exported_at' => 'datetime',
        'exports_summary' => 'json',
        'has_real_property' => 'integer',
        'has_car' => 'integer',
    ];

    public static $STORAGE_PATH = 'applications/';
    public const MAX_STEPS = 5;

    public const FIRST_HASH_LENGTH = 6;
    public const SECOND_HASH_LENGTH = 7;

    public function consumer(): BelongsTo
    {
        return $this->belongsTo(Consumer::class, 'consumer_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function getHashAttribute(): string
    {
        return sha1($this->created_at . '.' . $this->token);
    }

    public function getActualAgeAttribute(): int
    {
        $actual_age = 0;
        if (!empty($this->personal_code)) {
            try {
                $exploded = explode('-', $this->personal_code);
                if ($exploded !== false && count($exploded) === 2) {
                    $personcode_start = $exploded[0];
                    $dob = Carbon::createFromFormat('dmy', $personcode_start);

                    if ($dob->isFuture()) {
                        $dob->subYears(100);
                    }

                    $actual_age = now()->diffInYears($dob);
                }
            } catch (\Throwable $e) {
            }
        }

        return $actual_age;
    }

    public static function getApplicationTypes($locale = null, $includeAmountsAndTerms = false): array
    {

        if ($locale === 'en_GB') {
            \Log::warning('en_GB locale requested but not available', [
                'available_locales' => available_locales(),
                'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)
            ]);
        }

        if (config('app.broker_enabled') !== true) {
            return [];
        }

        $available_locales = available_locales();

        if (!empty($available_locales)) {
            //            foreach ($available_locales as $available_locale) {
            //                $types[$available_locale][] = [
            //                    'id' => 1,
            //                    'name' => __('Loan comparison', [], $available_locale),
            //                    'url' => '/' . $available_locale,
            //                ];
            //            }

            //            $sections = Section::active()
            //                ->whereIn('lang', $available_locales)
            //                ->where('template', Section::TEMPLATE_HOME)
            //                ->select([
            //                    'id',
            //                    'name',
            //                    'lang',
            //                    'route_name',
            //                ])
            //                ->orderBy('lang', 'ASC')
            //                ->orderBy('order', 'ASC')
            //                ->get();
            //
            //            $current_lang = '';
            //            foreach ($sections as $section) {
            //                if ($section->lang != $current_lang) {
            //                    $current_lang = $section->lang;
            //                }
            //
            //                $icon = 'shopping-bag';
            //
            //                $types[$section->lang][] = [
            //                    'id' => $section->id,
            //                    'icon' => $icon,
            //                    'is_business' => false,
            //                    'is_mortgage' => false,
            //                    'name' => $section->name,
            //                    'url' => '/' . $section->lang,
            //                ];
            //            }

            $sections = Section::active()
                ->whereIn('lang', $available_locales)
                ->where('template', Section::TEMPLATE_APPLICANT_FORM_LANDING_PAGE)
                ->select([
                    'id',
                    'name',
                    'lang',
                    'route_name',
                    'common_name',
                    'json'
                ])
            ->orderBy('lang', 'ASC')
                ->orderBy('order', 'ASC')
                ->get();

            if (!empty($sections) && $sections->isNotEmpty()) {
                $i = 1;
                $current_lang = '';
                foreach ($sections as $section) {
                    /** @var Section $section */
                    if ($section->lang != $current_lang) {
                        $i = 1;
                        $current_lang = $section->lang;
                    }

                    $i++;
                    $icon = '';
                    $is_business = in_array($section->common_name, ['kredits-uznemumam']);
                    $is_mortgage = in_array($section->common_name, ['kredits-pret-kilu']);
                    $is_car_mortgage = in_array($section->common_name, ['kredits-pret-auto-kilu']);

                    if ($i === 2) {
                        $icon = 'shopping-bag';
                    }

                    if ($i === 3) {
                        $icon = 'arrows-pointing-in';
                    }

                    if ($i === 4) {
                        $icon = 'clipboard-document';
                    }

                    if ($i === 5) {
                        $icon = 'home-modern';
                    }

                    if ($i === 6) {
                        $icon = 'car';
                    }

                    if ($i === 7) {
                        $icon = 'arrows-right-left';
                    }

                    if ($i === 8) {
                        $icon = 'wrench';
                    }

                    if ($i === 9) {
                        $icon = 'briefcase';
                    }

                    if ($i === 10) {
                        $icon = 'banknotes';
                    }

                    $sectionData = [
                        'id' => $section->id,
                        'icon' => $icon,
                        'is_business' => $is_business,
                        'is_mortgage' => $is_mortgage,
                        'is_car_mortgage' => $is_car_mortgage,
                        'name' => $section->name,
                        'common_name' => $section->common_name,
                        'url' => '/' . $section->lang . '/' . $section->route_name,
                    ];

                    if ($includeAmountsAndTerms) {
                        $sectionData['amounts'] = $section->getAmounts(null);
                        $sectionData['term_months'] = $section->getTermMonths(null);
                    }

                    $types[$section->lang][] = $sectionData;
                }
            }
        }

        if (empty($types)) {
            return [];
        }

        try {
            if (!empty($locale)) {
                return $types[$locale];
            }
        } catch (\Throwable $e) {
            Log::warning('Wrong locale requested but not available', [$e->getMessage()]);
            return $types['lv'];
        }



        return $types;
    }

    /**
     * @return string[]
     */
    public static function getEmploymentStatuses(): array
    {
        return [
            'hired-employee' => 'Hired employee',
            'state-employee' => 'State employee',
            'self-employed' => 'Self employed',
            'businessman' => 'Businessman',
            'student' => 'Student',
            'pensioner' => 'Pensioner',
            'unemployed' => 'Unemployed',
            'other' => 'Other',
        ];
    }

    /**
     * @return string[]
     */
    public static function getEducationLevels(): array
    {
        return [
            'no-education' => 'No formal education',
            'primary' => 'Primary education',
            'secondary' => 'Secondary education',
            'bachelor' => 'Bachelor\'s degree',
            'master' => 'Master\'s degree',
            'doctorate' => 'Doctorate/PhD',
            'other' => 'Other',
        ];
    }

    /**
     * @return string[]
     */
    public static function getMaritalStatuses(): array
    {
        return [
            'single' => 'Single',
            'married' => 'Married',
            'cohabitating' => 'Cohabitating',
            'divorced' => 'Divorced',
            'widowed' => 'Widowed',
        ];
    }

    public function gocardless_sessions(): HasMany
    {
        return $this->hasMany(ApplicationGocardlessSession::class, 'application_id');
    }

    public function getPdfHashAttribute(): string
    {
        return md5('application' . $this->id) . '.' . hash('sha256', $this->pdf);
    }

    public function getSignatureAttribute(): string
    {
        return hash('sha256', $this->token);
    }

    public function fillConsumerId(): Application
    {
        $shouldSearch = empty($this->consumer_id);

        if (!$shouldSearch && !empty($this->consumer_id)) {
            $currentConsumer = $this->consumer;
            if ($currentConsumer && $currentConsumer->phone !== $this->phone) {
                $shouldSearch = true;
            }
        }

        if ($shouldSearch && !empty($this->phone)) {
            $consumer = Consumer::query()->where('phone', $this->phone)->select(['id', 'email'])->first();
            if (!empty($consumer->id)) {
                $this->consumer_id = $consumer->id;
                $this->save();

                if (empty($consumer->email) && !empty($this->email)) {
                    $consumer->email = $this->email;
                    $consumer->save();
                }
            }
        }

        return $this;
    }

    public function hasVerifiedPhone(): bool
    {
        return !empty($this->consumer) && !empty($this->consumer->confirmed_at);
    }

    public function hasVerifiedEmail(): bool
    {
        return !empty($this->consumer) && !empty($this->consumer->email_verified_at);
    }

    public function getHoiToken(): ?string
    {
        return $this->consumer->hoi_token ?? '';
    }

    public function isSubscribedToEmailsAndSms()
    {
        return !empty($this->subscribed);
    }

    public function hasSignedUpViaApp(): bool
    {
        return !empty($this->utm_source) && $this->utm_source === 'app';
    }

    public function updateExportsSummary(array $summary): void
    {
        if ( ! empty($summary)) {
            $this->exports_summary = $summary;
            $this->save();
        }
    }
    public function getExportsSummary(): stdClass
    {
        return (object) $this->exports_summary;
    }

    public function scopeCheckToken(Builder $query, string $token): Builder
    {
        return $query->where(function ($q) use ($token) {
            // When short token is passed, then search for first 6 characters in token
            if (strlen($token) === 6) {
                $q->where('token', 'like', $token . '%');
            } else {
                $q->where('token', $token);
            }
        });
    }
}
