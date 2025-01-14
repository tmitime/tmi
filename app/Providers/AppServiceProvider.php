<?php

namespace App\Providers;

use App\Models\Identity as ModelsIdentity;
use App\Models\User;
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
        Identity::useIdentityModel(ModelsIdentity::class);
        Identity::useUserModel(User::class);
    }
}
