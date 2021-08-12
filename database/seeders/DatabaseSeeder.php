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
        $this->call(PermissionsSeeder::class);
        $this->call(RootUserSeeder::class);
        $this->call(ModulesSeeder::class);
        $this->call(SettingsSeeder::class);
        $this->call(EventsNotificationsSeeder::class);
    }
}
