<?php

namespace Tests\Feature;

use App\Meal;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Laravel\Passport\Passport;
use Tests\TestCase;
use RolesTableSeeder;

class MealsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    protected $adminUser;

    /**
     * @var User
     */
    protected $managerUser;

    /**
     * @var User
     */
    protected $normalUser1;

    /**
     * @var User
     */
    protected $normalUser2;

    /**
     * @var Collection
     */
    protected $meals;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesTableSeeder::class);
        $this->adminUser = factory(User::class)->state('admin')->create();
        $this->managerUser = factory(User::class)->state('manager')->create();
        $this->normalUser1 = factory(User::class)->create();
        $this->normalUser2 = factory(User::class)->create();
        $this->meals = factory(Meal::class, 10)->make();
        for ($i = 0; $i < 5; $i++) {
            $this->meals[$i]['user_id'] = $this->normalUser1->id;
            $this->meals[$i]->save();
        }
        for ($i = 5; $i < 10; $i++) {
            $this->meals[$i]['user_id'] = $this->normalUser2->id;
            $this->meals[$i]->save();
        }
    }

    public function testIndex()
    {
        // Admin permission, see all records
        Passport::actingAs($this->adminUser);
        $response = $this->json('GET', '/api/meals');
        $response->assertStatus(200);
        $response->assertJsonCount(Meal::query()->count());
        $response->assertJsonStructure([
            '*' => [
                'id',
                'user_id',
                'name',
                'date',
                'calories'
            ]
        ]);

        // Manager cannot see meals
        Passport::actingAs($this->managerUser);
        $response = $this->json('GET', '/api/meals');
        $response->assertStatus(403);

        // User can see own records
        Passport::actingAs($this->normalUser1);
        $response = $this->json('GET', '/api/meals');
        $response->assertStatus(200);
        $response->assertJsonCount(5);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'user_id',
                'name',
                'date',
                'calories'
            ]
        ]);
        // check if records are from calling user
        $mealData = $response->json();
        $this->assertNotEmpty($mealData);
        $this->assertCount(5, $mealData);
        foreach ($mealData as $mealDatum) {
            $this->assertEquals($this->normalUser1->id, $mealDatum['user_id']);
        }
    }

    public function testShow()
    {
        // admin user can CRUD all records
        Passport::actingAs($this->adminUser);
        $randomMeal = $this->meals->random();
        $response = $this->json('GET', "/api/meals/{$randomMeal->id}");
        $response->assertStatus(200);
        $response->assertJson($randomMeal->toArray());

        // manager cannot see meal
        Passport::actingAs($this->managerUser);
        $randomMeal = $this->meals->random();
        $response = $this->json('GET', "/api/meals/{$randomMeal->id}");
        $response->assertStatus(403);

        // user can see own records
        Passport::actingAs($this->normalUser1);
        $randomMealFromUser1 = $this->meals->filter(function ($meal) {
            return $meal->user_id === $this->normalUser1->id;
        })->random();
        $response = $this->json('GET', "/api/meals/{$randomMealFromUser1->id}");
        $response->assertStatus(200);
        $response->assertJson($randomMealFromUser1->toArray());

        // user cannot see records from other users
        Passport::actingAs($this->normalUser1);
        $randomMealFromUser2 = $this->meals->filter(function ($meal) {
            return $meal->user_id === $this->normalUser2->id;
        })->random();
        $response = $this->json('GET', "/api/meals/{$randomMealFromUser2->id}");
        $response->assertStatus(403);
    }

    public function testCreate()
    {
        // admin user can CRUD all records
        Passport::actingAs($this->adminUser);
        $meal = factory(Meal::class)->make();
        $mealData = Arr::only($meal->toArray(), ['user_id', 'name', 'date', 'calories']);
        $mealData['user_id'] = $this->adminUser->id;
        $response = $this->json('POST', '/api/meals', $mealData);
        $response->assertStatus(201);
        $response->assertJson($mealData);
        $this->assertDatabaseHas('meals', $mealData);

        // manager cannot create meals
        Passport::actingAs($this->managerUser);
        $meal = factory(Meal::class)->make();
        $mealData = Arr::only($meal->toArray(), ['user_id', 'name', 'date', 'calories']);
        $mealData['user_id'] = $this->adminUser->id;
        $response = $this->json('POST', '/api/meals', $mealData);
        $response->assertStatus(403);

        // user can create meals only for itself
        Passport::actingAs($this->normalUser1);
        $meal = factory(Meal::class)->make();
        $mealData = Arr::only($meal->toArray(), ['name', 'date', 'calories']);
        $mealData['user_id'] = $this->normalUser2->id; // should override normalUser1 id
        $response = $this->json('POST', '/api/meals', $mealData);
        $response->assertStatus(201);
        $mealData['user_id'] = $this->normalUser1->id;
        $response->assertJson($mealData);
        $this->assertDatabaseHas('meals', $mealData);
    }

    public function testUpdate()
    {
        // admin user can CRUD all records
        Passport::actingAs($this->adminUser);
        $randomMeal = $this->meals->random();
        $updatedData = Arr::only($randomMeal->toArray(), ['id', 'user_id', 'name', 'date', 'calories']);
        $updatedData['name'] = 'TEST NAME';
        $response = $this->json('PUT', "/api/meals/{$randomMeal->id}", $updatedData);
        $response->assertStatus(200);
        $response->assertJson($updatedData);
        $this->assertDatabaseHas('meals', $updatedData);

        // manager cannot CRUD meals
        Passport::actingAs($this->managerUser);
        $randomMeal = $this->meals->random();
        $updatedData = Arr::only($randomMeal->toArray(), ['id', 'user_id', 'name', 'date', 'calories']);
        $updatedData['name'] = 'TEST NAME';
        $response = $this->json('PUT', "/api/meals/{$randomMeal->id}", $updatedData);
        $response->assertStatus(403);

        // user can update only own records
        Passport::actingAs($this->normalUser1);
        $randomMealFromUser1 = $this->meals->filter(function ($meal) {
            return $meal->user_id === $this->normalUser1->id;
        })->random();
        $updatedData = Arr::only($randomMealFromUser1->toArray(), ['id', 'user_id', 'name', 'date', 'calories']);
        $updatedData['name'] = 'TEST NAME';
        $response = $this->json('PUT', "/api/meals/{$randomMealFromUser1->id}", $updatedData);
        $response->assertStatus(200);
        $response->assertJson($updatedData);
        $this->assertDatabaseHas('meals', $updatedData);

        // try to update other user record
        Passport::actingAs($this->normalUser1);
        $randomMealFromUser2 = $this->meals->filter(function ($meal) {
            return $meal->user_id === $this->normalUser2->id;
        })->random();
        $updatedData = Arr::only($randomMealFromUser2->toArray(), ['id', 'user_id', 'name', 'date', 'calories']);
        $updatedData['name'] = 'TEST NAME';
        $response = $this->json('PUT', "/api/meals/{$randomMealFromUser2->id}", $updatedData);
        $response->assertStatus(403);
    }

    public function testDestroy()
    {
        $removeDeletedMeals = function () {
            for ($i = 0; $i < $this->meals->count(); $i++) {
                if (!Meal::query()->where('id', $this->meals[$i]->id)->exists()) {
                    $this->meals->splice($i, 1);
                    $i = -1;
                }
            }
        };

        // admin user can CRUD all records
        Passport::actingAs($this->adminUser);
        $randomMeal = $this->meals->random();
        $response = $this->json('DELETE', "/api/meals/{$randomMeal->id}");
        $response->assertStatus(200);
        $this->assertFalse(Meal::query()->where('id', $randomMeal->id)->exists());
        $removeDeletedMeals();

        // manager cannot CRUD meals
        Passport::actingAs($this->managerUser);
        $randomMeal = $this->meals->random();
        $response = $this->json('DELETE', "/api/meals/{$randomMeal->id}");
        $response->assertStatus(403);
        $this->assertTrue(Meal::query()->where('id', $randomMeal->id)->exists());
        $removeDeletedMeals();

        // user can delete only own records
        Passport::actingAs($this->normalUser1);
        $randomMealFromUser1 = $this->meals->filter(function ($meal) {
            return $meal->user_id === $this->normalUser1->id;
        })->random();
        $response = $this->json('DELETE', "/api/meals/{$randomMealFromUser1->id}");
        $response->assertStatus(200);
        $this->assertFalse(Meal::query()->where('id', $randomMealFromUser1->id)->exists());
        $removeDeletedMeals();

        // try to delete other user record
        Passport::actingAs($this->normalUser1);
        $randomMealFromUser2 = $this->meals->filter(function ($meal) {
            return $meal->user_id === $this->normalUser2->id;
        })->random();
        $response = $this->json('DELETE', "/api/meals/{$randomMealFromUser2->id}");
        $response->assertStatus(403);
        $this->assertTrue(Meal::query()->where('id', $randomMealFromUser2->id)->exists());
        $removeDeletedMeals();
    }
}
