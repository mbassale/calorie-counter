<?php

namespace Tests\Browser;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use RolesTableSeeder;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesTableSeeder::class);
        Artisan::call('passport:install');
    }

    /**
     * A basic browser test example.
     *
     * @return void
     * @throws \Throwable
     */
    public function testLoginExample()
    {
        $user = factory(User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/')
                    ->assertSee('Welcome Back!');

            $browser->type('input[type="email"]', $user->email)
                ->type('input[type="password"]', 'password')
                ->press('button.btn.btn-primary')
                ->waitForText('Meals');
        });
    }
}
