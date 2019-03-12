<?php

use App\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_user = new Role();
        $role_user->name = 'admin';
        $role_user->description = 'web admin';
        $role_user->save();

        $role_user = new Role();
        $role_user->name = 'user';
        $role_user->description = 'user';
        $role_user->save();

        $role_user = new Role();
        $role_user->name = 'artist';
        $role_user->description = 'verified artist';
        $role_user->save();
    }
}
