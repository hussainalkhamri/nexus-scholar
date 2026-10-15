<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        Gate::define('view-reports', function ($user) {
            return in_array($user->role, ['super_admin', 'admin', 'projects_officer', 'alumni_officer']);
        });

        Gate::define('manage-users', function ($user) {
            return in_array($user->role, ['super_admin', 'admin']);
        });

        if (!function_exists('ar_fix')) {
            function ar_fix($text) {
                return \App\Helpers\ArabicReshaper::fix($text);
            }
        }
    }
}

