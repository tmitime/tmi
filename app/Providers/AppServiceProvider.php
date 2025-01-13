<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Oneofftech\Identities\Facades\Identity;

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
        Identity::useIdentityModel('App\\Models\\Identity');
        Identity::useUserModel('App\\Models\\User');
    }
}
