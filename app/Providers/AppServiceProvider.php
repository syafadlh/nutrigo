<?php
namespace App\Providers;

use App\Services\CalorieCalculatorService;
use App\Services\AllergyFilterService;
use App\Services\MenuRecommendationService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    
    public function register(): void {
        $this->app->singleton(CalorieCalculatorService::class);
        $this->app->singleton(AllergyFilterService::class);
        $this->app->singleton(MenuRecommendationService::class);
    }

    public function boot(): void {
        Paginator::defaultView('vendor.pagination.tailwind');
        Paginator::defaultSimpleView('vendor.pagination.simple-tailwind');
    }
}