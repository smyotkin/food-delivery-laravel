<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $developer = Role::where('slug', 'web-developer')->first();
        $manager = Role::where('slug', 'project-manager')->first();
        $createTasks = Permission::where('slug', 'create-tasks')->first();
        $manageUsers = Permission::where('slug', 'manage-users')->first();

        $faker = \Faker\Factory::create();

        $user1 = User::create([
            'city_id' => random_int(1, 9),
            'position_id' => random_int(1, 9),
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'password' => Hash::make(123456),
            'phone' => $faker->numerify('79#########'),
            'is_active' => 1,
        ]);

        $user1->roles()->attach($developer);
        $user1->permissions()->attach($createTasks);

        $user2 = User::create([
            'city_id' => random_int(1, 9),
            'position_id' => random_int(1, 9),
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'password' => Hash::make(123456),
            'phone' => $faker->numerify('79#########'),
            'is_active' => 1,
        ]);

        $user2->roles()->attach($manager);
        $user2->permissions()->attach($manageUsers);
    }
}
