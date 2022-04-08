<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        User::create([
            'role_id' => 1,
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'super-admin@desitrainer.com',
            'password' => '123456',
            'country_code' => '+91',
            'phone_number' => '9876511111'
        ]);
    }
}
