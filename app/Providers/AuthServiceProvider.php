<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // LGP gate
        Gate::define('viewInternals', function (?User $user) {
            return !empty($user->email) && in_array($user->email, $this->canSeeInternals());
        });

        // "opcodesio/log-viewer"
        Gate::define('viewLogViewer', function (?User $user) {
            return !empty($user->email) && in_array($user->email, $this->canSeeInternals());
        });
    }

    protected function canSeeInternals(): array
    {
        return config('app.superadmin_emails') ?? [];
    }
}
