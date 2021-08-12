<?php

namespace Database\Seeders;

use App\Models\EventsNotifications;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EventsNotificationsSeeder extends Seeder
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
                'key' => 'auth_failed',
                'label' => 'Авторизация',
                'msg_template' => "Неудачная авторизация\nIp: {ip}\nТелефон: {phone}",
            ],
            [
                'key' => 'password_reset_limit',
                'label' => 'Авторизация',
                'msg_template' => "Исчерпан лимит восстановления пароля в сутки по номеру - {phone}",
            ],
            [
                'key' => 'password_reset_success',
                'label' => 'Авторизация',
                'msg_template' => "Успешное восстановления пароля по номеру - {phone}",
            ],
            [
                'key' => 'position_created',
                'label' => 'Должности',
                'msg_template' => "Должность \"{name}\" успешно создана.",
            ],
            [
                'key' => 'position_remove_error',
                'label' => 'Должности',
                'msg_template' => "Попытка удалить должность \"{name}\" не удалась.",
            ],
            [
                'key' => 'position_removed',
                'label' => 'Должности',
                'msg_template' => "Должность \"{name}\" была удалена.",
            ],
            [
                'key' => 'position_updated',
                'label' => 'Должности',
                'msg_template' => "Должность \"{name}\" была изменена.",
            ],
            [
                'key' => 'setting_update_failed',
                'label' => 'Настройки',
                'msg_template' => "Произошла ошибка при изменении опции \"{name}\" в настройках.",
            ],
            [
                'key' => 'setting_updated',
                'label' => 'Настройки',
                'msg_template' => "Опция \"{name}\" была изменена.",
            ],
            [
                'key' => 'user_created',
                'label' => 'Пользователи',
                'msg_template' => "Пользователь \"{full_name} ({phone})\" успешно создан.",
            ],
            [
                'key' => 'user_removed',
                'label' => 'Пользователи',
                'msg_template' => "Пользователь \"{full_name} ({phone})\" был удален.",
            ],
            [
                'key' => 'user_updated',
                'label' => 'Пользователи',
                'msg_template' => "Пользователь \"{full_name} ({phone})\" был изменен.",
            ],
        ];

        foreach($array as $row) {
            EventsNotifications::updateOrCreate(
                [
                    'key' => $row['key']
                ],
                [
                    'key' => $row['key'],
                    'label' => $row['label'],
                    'msg_template' => $row['msg_template'],
                    'updated_at' => Carbon::now(),
                ]
            );
        }
    }
}
