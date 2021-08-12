<?php

namespace Database\Seeders;

use App\Models\Modules;
use Illuminate\Database\Seeder;

class ModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            [
                'slug' => 'callcenter',
                'name' => 'Колл-центр',
                'url' => 'javascript:',
                'is_control' => 1,
                'is_active' => 0,
            ],
            [
                'slug' => 'kitchen',
                'name' => 'Кухня',
                'url' => 'javascript:',
                'is_control' => 1,
                'is_active' => 0,
            ],
            [
                'slug' => 'packing',
                'name' => 'Сборка',
                'url' => 'javascript:',
                'is_control' => 1,
                'is_active' => 0,
            ],
            [
                'slug' => 'delivery',
                'name' => 'Доставка',
                'url' => 'javascript:',
                'is_control' => 1,
                'is_active' => 0,
            ],
            [
                'slug' => 'manager',
                'name' => 'Управляющий',
                'url' => 'javascript:',
                'is_control' => 1,
                'is_active' => 0,
            ],
            [
                'slug' => 'crm',
                'name' => 'CRM',
                'url' => 'javascript:',
                'is_control' => 0,
                'is_active' => 0,
            ],
            [
                'slug' => 'users',
                'name' => 'Пользователи',
                'url' => '/users',
                'is_control' => 0,
                'is_active' => 1,
            ],
            [
                'slug' => 'menu',
                'name' => 'Меню',
                'url' => 'javascript:',
                'is_control' => 0,
                'is_active' => 0,
            ],
            [
                'slug' => 'loyalty',
                'name' => 'Лояльность',
                'url' => 'javascript:',
                'is_control' => 0,
                'is_active' => 0,
            ],
            [
                'slug' => 'settings',
                'name' => 'Настройки',
                'url' => '/settings',
                'is_control' => 0,
                'is_active' => 1,
            ],
        ];

        foreach($array as $row) {
            Modules::updateOrCreate(
                [
                    'slug' => $row['slug']
                ],
                [
                    'slug' => $row['slug'],
                    'name' => $row['name'],
                    'url' => $row['url'],
                    'is_control' => $row['is_control'],
                    'is_active' => $row['is_active'],
                ]
            );
        }
    }
}
