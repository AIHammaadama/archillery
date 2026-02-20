<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Force all emails to specific address in local/testing environment
        if ($this->app->environment(['local', 'development'])) {
            \Illuminate\Support\Facades\Mail::alwaysTo('ahmadidris67@gmail.com');
        }
    }
}
