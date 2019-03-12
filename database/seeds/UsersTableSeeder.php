<?php

use App\Role;
use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->name = 'Admin';
        $user->email = 'super@test.com';
        $user->password = app('hash')->make('secret');
        $user->verified = 1;
        $user->enabled = 1;
        $user->save();

        $user->roles()->save(Role::where('name', 'admin')->first());
    }
}
