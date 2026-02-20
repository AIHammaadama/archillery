<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
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
        \App\Models\Project::class => \App\Policies\ProjectPolicy::class,
        \App\Models\ProcurementRequest::class => \App\Policies\ProcurementRequestPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('manage-projects', fn($user) => $user->hasPermission('manage-projects'));
        Gate::define('manage-reports', fn($user) => $user->hasPermission('manage-reports'));
        Gate::define('manage-requests', fn($user) => $user->hasPermission('manage-requests'));
        Gate::define('manage-system-settings', fn($user) => $user->hasPermission('manage-system-settings'));
        Gate::define('manage-vendors', fn($user) => $user->hasPermission('manage-vendors'));
        Gate::define('manage-users', fn($user) => $user->hasPermission('manage-users'));
        Gate::define('manage-audits', fn($user) => $user->hasPermission('manage-audits'));
    }
}