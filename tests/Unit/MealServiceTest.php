<?php
namespace Tests\Unit;

use App\Meal;
use App\Services\MealService;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use RolesTableSeeder;

class MealServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    protected $normalUser;

    /**
     * @var Meal[]
     */
    protected $meals;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesTableSeeder::class);
        $this->normalUser = factory(User::class)->create();
        $this->meals = factory(Meal::class, 10)->make();
        for ($i = 0; $i < 10; $i++) {
            $this->meals[$i]['user_id'] = $this->normalUser->id;
            $this->meals[$i]->save();
        }
    }

    /**
     * Test correct is_getting_fit flag calculation.
     */
    public function testProcessCaloriesPerDay()
    {
        $mealService = new MealService();

        // minimum calories per day, is_getting_fit should be false
        $this->normalUser->calories_per_day = 1;
        $this->normalUser->save();

        // All meals same date.
        $mealDate = Carbon::now();
        Meal::query()->update(['date' => $mealDate]);

        // get a random meal
        $randomMeal = $this->meals->random();
        $randomMeal->refresh();

        // check if is_getting_fit is false|0
        $mealService->processCaloriesPerDay($randomMeal);
        $this->assertEquals(10, Meal::query()
            ->whereNotNull('is_getting_fit')
            ->where('is_getting_fit', 0)->count());

        // big number of calories per day, is_getting_fit should be true
        $this->normalUser->calories_per_day = 100000000;
        $this->normalUser->save();

        // get a random meal
        $randomMeal = $this->meals->random();
        $randomMeal->refresh();

        // check if is_getting_fit is true|1
        $mealService->processCaloriesPerDay($randomMeal);
        $this->assertEquals(10, Meal::query()
            ->whereNotNull('is_getting_fit')
            ->where('is_getting_fit', 1)->count());
    }
}
