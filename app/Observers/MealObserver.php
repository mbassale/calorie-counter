<?php

namespace App\Observers;

use App\Meal;
use App\Services\MealServiceInterface;
use Carbon\Carbon;

class MealObserver
{
    /**
     * @var MealServiceInterface
     */
    protected $mealService;

    public function __construct(MealServiceInterface $mealService)
    {
        $this->mealService = $mealService;
    }

    /**
     * Handle the meal "created" event.
     *
     * @param  \App\Meal  $meal
     * @return void
     */
    public function created(Meal $meal)
    {
        $this->processCaloriesPerDay($meal);
    }

    /**
     * Handle the meal "updated" event.
     *
     * @param  \App\Meal  $meal
     * @return void
     */
    public function updated(Meal $meal)
    {
        $this->processCaloriesPerDay($meal);
    }

    /**
     * Handle the meal "deleted" event.
     *
     * @param  \App\Meal  $meal
     * @return void
     */
    public function deleted(Meal $meal)
    {
        $this->processCaloriesPerDay($meal);
    }

    /**
     * Set is_getting_fit flag to all meals of the day
     * @param Meal $meal
     */
    protected function processCaloriesPerDay(Meal $meal)
    {
        $mealUser = $meal->user;
        $maxCaloriesPerDay = $mealUser->calories_per_day;
        if ($maxCaloriesPerDay > 0) {
            $this->mealService->processCaloriesPerDay($meal);
        }
    }
}
