<?php

namespace App\Providers;

use App\Meal;
use App\Observers\MealObserver;
use App\Observers\UserObserver;
use App\Services\MealService;
use App\Services\MealServiceInterface;
use App\User;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public $bindings = [
        MealServiceInterface::class => MealService::class
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        Meal::observe(MealObserver::class);
    }
}
