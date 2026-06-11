<?php

namespace App\Providers;

use App\Services\CalorieCalculatorService;
use App\Services\AllergyFilterService;
use App\Services\MenuRecommendationService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CalorieCalculatorService::class);
        $this->app->singleton(AllergyFilterService::class);
        $this->app->singleton(MenuRecommendationService::class);
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Paginator::defaultView('vendor.pagination.tailwind');
        Paginator::defaultSimpleView('vendor.pagination.simple-tailwind');
    }
}