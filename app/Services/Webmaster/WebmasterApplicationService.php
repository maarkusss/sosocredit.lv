<?php

namespace App\Services\Webmaster;

use App\Models\Application;
use App\Models\Lender;
use App\Models\Section;
use App\Services\LenderConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class WebmasterApplicationService
{
    public static function create(Request $request): Application
    {
        $token = Str::random(55);
        $token .= Str::random(7);

        /** @var Section $section */
        $section = Section::where('common_name', $request->loantype)
            ->where('template', Section::TEMPLATE_APPLICANT_FORM_LANDING_PAGE)
            ->where('lang', $request->lang)
            ->active()
            ->firstOrFail();

        return Application::create([
            'token' => $token,
            'fingerprint' => null,
            'webmaster_token' => $request->webmaster['token'],
            'email' => $request->email,
            'phone' => $request->phone,
            'first_name' => $request->first_name ?? null,
            'last_name' => $request->last_name ?? null,
            'personal_code' => $request->personal_code ?? null,
            'lang' => $request->lang,
            'amount' => $request->amount,
            'term_months' => $request->term_months,
            'is_political_person' => $request->is_political_person ?? 0,
            'has_real_property' => $request->has_real_property ?? null,
            'has_car' => $request->has_car ?? null,
            'employment_status' => $request->employment_status ?? null,
            'education' => $request->education ?? null,
            'marital_status' => $request->marital_status ?? null,
            'address_street' => $request->address_street ?? null,
            'address_city' => $request->address_city ?? null,
            'address_zipcode' => $request->address_zipcode ?? null,
            'disbursement_iban' => $request->disbursement_iban ?? null,
            'salary' => $request->salary ?? null,
            'monthly_debit' => $request->monthly_debit ?? null,
            'age' => $request->age ?? null,
            'credit_history' => null,
            'dependants' => $request->dependants ?? 0,
            'loantype' => $section->common_name,
            'section_id' => $section->id,
            'company_name' => $request->company_name ?? null,
            'company_reg_nr' => $request->company_reg_nr ?? null,
            'company_contact_person' => null,
            'cadastral_number' => $request->cadastral_number ?? null,
            'car_number' => $request->car_number ?? null,
            'subscribed' => 0,
            'automated' => 0,
            'agree_to_terms' => 0,
            'ip' => $request->ip ? $request->ip : request()->ip(),
            'user_agent' => $request->user_agent ? $request->user_agent : request()->userAgent(),
            'utm_source' => $request->s5 ?? null,
            'utm_medium' => null,
            'utm_campaign' => $request->s2 ?? null,
            'utm_content' => $request->s4 ?? null,
            'gclid' => $request->s3 ?? null,
            'completion_percentage' => 40,
        ]);
    }

    public static function ensureUtmSourceGoodaff(Application $application, Request $request): Application
    {
        if ($application->goodaff_click_id) {
            return $application;
        }

        try {
            $link = 'https://go.goodaff.com/sosocreditapi.lv/' . $application->webmaster_token;

            $params = [
                'return_json' => 1,
                's1' => $request->s1 ?? null,
                's2' => $request->s2 ?? null,
                's3' => $request->s3 ?? null,
                's4' => $request->s4 ?? null,
                's5' => $request->s5 ?? null,
                'ip' => $application->ip ? $application->ip : null,
                'user_agent' => $application->user_agent ? Str::limit($application->user_agent, 512, '') : null,
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

            $response = Http::connectTimeout(3)->get($url);

            if ($response->successful()) {
                $json = $response->json();
                if (!empty($json['click']['click_id'])) {
                    $application->goodaff_click_id = $json['click']['click_id'];
                    $application->save();
                }
            }
        } catch (Throwable $e) {
            Log::error('Goodaff WebmasterApi Error (create click)', [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'application_id' => $application->id,
            ]);
        }

        return $application;
    }
}
