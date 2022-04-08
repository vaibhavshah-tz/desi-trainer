<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->call(UserSeeder::class);
        $this->call(TimezoneSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(TicketTypeSeeder::class);
        $this->call(EmailTemplateSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(PermissionRoleSeeder::class);
        $this->call(TimezoneAbbreviationSeeder::class);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
