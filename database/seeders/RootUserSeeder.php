<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RootUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        $allPermissions = Permission::all();
        $user = User::find(1);

        if (!$user) {
            $user = User::create([
                'id' => 1,
                'city_id' => 0,
                'first_name' => 'Главный',
                'last_name' => 'Админ',
                'password' => Hash::make(123456),
                'phone' => '79112223344',
                'timezone' => 'Europe/Moscow',
                'is_active' => 1,
                'is_custom_permissions' => 1,
            ]);
        }

        $user->permissions()->syncWithoutDetaching($allPermissions);
    }
}
