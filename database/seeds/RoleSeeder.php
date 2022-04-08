<?php

use App\Models\Role;
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
        Role::truncate();
        $roles = [
            [
                'name' => 'Super Admin'
            ],
            [
                'name' => 'Sub Admin'
            ]
        ];
        Role::insert($roles);
    }
}
