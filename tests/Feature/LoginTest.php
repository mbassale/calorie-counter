<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\LoginController;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Tests\TestCase;
use RolesTableSeeder;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    protected $normalUser;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:install');
        $this->seed(RolesTableSeeder::class);
        $this->normalUser = factory(User::class)->create();
    }

    public function testLogin()
    {
        // execute api endpoint without login, should return HTTP 401 Unauthorized
        $response = $this->json('GET', '/api/user');
        $response->assertStatus(401);

        // execute login endpoint, expect bearer token on result
        $response = $this->json('POST', '/api/login', [
            'email' => $this->normalUser->email,
            'password' => 'password'
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
        $responseData = $response->json();
        $accessToken = $responseData['token'];

        // with token, execute a simple api access
        $response = $this->withHeader('Authorization', "Bearer {$accessToken}")->json('GET', '/api/user');
        $response->assertStatus(200);
        $response->assertJson([
            'first_name' => $this->normalUser->first_name,
            'last_name' => $this->normalUser->last_name,
            'email' => $this->normalUser->email
        ]);
    }
}
