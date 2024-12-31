<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::useTokenModel(Passport::tokenModel());
        Passport::useClientModel(Passport::clientModel());
        Passport::useAuthCodeModel(Passport::authCodeModel());
        Passport::usePersonalAccessClientModel(Passport::personalAccessClientModel());
    }
}
