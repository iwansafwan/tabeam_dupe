<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::define('admin-only', function ($user) {
            return $user->roles->contains('name', 'admin');
        });

        Gate::define('treasurer-only', function ($user) {
            return $user->roles->contains('name', 'treasurer');
        });

        Gate::define('donator-only', function ($user) {
            return $user->roles->contains('name', 'donator');
        });
    }
}
