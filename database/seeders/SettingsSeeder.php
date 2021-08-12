<?php

namespace Database\Seeders;

use App\Models\Settings;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
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
                'key' => 'global_event_period',
                'name' => 'Период хранения системных событий',
                'value' => 'week',
            ],
            [
                'key' => 'global_max_rows_limit',
                'name' => 'Максимальный лимит записей',
                'value' => '1000',
            ],
            [
                'key' => 'global_rows_per_page',
                'name' => 'Количество записей на странице',
                'value' => '100',
            ],
            [
                'key' => 'smscru_login',
                'name' => 'Логин СМС-центра',
                'value' => '',
            ],
            [
                'key' => 'smscru_secret',
                'name' => 'Пароль СМС-центра',
                'value' => '',
            ],
            [
                'key' => 'telegram_token',
                'name' => 'Токен Telegram-бота',
                'value' => '',
            ],
        ];

        foreach($array as $row) {
            Settings::updateOrCreate(
                [
                    'key' => $row['key']
                ],
                [
                    'key' => $row['key'],
                    'name' => $row['name'],
                    'value' => $row['value'],
                ]
            );
        }
    }
}
