<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

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
        Blade::directive('canAccess', function ($permission) {
            return "<?php if(auth()->check() && auth()->user()->canAccess($permission)): ?>";
        });

        Blade::directive('endcanAccess', function () {
            return "<?php endif; ?>";
        });
    }
}
