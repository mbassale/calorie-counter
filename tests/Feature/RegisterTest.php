<?php

namespace Tests\Feature;

use App\Role;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Tests\TestCase;
use RolesTableSeeder;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:install');
        $this->seed(RolesTableSeeder::class);
    }

    public function testRegister()
    {
        // execute api endpoint without registration, should return HTTP 401 Unauthorized
        $response = $this->json('GET', '/api/user');
        $response->assertStatus(401);

        $email = 'test@example.com';
        $password = 'password';
        $userData = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password
        ];

        // execute register endpoint, expect bearer token on result
        $response = $this->json('POST', '/api/register', $userData);
        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
        $responseData = $response->json();
        $accessToken = $responseData['token'];

        // with token, execute a simple api access
        $response = $this->withHeader('Authorization', "Bearer {$accessToken}")->json('GET', '/api/user');
        $response->assertStatus(200);
        $response->assertJson(Arr::except($userData, ['password', 'password_confirmation']));
    }

    public function testRegisterValidation()
    {
        $email = 'test@example.com';
        $password = 'password';
        $userData = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password . '1'
        ];

        // execute register endpoint, should return validation error, passwords mismatch
        $response = $this->json('POST', '/api/register', $userData);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => ['password']
        ]);

        // missing required data
        $userData = [
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password
        ];
        $response = $this->json('POST', '/api/register', $userData);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => ['first_name', 'last_name']
        ]);

        // empty request
        $response = $this->json('POST', '/api/register');
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors'
        ]);
    }
}
