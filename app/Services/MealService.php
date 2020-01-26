<?php
namespace App\Services;


use App\Meal;
use Carbon\Carbon;

class MealService implements MealServiceInterface
{
    public function processCaloriesPerDay(Meal $meal)
    {
        $mealUser = $meal->user;
        $maxCaloriesPerDay = $mealUser->calories_per_day;
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
