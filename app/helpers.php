<?php

use App\Contracts\ConsumerSmsServiceInterface;
use App\Models\LoanType;
use App\Models\Section;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Request;

if (!function_exists('strip_phone_number_formatting')) {
    function strip_phone_number_formatting(string $phone_number, ?string $extension = ''): string
    {
        if (empty($extension)) {
            $extension = '+' . phone_extension();
        }

        if (!str_starts_with($extension, '+')) {
            $extension = '+' . $extension;
        }

        return str_replace(['(', ')', $extension, ' '], '', $phone_number);
    }
}

if (!function_exists('get_browser_name')) {
    function get_browser_name($user_agent): string
    {
        // Make case-insensitive.
        $t = strtolower($user_agent);
        // If the string *starts* with the string, strpos returns 0 (i.e., FALSE). Do a ghetto hack and start with a space.
        // "[strpos()] may return Boolean FALSE, but may also return a non-Boolean value which evaluates to FALSE."
        //     http://php.net/manual/en/function.strpos.php
        $t = ' ' . $t;
        // Humans / Regular Users
        if (strpos($t, 'opera') || strpos($t, 'opr/')) {
            return 'Opera';
        } elseif (strpos($t, 'edge') || strpos($t, 'Edg')) {
            return 'Edge';
        } elseif (strpos($t, 'chrome')) {
            return 'Chrome';
        } elseif (strpos($t, 'safari')) {
            return 'Safari';
        } elseif (strpos($t, 'firefox')) {
            return 'Firefox';
        } elseif (strpos($t, 'msie') || strpos($t, 'trident/7')) {
            return 'Internet Explorer';
        } // Search Engines
        elseif (strpos($t, 'google')) {
            return '[Bot] Googlebot';
        } elseif (strpos($t, 'bing')) {
            return '[Bot] Bingbot';
        } elseif (strpos($t, 'slurp')) {
            return '[Bot] Yahoo! Slurp';
        } elseif (strpos($t, 'duckduckgo')) {
            return '[Bot] DuckDuckBot';
        } elseif (strpos($t, 'baidu')) {
            return '[Bot] Baidu';
        } elseif (strpos($t, 'yandex')) {
            return '[Bot] Yandex';
        } elseif (strpos($t, 'sogou')) {
            return '[Bot] Sogou';
        } elseif (strpos($t, 'exabot')) {
            return '[Bot] Exabot';
        } elseif (strpos($t, 'msn')) {
            return '[Bot] MSN';
        } // Common Tools and Bots
        elseif (strpos($t, 'mj12bot')) {
            return '[Bot] Majestic';
        } elseif (strpos($t, 'ahrefs')) {
            return '[Bot] Ahrefs';
        } elseif (strpos($t, 'semrush')) {
            return '[Bot] SEMRush';
        } elseif (strpos($t, 'rogerbot') || strpos($t, 'dotbot')) {
            return '[Bot] Moz or OpenSiteExplorer';
        } elseif (strpos($t, 'frog') || strpos($t, 'screaming')) {
            return '[Bot] Screaming Frog';
        } // Miscellaneous
        elseif (strpos($t, 'facebook')) {
            return '[Bot] Facebook';
        } elseif (strpos($t, 'pinterest')) {
            return '[Bot] Pinterest';
        } // Check for strings commonly used in bot user agents
        elseif (
            strpos($t, 'crawler') || strpos($t, 'api') ||
            strpos($t, 'spider') || strpos($t, 'http') ||
            strpos($t, 'bot') || strpos($t, 'archive') ||
            strpos($t, 'info') || strpos($t, 'data')
        ) {
            return '[Bot] Other';
        }

        return 'Unknown';
    }
}

if (!function_exists('get_os')) {
    function get_os($user_agent): string
    {
        $os_platform = 'Unknown';

        if (!$user_agent) {
            return $os_platform;
        }

        $os_array = [
            '/windows nt 10/i' => 'Windows',
            '/windows nt 6.3/i' => 'Windows',
            '/windows nt 6.2/i' => 'Windows',
            '/windows nt 6.1/i' => 'Windows',
            '/windows nt 6.0/i' => 'Windows',
            '/windows nt 5.2/i' => 'Windows',
            '/windows nt 5.1/i' => 'Windows',
            '/windows xp/i' => 'Windows',
            '/windows nt 5.0/i' => 'Windows',
            '/windows me/i' => 'Windows',
            '/win98/i' => 'Windows',
            '/win95/i' => 'Windows',
            '/win16/i' => 'Windows',
            '/macintosh|mac os x/i' => 'Mac OS',
            '/mac_powerpc/i' => 'Mac OS',
            '/linux/i' => 'Linux',
            '/ubuntu/i' => 'Linux',
            '/iphone/i' => 'iOS',
            '/ipod/i' => 'iPodOS',
            '/ipad/i' => 'iPadOS',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile',
        ];
        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
            }
        }

        return $os_platform;
    }
}

