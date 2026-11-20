<?php

namespace App\Providers;

use App\Events\ApplicationPhoneVerified;
use App\Events\ApplicationRequiresSmsVerificationCode;
use App\Events\ConsumerEmailVerified;
use App\Events\ConsumerPhoneVerified;
use App\Events\ConsumerRequiresEmailVerificationLink;
use App\Events\ConsumerRequiresSmsVerificationCode;
use App\Events\ConsumerReSignup;
use App\Events\ConsumerSignup;
use App\Events\ConsumerVisitedOfferLink;
use App\Listeners\Applications\AddVerifiedApplicationPhoneToHoiFm;
use App\Listeners\Applications\SendVerificationSmsToApplication;
use App\Listeners\Consumers\AddVerifiedEmailToHoiFm;
use App\Listeners\Consumers\AddVerifiedPhoneToHoiFm;
use App\Listeners\Consumers\AddVerifiedPhoneToRc;
use App\Listeners\Consumers\SendOfferEmailToVerifiedAndSubscribed;
use App\Listeners\Consumers\SendOfferSmsToVerified;
use App\Listeners\Consumers\SendVerificationSms;
use App\Listeners\Consumers\SendVerificationEmail;
use App\Listeners\Consumers\UpdateHoiTokenForPhone;
use App\Listeners\LogFailedAuthenticationAttempt;
use App\Listeners\LogSuccessfulLogin;
use Illuminate\Auth\Events\Failed as AdminLoginFailed;
use Illuminate\Auth\Events\Login as AdminLoginSuccessful;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        AdminLoginFailed::class => [
            LogFailedAuthenticationAttempt::class,
        ],

        AdminLoginSuccessful::class => [
            LogSuccessfulLogin::class,
        ],

        ApplicationRequiresSmsVerificationCode::class => [
            SendVerificationSmsToApplication::class,
        ],

        ApplicationPhoneVerified::class => [
            AddVerifiedApplicationPhoneToHoiFm::class,
        ],

        ConsumerSignup::class => [
            AddVerifiedPhoneToRc::class,
        ],

        ConsumerReSignup::class => [
            SendOfferSmsToVerified::class,
//            SendOfferEmailToVerifiedAndSubscribed::class,
        ],

        ConsumerRequiresEmailVerificationLink::class => [
            SendVerificationEmail::class,
        ],

        ConsumerRequiresSmsVerificationCode::class => [
            SendVerificationSms::class,
        ],

        ConsumerPhoneVerified::class => [ // Currently disabled 17.09.2025
//            AddVerifiedPhoneToHoiFm::class,
//            SendOfferSmsToVerified::class,
        ],

        ConsumerEmailVerified::class => [
            AddVerifiedEmailToHoiFm::class,
//            SendOfferEmailToVerifiedAndSubscribed::class,
        ],

        ConsumerVisitedOfferLink::class => [
            UpdateHoiTokenForPhone::class,
            AddVerifiedEmailToHoiFm::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
