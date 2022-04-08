<?php

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::truncate();
        $roles = [
            [
                'name' => 'sub-admin-list'
            ],
            [
                'name' => 'create-sub-admin'
            ],
            [
                'name' => 'edit-sub-admin'
            ],
            [
                'name' => 'view-sub-admin'
            ],
            [
                'name' => 'delete-sub-admin'
            ]
        ];
        Permission::insert($roles);
    }
}
