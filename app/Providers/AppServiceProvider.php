<?php

namespace App\Providers;

use App\Contracts\ConsumerSmsServiceInterface;
use App\Contracts\HoiFmServiceInterface;
use App\Contracts\OfferPageLinkStrategyInterface;
use App\Contracts\OfferServiceInterface;
use App\Contracts\SendSmsInterface;
use App\Contracts\SettingStorage;
use App\Models\Application;
use App\Models\Consumer;
use App\Models\Setting;
use App\Observers\ApplicationObserver;
use App\Observers\ConsumerObserver;
use App\Services\HoiFm\HoiFmService;
use App\Services\LenderService;
use App\Services\OfferPage\OfferPageLinkStrategy;
use App\Services\Sms\HoiFmSmsService;
use Illuminate\Cache\RateLimiting\Limit;
//use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Services\NordigenService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(HoiFmServiceInterface::class, HoiFmService::class);
        $this->app->singleton(ConsumerSmsServiceInterface::class, HoiFmSmsService::class);
        $this->app->singleton(SendSmsInterface::class, HoiFmSmsService::class);
        $this->app->singleton(OfferPageLinkStrategyInterface::class, OfferPageLinkStrategy::class);
        $this->app->singleton(OfferServiceInterface::class, LenderService::class);
        $this->app->singleton(SettingStorage::class, Setting::class);

        $this->app->singleton(NordigenService::class, function ($app) {
            return new NordigenService(
                config('services.gocardless.secret_id'),
                config('services.gocardless.secret_key')
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Application::observe(ApplicationObserver::class);
        Consumer::observe(ConsumerObserver::class);

        //        Model::shouldBeStrict(!$this->app->isProduction());

        Response::macro('api_response', function (string $message, $status_code = 200) {
            return response()->json([
                'message' => $message
            ], $status_code);
        });

        RateLimiter::for('unsubscribe', function (Request $request) {
            return Limit::perMinute(5);
        });
    }
}
