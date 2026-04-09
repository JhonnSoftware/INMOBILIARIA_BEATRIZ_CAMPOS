<?php

namespace App\Providers;

use App\Models\Proyecto;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
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
        Gate::define('manage-system-users', fn ($user) => $user->hasRole('dueno'));

        View::composer('layouts.admin-main', function ($view): void {
            $view->with('sidebarProjects', Proyecto::query()
                ->withCount('lotes')
                ->orderByDesc('orden_menu')
                ->orderByDesc('id')
                ->get());
        });
    }
}
