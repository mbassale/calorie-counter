<?php

namespace App\Observers;

use App\Meal;
use Carbon\Carbon;

class MealObserver
{
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
        if ($maxCaloriesPerDay >= 0) {
            $mealDate = Carbon::instance($meal->date);
            $startDate = $mealDate->copy()->startOfDay();
            $endDate = $mealDate->copy()->endOfDay();
            $mealsOfDay = Meal::query()
                ->where('user_id', $mealUser->id)
                ->where('date', '>=', $startDate)
                ->where('date', '<=', $endDate);
            $caloriesOfDay = with(clone $mealsOfDay)->sum('calories');
            $isGettingFit = $caloriesOfDay <= $maxCaloriesPerDay;
            with(clone $mealsOfDay)->update(['is_getting_fit' => $isGettingFit]);
        }
    }
}
