<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // User::factory(10)->create();

        $this->call(MenuSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(RefDestinationSeeder::class);
        $this->call(RefNationalitySeeder::class);
        $this->call(RefActivitySeeder::class);

        $this->call(ConfigSeeder::class);
        // $this->call(BoatLicenseSeeder::class);
    }
}
