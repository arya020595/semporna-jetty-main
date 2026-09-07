<?php

namespace App\Providers;

use App\Services\Auth\Contracts\AuthService;
use App\Services\Auth\MockAuthService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
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
        //

        JsonResource::withoutWrapping();

        if (config('services.lkm.use_mock')) {
            $this->app->bind(AuthService::class, MockAuthService::class);
        } else {
            $this->app->bind(AuthService::class, MockAuthService::class);
        }

        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }
}
