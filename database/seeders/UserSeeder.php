<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
//use App\Services\UsersService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Throwable
     */
    public function run()
    {
        $faker = \Faker\Factory::create('ru_RU');

        $newRole = Role::firstOrCreate([
            'name' => 'test',
            'slug' => 'test',
            'status' => 'employee'
        ]);

        $newUser = User::create([
            'city_id' => 0,
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'phone' => $faker->numerify('79#########'),
            'is_custom_permissions' => 0,
            'timezone' => 'Europe/Moscow',
            'is_active' => 1,
            'password' => Hash::make(123456),
        ]);

        if ($newUser) {
            DB::table('users_roles')->insert([
                'user_id' => $newUser->id,
                'role_id' => $newRole->id,
            ]);

            $newUser->givePermissionsArray($permissionsParams['permissions'] ?? []);
        }
    }
}
