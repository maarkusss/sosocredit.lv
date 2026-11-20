<?php

use Illuminate\Support\Facades\Facade;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'Europe/Riga',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'lv',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'lv',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'lv_LV',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => 'file',
        // 'store'  => 'redis',
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\TelescopeServiceProvider::class,
        App\Providers\ViewComposerServiceProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        // 'ExampleClass' => App\Example\ExampleClass::class,
    ])->toArray(),


    /*
    |--------------------------------------------------------------------------
    | Available locales
    |--------------------------------------------------------------------------
    |
    | List all locales that have translations available
    | You should have lang files for each locale specified here
    | Otherwise the content will fallback to default locale
    |
    */

    'available_locales' => [
        'English' => 'en',
//        'Kazakh' => 'kk',
        'Latvian' => 'lv',
        'Russian' => 'ru',
//        'Ukrainian' => 'uk',
//        'Vietnamese' => 'vi',
    ],

    /*
    |--------------------------------------------------------------------------
    | Global site variables
    |--------------------------------------------------------------------------
    */

    'site_creation_year' => env('SITE_CREATION_YEAR'),
    'site_domain' => env('SITE_DOMAIN'),
    'site_url_sms' => env('SITE_URL_SMS'),

    'internal_log_enabled' => true,

    /**
     * Show Cookies notification in public layout (popup at the bottom of the page)
     */
    'show_cookies_notification' => true,

    /**
     * Enable Registration for User model
     */
    'registration_enabled' => env('REGISTRATION_ENABLED', false),

    /**
     * Currency symbol to be used accross the site
     */
    'currency_symbol' => '€',
    'currency' => env('CURRENCY', 'EUR'),
    'currency_as_prefix' => env('CURRENCY_AS_PREFIX', false),
    'currency_separate_thousands' => env('CURRENCY_SEPARATE_THOUSANDS', false),
    'currency_separate_thousands_comma' => env('CURRENCY_SEPARATE_THOUSANDS_COMMA', false),
    'currency_separate_thousands_space' => env('CURRENCY_SEPARATE_THOUSANDS_SPACE', true),

    /**
     * Whether the provided email address must be verified via email confirmation link
     */
    'verify_email' => env('VERIFY_EMAIL', true),

    /**
     * Whether the provided phone number must be verified via SMS
     */
    'verify_phone' => true,

    /**
     * Whether the phone number provided via API must be verified via SMS
     */
    'verify_phone_in_api' => true,

    /**
     * Email addresses of users who have access to Internal admin sections, like Telescope or Log Viewer
     */
    'superadmin_emails' => [
        'matisssefers@gmail.com',
        'markusssefers@gmail.com',
        'artursmelnis@goodday.group',
        'martins.vilskersts@gmail.com',
    ],

    /**
     * Enable Go Rewrites functionality for Go links
     */
    'go_rewrites_enabled' => env('GO_REWRITES_ENABLED', false),

    /**
     * Classes to iterate Lender models through before output
     * @return array
     */
    'lenders_listing_filters' => [
        \App\Filters\FilterLenders::class,
    ],

    /**
     * Should we unsubscribe Not-found Consumers (consumers from old database)
     * @return bool
     */
    'unsubscribe_not_found_consumers' => env('UNSUBSCRIBE_NOT_FOUND_CONSUMERS', false),

    /**
     * @return string
     */
    'country_code' => env('COUNTRY', 'lv'),

    /**
     * @return bool
     */
    'send_sms_from_local_env' => env('SMS_SEND_FROM_LOCAL_ENV', false),

    /**
     * Broker email
     * @return string
     */
    'broker_email' => env('BROKER_EMAIL', 'dokumenti@sosocredit.lv'),

    /**
     * Broker phone number
     * @return string
     */
    'broker_phone' => env('BROKER_PHONE', '+371 23371842'),

    /**
     * Enable Broker functionality
     * @return bool
     */
    'broker_enabled' => env('BROKER_ENABLED', true),

    /**
     * Term picker enabled
     */
    'term_picker_enabled' => env('TERM_PICKER_ENABLED', true),

    /**
     * Toggle loan modal, news or blog section
     */
    'loan_modal_enabled' => env('LOAN_MODAL_ENABLED', true),

    /**
     * Enabled RC functionality
     */
    'rc_enabled' => env('RC_ENABLED', false),

    /**
     * LenderCampaign IDs to exclude from FirstLenderOffer mail
     */
    'first_lender_offer_exclude_campaign_ids' => [
        17, // Sefinance
        907, // Latkredits
    ],

    /**
     * LenderCampaign IDs to include into SecondLenderOffer mail
     */
    'second_lender_offer_include_campaign_ids' => [
        17, // Sefinance
        907, // Latkredits
    ],

    'load_avg_threshold' => env('LOAD_AVG_THRESHOLD', 0),
    'public_css_class' => env('PUBLIC_CSS_CLASS', 'public-section_primary'),
    'sms_alpha_name' => env('SMS_ALPHA_NAME', 'Sosocredit'),
    'footer_address' => env('FOOTER_ADDRESS', ''),

    'whatsapp' => [
        'msgs_url' => env('WA_MESSAGE_URL', 'https://wa.me/message/'),
        'lv' => env('WA_LV', ''),
        'ru' => env('WA_RU', ''),
    ],

    'address_autocomplete_enabled' => env('ADDRESS_AUTOCOMPLETE_ENABLED', false),
    'send_sms_via_rc' => env('SEND_SMS_VIA_RC', false),
];
