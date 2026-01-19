<?php

namespace App\Providers;

use App\Models\Qaror;
use App\Observers\QarorObserver;
use Illuminate\Pagination\Paginator;
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
        Paginator::useBootstrap();

        // Register Qaror Observer for cache invalidation
        Qaror::observe(QarorObserver::class);
    }
}
