<?php

namespace App\Providers;

use App\Models\Dashboard;
use App\Models\Institution;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

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
        Inertia::share([
            'accessible_institutions' => function () {
                if (Auth::check()) {
                    $user = Auth::user();
                    if ($user->isSuperAdmin()) {
                        return Institution::orderBy('name')->get();
                    }
                    return $user->institutions()->orderBy('name')->get();
                }
                return [];
            },
            'accessible_dashboards' => function () {
                if (Auth::check()) {
                    $user = Auth::user();
                    if ($user->isSuperAdmin()) {
                        return Dashboard::orderBy('name')->with('institution')->get();
                    }
                    return $user->dashboards()->orderBy('name')->with('institution')->get();
                }
                return [];
            },
        ]);
    }
}
