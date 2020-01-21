<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Meal;
use Faker\Generator as Faker;

$mealNames = [
    'Clam Chowder' => 56,
    'Bagel and Lox' => 320,
    'Deep-Dish Pizza' => 420,
    'Drop Biscuits and Sausage Gravy' => 177,
    'Texas Barbecue' => 279,
    'Hominy Grits' => 59,
    'Tacos' => 226,
    'Key Lime Pie' => 359,
    'Tater Tots' => 160
];

$factory->define(Meal::class, function (Faker $faker) use ($mealNames) {
    $mealName = $faker->randomElement(array_keys($mealNames));
    return [
        'name' => $mealName,
        'date' => $faker->dateTimeBetween('-7 days'),
        'calories' => $mealNames[$mealName]
    ];
});
