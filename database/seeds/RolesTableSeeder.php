<?php

use Illuminate\Database\Seeder;
use App\Role;

class RolesTableSeeder extends Seeder
{
    static $roleSpecs = [
        ['id' => Role::ADMIN, 'name' => 'admin'],
        ['id' => Role::MANAGER, 'name' => 'manager'],
        ['id' => Role::USER, 'name' => 'user']
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::$roleSpecs as $roleSpec) {
            Role::updateOrCreate($roleSpec);
        }
    }
}
