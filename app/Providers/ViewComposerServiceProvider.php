<?php

namespace App\Providers;

use App\View\Composers\ApplicantFormTypesComposer;
use App\View\Composers\LocalesSwitcherComposer;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('includes.header', LocalesSwitcherComposer::class);
        View::composer('includes.footer', ApplicantFormTypesComposer::class);
    }
}