if (!function_exists('check_device')) {
    function check_device($user_agent): string
    {
        if ($user_agent) {
            if (is_numeric(strpos(strtolower($user_agent), 'mobile'))) {
                return is_numeric(strpos(strtolower($user_agent), 'tablet')) ? 'Tablet' : 'Phone';
            } else {
                return 'Desktop';
            }
        } else {
            return 'Unknown';
        }
    }
}

if (!function_exists('get_consumer_user_agent_data')) {
    function get_consumer_user_agent_data(): array
    {
        $http_user_agent = request()->server('HTTP_USER_AGENT');
        if ($http_user_agent) {
            $device = urlencode(check_device($http_user_agent));
            $os = urlencode(get_os($http_user_agent));
            $browser = urlencode(get_browser_name($http_user_agent));
        }

        return [
            'device' => $device ?? '',
            'os' => $os ?? '',
            'browser' => $browser ?? '',
        ];
    }
}

if (!function_exists('is_bot')) {
    function is_bot(): bool
    {
        if (
            stristr(request()->header('user-agent'), 'bot') ||
            stristr(request()->header('user-agent'), 'crawler') ||
            stristr(request()->header('user-agent'), 'Skype')
        ) {
            return true;
        }

        return false;
    }
}

if (!function_exists('inactive_locales')) {
    function inactive_locales(): array
    {
        return collect(available_locales())
            ->reject(fn ($locale) => $locale == app()->getLocale())
            ->toArray();
    }
}

if (!function_exists('default_locale')) {
    function default_locale(): string
    {
        return config('app.locale');
    }
}

if (!function_exists('available_locales')) {
    function available_locales(): array
    {
        return Cache::rememberForever('settings_available_locales', function () {
            $available_locales = [];
            $settings = Setting::query()
                ->where('name', 'lang')
                ->orderBy('value', 'ASC')
                ->select('value')
                ->get();

            if ($settings->isNotEmpty()) {
                foreach ($settings as $setting) {
                    if (!empty($setting->value)) {
                        $available_locales[] = $setting->value;
                    }
                }
            }

            return $available_locales;
        });
    }
}

if (!function_exists('available_permissions')) {
    function available_permissions(string $type = 'add'): array
    {
        $available_permissions = [];

        $available_permissions['admins'] = [
            'view admins',
            'edit admins',
            'add admins',
            'delete admins',
        ];
        $available_permissions['bans'] = [
            'view bans',
            'edit bans',
            'add bans',
            'delete bans',
        ];
        $available_permissions['consumers'] = [
            'view consumers',
            'delete consumers',
        ];
        $available_permissions['faqs'] = [
            'view faqs',
            'edit faqs',
            'add faqs',
            'delete faqs',
        ];
        $available_permissions['news'] = [
            'view news',
            'edit news',
            'add news',
            'delete news',
        ];
        $available_permissions['go-rewrites'] = [
            'view go-rewrites',
            'edit go-rewrites',
            'add go-rewrites',
            'delete go-rewrites',
        ];
        $available_permissions['lenders'] = [
            'view lenders',
            'edit lenders',
            'add lenders',
            'delete lenders',
        ];
        $available_permissions['loan types'] = [
            'view loantypes',
            'edit loantypes',
            'add loantypes',
            'delete loantypes',
        ];
        $available_permissions['postbacks'] = [
            'access postbacks',
        ];
        $available_permissions['redirects'] = [
            'view redirects',
            'edit redirects',
            'add redirects',
            'delete redirects',
        ];
        $available_permissions['reviews'] = [
            'view reviews',
            'edit reviews',
            'add reviews',
            'delete reviews',
        ];
        $available_permissions['sections'] = [
            'view sections',
            'edit sections',
            'add sections',
        ];
        $available_permissions['settings'] = [
            'view settings',
            'edit settings',
        ];
        $available_permissions['statistics'] = [
            'view statistics',
        ];
        $available_permissions['visitors'] = [
            'view visitors',
        ];

        return $available_permissions;
    }
}

if (!function_exists('flag_filename')) {
    function flag_filename(?string $locale = null): string
    {
        if (empty($locale)) {
            $locale = app()->getLocale();
        }

        $flag_filename = strtolower($locale);

        if ($flag_filename === 'kk') {
            $flag_filename = 'kz';
        } elseif ($flag_filename === 'en') {
            $flag_filename = 'gb';
        } elseif ($flag_filename === 'uk') {
            $flag_filename = 'ua';
        } elseif ($flag_filename === 'vi') {
            $flag_filename = 'vn';
        }

        return $flag_filename;
    }
}

