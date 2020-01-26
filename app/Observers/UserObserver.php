<?php

namespace App\Observers;

use App\Services\MealServiceInterface;
use App\User;
use Carbon\Carbon;

class UserObserver
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
     * Handle the user "updated" event.
     *
     * @param User $user
     * @return void
     */
    public function updated(User $user)
    {
        if (in_array('calories_per_day', array_keys($user->getDirty()))) {
            $this->processCaloriesPerDay($user);
        }
    }

    /**
     * Set is_getting_fit flag to all user's meals
     * @param User $user
     */
    protected function processCaloriesPerDay(User $user)
    {
        if ($user->meals()->doesntExist()) return;
        $maxCaloriesPerDay = $user->calories_per_day;
        if (!$maxCaloriesPerDay) return;

        // we start at minimum date
        $currentDate = Carbon::parse($user->meals()->min('date'))->startOfDay();
        // there is a meal in current day?
        while ($user->meals()->where('meals.date', '>=', $currentDate)->exists()) {
            $meal = $user->meals()->where('meals.date', '>=', $currentDate)->orderBy('meals.date')->first();
            $this->mealService->processCaloriesPerDay($meal);
            // move to next day
            $currentDate = $currentDate->addDay()->startOfDay();
        }
    }
}
