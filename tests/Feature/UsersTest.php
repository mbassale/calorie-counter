<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\UsersController;
use App\Role;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Laravel\Passport\Passport;
use Tests\TestCase;
use RolesTableSeeder;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Collection
     */
    protected $normalUsers;

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
    protected $normalUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesTableSeeder::class);
        $this->adminUser = factory(User::class)->state('admin')->create();
        $this->managerUser = factory(User::class)->state('manager')->create();
        $this->normalUsers = factory(User::class, 9)->create();
        $this->normalUser = factory(User::class)->create();
    }

    public function testIndex()
    {
        // Admin Permission
        Passport::actingAs($this->adminUser);
        $response = $this->get('/api/users');
        $response->assertStatus(200);
        $response->assertJsonCount(12);
        $responseData = $response->json();
        $this->assertNotEmpty($responseData);
        foreach ($responseData as $responseDatum) {
            $this->assertArrayHasKey('id', $responseDatum);
            $this->assertArrayHasKey('first_name', $responseDatum);
            $this->assertArrayHasKey('last_name', $responseDatum);
            $this->assertArrayHasKey('email', $responseDatum);
            $this->assertArrayNotHasKey('password', $responseDatum);
        }

        // Manager Permission
        Passport::actingAs($this->managerUser);
        $response = $this->get('/api/users');
        $response->assertStatus(200);
        $response->assertJsonCount(12);
        $responseData = $response->json();
        $this->assertNotEmpty($responseData);
        foreach ($responseData as $responseDatum) {
            $this->assertArrayHasKey('id', $responseDatum);
            $this->assertArrayHasKey('first_name', $responseDatum);
            $this->assertArrayHasKey('last_name', $responseDatum);
            $this->assertArrayHasKey('email', $responseDatum);
            $this->assertArrayNotHasKey('password', $responseDatum);
        }

        // Normal users HTTP 403 Forbidden
        Passport::actingAs($this->normalUser);
        $response = $this->get('/api/users');
        $response->assertStatus(403);
    }

    public function testShow()
    {
        // Admin Permission
        Passport::actingAs($this->adminUser);
        // Own user data
        $response = $this->get('/api/user');
        $response->assertStatus(200);
        $response->assertJson([
            'role_id' => $this->adminUser->role_id,
            'id' => $this->adminUser->id,
            'first_name' => $this->adminUser->first_name,
            'last_name' => $this->adminUser->last_name,
            'email' => $this->adminUser->email
        ]);
        // Other user data
        $randomUser = $this->normalUsers->random();
        $this->assertNotNull($randomUser);
        $response = $this->get('/api/users/' . $randomUser->id);
        $response->assertStatus(200);
        $response->assertJson([
            'role_id' => $randomUser->role_id,
            'id' => $randomUser->id,
            'first_name' => $randomUser->first_name,
            'last_name' => $randomUser->last_name,
            'email' => $randomUser->email
        ]);

        // Manager Permission
        Passport::actingAs($this->managerUser);
        // Own user data
        $response = $this->get('/api/user');
        $response->assertStatus(200);
        $response->assertJson([
            'role_id' => $this->managerUser->role_id,
            'id' => $this->managerUser->id,
            'first_name' => $this->managerUser->first_name,
            'last_name' => $this->managerUser->last_name,
            'email' => $this->managerUser->email
        ]);
        // Other user data
        $randomUser = $this->normalUsers->random();
        $this->assertNotNull($randomUser);
        $response = $this->get('/api/users/' . $randomUser->id);
        $response->assertStatus(200);
        $response->assertJson([
            'role_id' => $randomUser->role_id,
            'id' => $randomUser->id,
            'first_name' => $randomUser->first_name,
            'last_name' => $randomUser->last_name,
            'email' => $randomUser->email
        ]);

        // Normal User
        Passport::actingAs($this->normalUser);
        // Own data
        $response = $this->get('/api/user');
        $response->assertStatus(200);
        $response->assertJson([
            'role_id' => $this->normalUser->role_id,
            'id' => $this->normalUser->id,
            'first_name' => $this->normalUser->first_name,
            'last_name' => $this->normalUser->last_name,
            'email' => $this->normalUser->email
        ]);
        // Other users data, it should return HTTP 403 Forbidden
        $randomUser = $this->normalUsers->random();
        $response = $this->get('/api/users/' . $randomUser->id);
        $response->assertStatus(403);
    }

    public function testUpdate()
    {
        // Admin Permission
        Passport::actingAs($this->adminUser);
        // Update random user
        $randomUser = $this->normalUsers->random();
        $updatedData = [
            'role_id' => Role::MANAGER,
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@test.com'
        ];
        $response = $this->put('/api/users/' . $randomUser->id, $updatedData);
        $response->assertStatus(200);
        $response->assertJson(array_merge(['id' => $randomUser->id], $updatedData));

        // Manager Permission
        Passport::actingAs($this->managerUser);
        // Update random user
        $randomUser = $this->normalUsers->random();
        $updatedData = [
            'first_name' => 'test2',
            'last_name' => 'test2',
            'email' => 'test2@test.com'
        ];
        $response = $this->put('/api/users/' . $randomUser->id, $updatedData);
        $response->assertStatus(200);
        $response->assertJson(array_merge(['id' => $randomUser->id], $updatedData));

        // Normal User
        Passport::actingAs($this->normalUser);
        // Cannot random user, should return 403 Forbidden
        $randomUser = $this->normalUsers->random();
        $updatedData = [
            'first_name' => 'test3',
            'last_name' => 'test3',
            'email' => 'test3@test.com'
        ];
        $response = $this->put('/api/users/' . $randomUser->id, $updatedData);
        $response->assertStatus(403);
        // Update own data, should return HTTP OK
        $updatedData = [
            'first_name' => 'test3',
            'last_name' => 'test3',
            'email' => 'test3@test.com'
        ];
        $response = $this->put('/api/users/' . $this->normalUser->id, $updatedData);
        $response->assertStatus(200);
        $response->assertJson(array_merge(['id' => $this->normalUser->id], $updatedData));
    }

    public function testDestroy()
    {
        // Admin Permission
        Passport::actingAs($this->adminUser);
        // Delete random user
        $randomUser = $this->normalUsers->random();
        $response = $this->delete('/api/users/' . $randomUser->id);
        $response->assertStatus(200);
        $response->assertJson([
            'role_id' => $randomUser->role_id,
            'id' => $randomUser->id,
            'first_name' => $randomUser->first_name,
            'last_name' => $randomUser->last_name,
            'email' => $randomUser->email
        ]);
        // remove deleted user from collection
        $this->normalUsers = $this->normalUsers->filter(function ($normalUser) use ($randomUser) {
            return $normalUser->id != $randomUser->id;
        });

        // Manager Permission
        Passport::actingAs($this->managerUser);
        $randomUser = $this->normalUsers->random();
        $response = $this->delete('/api/users/' . $randomUser->id);
        $response->assertStatus(200);
        $response->assertJson([
            'role_id' => $randomUser->role_id,
            'id' => $randomUser->id,
            'first_name' => $randomUser->first_name,
            'last_name' => $randomUser->last_name,
            'email' => $randomUser->email
        ]);
        // remove deleted user from collection
        $this->normalUsers = $this->normalUsers->filter(function ($normalUser) use ($randomUser) {
            return $normalUser->id != $randomUser->id;
        });

        // Normal User, 403 Forbidden
        Passport::actingAs($this->normalUser);
        $randomUser = $this->normalUsers->random();
        $response = $this->delete('/api/users/' . $randomUser->id);
        $response->assertStatus(403);
    }
}