if (!function_exists('phone_extension')) {
    /**
     * Returns the prefix to be added before all phone numbers, depends on main country of the website
     * Must start with a number, i.e. cannot start with "+"
     *
     * @return string
     */
    function phone_extension(): string
    {
        return Cache::rememberForever('settings_country_code', function () {
            return Setting::retrieve('country_code');
        });
    }
}

if (!function_exists('site_name')) {
    function site_name(): string
    {
        return Cache::rememberForever('settings_site_name', function () {
            return Setting::retrieve('name');
        });
    }
}

if (!function_exists('logo')) {
    function logo(): string
    {
        return Cache::rememberForever('settings_logo', function () {
            return Setting::retrieve('logo');
        });
    }
}

if (!function_exists('s1')) {
    function s1(): string
    {
        return Cache::rememberForever('settings_s1', function () {
            return Setting::retrieve('prefix');
        });
    }
}

if (!function_exists('api_default_zero_percent_value')) {
    function api_default_zero_percent_value(): string
    {
        return Cache::rememberForever('settings_api_default_zero_percent_value', function () {
            return Setting::retrieve('api_default_zero_percent_value');
        });
    }
}

if (!function_exists('hoi_fm_sender_name')) {
    function hoi_fm_sender_name(): string
    {
        return Cache::rememberForever('settings_hoi_fm_sender_name', function () {
            return Setting::retrieve('hoi_fm_sender_name');
        });
    }
}

if (!function_exists('flush_menus_cache')) {
    function flush_menus_cache(): void
    {
        $locales = available_locales();
        if (!empty($locales)) {
            foreach ($locales as $locale) {
                Cache::forget('menus_footermenu2_' . $locale);
                Cache::forget('menus_headermenu_' . $locale);
            }
        }
    }
}

if (!function_exists('flush_settings_cache')) {
    function flush_settings_cache(): void
    {
        Cache::forget('settings_available_locales');
        Cache::forget('settings_country_code');
        Cache::forget('settings_site_name');
        Cache::forget('settings_logo');
        Cache::forget('settings_s1');
        Cache::forget('settings_api_default_zero_percent_value');
        Cache::forget('settings_hoi_fm_sender_name');
    }
}

if (!function_exists('convert_fbc_to_fbclid')) {
    function convert_fbc_to_fbclid($fbc = null)
    {
        if (empty($fbc) || !is_string($fbc)) {
            return null;
        }

        if (!str_starts_with($fbc, 'fb.')) {
            return null;
        }

        $exploded = explode('.', $fbc);
        if (!empty($exploded) && count($exploded) === 4) {
            $fbclid = end($exploded);

            if (!empty($fbclid)) {
                return $fbclid;
            }
        }

        return null;
    }
}

if (!function_exists('override_locale_if_provided')) {
    function override_locale_if_provided(): void
    {
        if (request()->input('locale') && request()->input('locale') != app()->getLocale()) {
            app()->setLocale(request()->input('locale'));
        }
    }
}

if (!function_exists('currency_symbol')) {
    function currency_symbol(): string
    {
        return config('app.currency_symbol') ?? '';
    }
}

if (!function_exists('currency')) {
    function currency(): string
    {
        return config('app.currency') ?? '';
    }
}

if (!function_exists('app_events_list')) {
    function app_events_list(): array
    {
        $app_events = Storage::disk('app')->files('Events');
        $event_names = [];
        if (!empty($app_events)) {
            foreach ($app_events as $app_event) {
                if (str_starts_with($app_event, 'Events/') && str_ends_with($app_event, '.php')) {
                    $full_class_name = str_replace('.php', '', $app_event);
                    $full_class_name = str_replace('Events/', '', $full_class_name);
                    $full_class_name = '\\App\\Events\\' . $full_class_name;
                    $event_names[] = $full_class_name;
                }
            }
        }

        return $event_names;
    }
}

if (!function_exists('sms_gateway_name')) {
    function sms_gateway_name(): string
    {
        $sms_consumer_service = app(ConsumerSmsServiceInterface::class);
        $class_name = (new ReflectionClass($sms_consumer_service))->getShortName();
        $sms_gateway_name = $class_name;

        if (str_ends_with($class_name, 'ConsumerService')) {
            $sms_gateway_name = str_replace('ConsumerService', '', $sms_gateway_name);
        } elseif (str_ends_with($class_name, 'SmsService')) {
            $sms_gateway_name = str_replace('SmsService', '', $sms_gateway_name);
        }

        if ($sms_gateway_name === 'NullSms') {
            $sms_gateway_name = 'Null';
        }

        return $sms_gateway_name;
    }
}

