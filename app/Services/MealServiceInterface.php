<?php
namespace App\Services;

use App\Meal;

interface MealServiceInterface
{
    function processCaloriesPerDay(Meal $meal);
}
