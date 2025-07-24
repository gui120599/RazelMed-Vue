<?php

namespace App\Providers;

use App\Models\User;
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
        // Define the 'manage-platform' Gate
        Gate::define('manage-platform', function (User $user) {
            return $user->is_super_admin;
        });

        // Implicitly grant "super admin" role access to all gates
        // This is a common pattern to ensure your Super Admin can do anything
        Gate::before(function (User $user, string $ability) {
            if ($user->is_super_admin) {
                return true;
            }
        });
    }
}