if (!function_exists('build_url')) {
    function build_url(array $parts): string
    {
        return (isset($parts['scheme']) ? "{$parts['scheme']}:" : '') .
            ((isset($parts['user']) || isset($parts['host'])) ? '//' : '') .
            (isset($parts['user']) ? "{$parts['user']}" : '') .
            (isset($parts['pass']) ? ":{$parts['pass']}" : '') .
            (isset($parts['user']) ? '@' : '') .
            (isset($parts['host']) ? "{$parts['host']}" : '') .
            (isset($parts['port']) ? ":{$parts['port']}" : '') .
            (isset($parts['path']) ? "{$parts['path']}" : '') .
            (isset($parts['query']) ? "?{$parts['query']}" : '') .
            (isset($parts['fragment']) ? "#{$parts['fragment']}" : '');
    }
}

if (!function_exists('convert_fbc_to_fbclid')) {
    function convert_fbc_to_fbclid($fbc = null)
    {
        if (empty($fbc) || !is_string($fbc)) {
            return null;
        }

        if (!str_starts_with($fbc, 'fb.')) {
            return null;
        }

        $exploded = explode('.', $fbc);
        if (!empty($exploded) && count($exploded) === 4) {
            $fbclid = end($exploded);

            if (!empty($fbclid)) {
                return $fbclid;
            }
        }

        return null;
    }
}

if (!function_exists('generate_breadcrumbs')) {
    function generate_breadcrumbs()
    {
        $segments = Request::segments();
        $breadcrumbs = [];

        $url = url('/');

        if (!empty($segments)) {
            $url .= '/' . $segments[0];

            $breadcrumbs[] = [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Sākums',
                'item' => $url,
            ];
        }

        foreach ($segments as $index => $segment) {
            if ($index === 0) continue;

            $url .= '/' . $segment;

            $title =
            Section::where('route_name', $segment)->value('name') ??
            LoanType::where('route_name', $segment)->value('name') ??
            ucfirst(str_replace('-', ' ', $segment));

            $breadcrumbs[] = [
                '@type' => 'ListItem',
                'position' => count($breadcrumbs) + 1,
                'name' => $title,
                'item' => $url,
            ];
        }

        return $breadcrumbs;
    }
}

if (!function_exists('currency_symbol')) {
    function currency_symbol(): string
    {
        return config('app.currency_symbol') ?? '';
    }
}

if (!function_exists('currency_formatted')) {
    function currency_formatted($amount): string
    {
        $display_amount = $amount;

        if (config('app.currency_separate_thousands')) {
            // for DK, for instance
            $amount = str_replace(' ', '', $amount);
            $display_amount = number_format(sprintf("%.0f", $amount), 0, '', '.');
        }

        if (config('app.currency_separate_thousands_comma')) {
            // for CO, for instance
            $amount = str_replace(' ', '', $amount);
            $display_amount = number_format(sprintf("%.0f", $amount), 0, '', ',');
        }

        if (config('app.currency_separate_thousands_space')) {
            // for CO, for instance
            $amount = str_replace(' ', '', $amount);
            $display_amount = number_format(sprintf("%.0f", $amount), 0, '', ' ');
        }

        if (config('app.currency_as_prefix')) {
            return currency_symbol() . ' ' . $display_amount;
        }

        return $display_amount . ' ' . currency_symbol();
    }
}

if (!function_exists('get_referrer_host')) {
    function get_referrer_host($request): ?string
    {
        $referer = $request->header('referer');

        if (!$referer) {
            return null;
        }

        $host = parse_url($referer, PHP_URL_HOST);

        return $host ? preg_replace('/^www\./', '', $host) : null;
    }
}

if (!function_exists('getMobileParameters')) {
    function getMobileParameters()
    {
        $utm_source = request()->cookie('utm_source');
        $utm_campaign = request()->cookie('utm_campaign');
        $gclid = request()->cookie('gclid');

        if (request()->s2) {
            $utm_campaign = request()->s2;
        }

        if (request()->s3) {
            $gclid = request()->s3;
        }

        if (request()->s5) {
            $utm_source = request()->s5;
        }

        if (request()->utm_source) {
            $utm_source = request()->utm_source;
        }

        if (request()->utm_campaign) {
            $utm_campaign = request()->utm_campaign;
        }

        if (request()->gclid) {
            $gclid = request()->gclid;
        }

        return '&referrer=' . urlencode('&utm_source=' . $utm_source . '&utm_campaign=' . $utm_campaign . '&gclid=' . $gclid);
    }
}

if (!function_exists('formatLoanTypeName')) {
    function formatLoanTypeName(?string $loanType): ?string
    {
        if (!$loanType) {
            return $loanType;
        }

        /** @var Section $section */
        // Find section by loan type name. If multiple same names exists, will take first active.
        $section = Section::where('name', $loanType)
            ->where('template', Section::TEMPLATE_APPLICANT_FORM_LANDING_PAGE)
            ->orderBy('active', 'desc')
            ->first();

        if ($section && $section->common_name) {
            $loanType = $section->common_name;
        }

        return $loanType;
    }
}
