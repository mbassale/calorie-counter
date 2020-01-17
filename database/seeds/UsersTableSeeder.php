<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    static $userSpecs = [
        [
            'id' => 1,
            'role_id' => Role::ADMIN,
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => 'toptal2020'
        ],
        [
            'id' => 2,
            'role_id' => Role::MANAGER,
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'toptal2020'
        ],
        [
            'id' => 3,
            'role_id' => Role::USER,
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
            'password' => 'toptal2020'
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::$userSpecs as $userSpec) {
            if (!User::where('id', $userSpec['id'])->exists()) {
                $userSpec['password'] = Hash::make($userSpec['password']);
                User::create($userSpec);
            }
        }
    }
}
