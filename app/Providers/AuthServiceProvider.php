<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use App\Policies\LocationPolicy;
use App\Policies\CountryPolicy;
use App\Policies\NationalityPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use App\Models\RefLocation;
use App\Models\RefCountry;
use App\Models\RefNationality;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        // RefLocation::class => LocationPolicy::class,
        // RefCountry::class => CountryPolicy::class,
        Role::class => RolePolicy::class,
        User::class => UserPolicy::class,
        RefNationality::class => NationalityPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //


        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        // Gate::before(function ($user, $ability) {
        //     return $user->hasRole('Super Admin') ? true : null;
        // });
    }
}
