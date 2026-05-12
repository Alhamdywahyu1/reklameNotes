<?php

namespace App\Providers;

use App\Events\SuratDiprintOlehOperator;
use App\Listeners\CreateNotificationSuratDiprint;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

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
        // Register event listeners
        Event::listen(
            SuratDiprintOlehOperator::class,
            CreateNotificationSuratDiprint::class,
        );
    }
}
