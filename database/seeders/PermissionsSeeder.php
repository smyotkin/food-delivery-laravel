<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionsSeeder extends Seeder
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
                'name' => 'Просмотр экрана КЦ',
                'slug' => 'callcenter_view',
                'group' => 'Колл-центр',
            ],
            [
                'name' => 'Назначение курьера на заказ на экране КЦ',
                'slug' => 'callcenter_courier_set',
                'group' => 'Колл-центр',
            ],
            [
                'name' => 'Переключение (вперед) статусов на экране КЦ',
                'slug' => 'callcenter_status_change',
                'group' => 'Колл-центр',
            ],
            [
                'name' => 'Откат статусов на экране КЦ',
                'slug' => 'callcenter_status_rollback',
                'group' => 'Колл-центр',
            ],
            [
                'name' => 'Перемещение заказа на другую точку на экране КЦ',
                'slug' => 'callcenter_shop_change',
                'group' => 'Колл-центр',
            ],
            [
                'name' => 'Право открыть смену точки',
                'slug' => 'callcenter_shift_open',
                'group' => 'Колл-центр',
            ],
            [
                'name' => 'Право закрыть смену точки',
                'slug' => 'callcenter_shift_close',
                'group' => 'Колл-центр',
            ],
            [
                'name' => 'Создать новый заказ',
                'slug' => 'callcenter_order_create',
                'group' => 'Колл-центр',
            ],
            [
                'name' => 'Отменить заказ',
                'slug' => 'callcenter_order_cancel',
                'group' => 'Колл-центр',
            ],
            [
                'name' => 'Просмотр заказа в текущей смене',
                'slug' => 'callcenter_order_view',
                'group' => 'Колл-центр',
            ],
            [
                'name' => 'Редактировать принятый заказ',
                'slug' => 'callcenter_order_modify',
                'group' => 'Колл-центр',
            ],
            [
                'name' => 'Отправить гостю смс/пуш об опоздании',
                'slug' => 'callcenter_fuckedup_send',
                'group' => 'Колл-центр',
            ],
            [
                'name' => 'Добавить второй тип оплаты',
                'slug' => 'callcenter_paytype_split',
                'group' => 'Колл-центр',
            ],
            [
                'name' => 'Разблокировать заказ (если заказ кем-то занят, возможность выкинуть этого человека из заказа)',
                'slug' => 'callcenter_detail_unlock',
                'group' => 'Колл-центр',
            ],
            [
                'name' => 'Просмотр экрана управляющего',
                'slug' => 'manager_view',
                'group' => 'Экран управляющего',
            ],
            [
                'name' => 'Настройка времени доставки/самовывоза по торговой точке',
                'slug' => 'manager_timer_set',
                'group' => 'Экран управляющего',
            ],
            [
                'name' => 'Включение / отключение, доставки / самовывоза на торговой точке',
                'slug' => 'manager_stop_set',
                'group' => 'Экран управляющего',
            ],
            [
                'name' => 'Просмотр экрана кухни',
                'slug' => 'kitchen_view',
                'group' => 'Кухня',
            ],
            [
                'name' => '"Запись" экран кухни',
                'slug' => 'kitchen_modify',
                'group' => 'Кухня',
            ],
            [
                'name' => 'Просмотр экрана сборки',
                'slug' => 'packing_view',
                'group' => 'Сборка',
            ],
            [
                'name' => '"Запись" экран сборки',
                'slug' => 'packing_modify',
                'group' => 'Сборка',
            ],
            [
                'name' => 'Просмотр экрана доставки',
                'slug' => 'delivery_view',
                'group' => 'Доставка',
            ],
            [
                'name' => '"Запись" экрана доставки',
                'slug' => 'delivery_modify',
                'group' => 'Доставка',
            ],
            [
                'name' => 'Доступ к разделу CRM',
                'slug' => 'crm_view',
                'group' => 'CRM',
            ],
            [
                'name' => 'Выгрузка списка гостей',
                'slug' => 'crm_download',
                'group' => 'CRM',
            ],
            [
                'name' => 'Просмотр профиля гостя',
                'slug' => 'crm_guest_view',
                'group' => 'CRM',
            ],
            [
                'name' => 'Редактирование профиля гостя',
                'slug' => 'crm_guest_modify',
                'group' => 'CRM',
            ],
            [
                'name' => 'Просмотр черного списка',
                'slug' => 'crm_bl_view',
                'group' => 'CRM / ЧС',
            ],
            [
                'name' => 'Редактирование черного списка',
                'slug' => 'crm_bl_modify',
                'group' => 'CRM / ЧС',
            ],
            [
                'name' => 'Просмотр заданий рассылки',
                'slug' => 'crm_messages_view',
                'group' => 'CRM / Сообщения',
            ],
            [
                'name' => 'Добавление заданий рассылки',
                'slug' => 'crm_messages_add',
                'group' => 'CRM / Сообщения',
            ],
            [
                'name' => 'Редактирование заданий рассылки',
                'slug' => 'crm_messages_modify',
                'group' => 'CRM / Сообщения',
            ],
            [
                'name' => 'Просмотр списка отзывов (доступ к разделу отзывы)',
                'slug' => 'crm_reviews_view',
                'group' => 'CRM / Отзывы',
            ],
            [
                'name' => 'Добавить отзыв',
                'slug' => 'crm_reviews_add',
                'group' => 'CRM / Отзывы',
            ],
            [
                'name' => 'Выгрузка списка отзывов',
                'slug' => 'crm_reviews_download',
                'group' => 'CRM / Отзывы',
            ],
            [
                'name' => 'Просмотр отзыва',
                'slug' => 'crm_reviews_detail_view',
                'group' => 'CRM / Отзывы',
            ],
            [
                'name' => 'Редактирование отзывов',
                'slug' => 'crm_reviews_detail_modify',
                'group' => 'CRM / Отзывы',
            ],
            [
                'name' => 'Просмотр списка заказов',
                'slug' => 'crm_orders_view',
                'group' => 'CRM / Заказы',
            ],
            [
                'name' => 'Просмотр заказа из прошлых смен',
                'slug' => 'crm_orders_detail_view',
                'group' => 'CRM / Заказы / Конкретный заказ',
            ],
            [
                'name' => 'Просмотр ',
                'slug' => 'menu_view',
                'group' => 'Меню',
            ],
            [
                'name' => 'Создание и редактирование блюд, групп и модификаторов',
                'slug' => 'menu_modify',
                'group' => 'Меню',
            ],
            [
                'name' => 'Настройка приказов',
                'slug' => 'menu_price_tasks',
                'group' => 'Меню',
            ],
            [
                'name' => 'Настройка стопов блюд',
                'slug' => 'menu_stop_tasks',
                'group' => 'Меню',
            ],
            [
                'name' => 'Просмотр акций',
                'slug' => 'loyalty_view',
                'group' => 'Программа лояльности',
            ],
            [
                'name' => 'Создание и редактирование акций',
                'slug' => 'loyalty_modify',
                'group' => 'Программа лояльности',
            ],
            [
                'name' => 'Включение и выключение существующих акций',
                'slug' => 'loyalty_activate',
                'group' => 'Программа лояльности',
            ],
            [
                'name' => 'Просмотр',
                'slug' => 'cities_view',
                'group' => 'Города',
            ],
            [
                'name' => 'Редактирование',
                'slug' => 'cities_modify',
                'group' => 'Города',
            ],
            [
                'name' => 'Просмотр профиля доставки',
                'slug' => 'cities_profiles_view',
                'group' => 'Города',
            ],
            [
                'name' => 'Редактирование профиля доставки',
                'slug' => 'cities_profiles_modify',
                'group' => 'Города',
            ],
            [
                'name' => 'Просмотр',
                'slug' => 'stores_view',
                'group' => 'Заведения',
            ],
            [
                'name' => 'Редактирование',
                'slug' => 'stores_modify',
                'group' => 'Заведения',
            ],
            [
                'name' => 'Просмотр профиля доставки',
                'slug' => 'stores_profiles_view',
                'group' => 'Заведения',
            ],
            [
                'name' => 'Редактирование профиля доставки',
                'slug' => 'stores_profiles_modify',
                'group' => 'Заведения',
            ],
            [
                'name' => 'Просмотр списка пользователей',
                'slug' => 'users_view',
                'group' => 'Пользователи',
            ],
            [
                'name' => 'Выгрузка списка пользователей',
                'slug' => 'users_download',
                'group' => 'Пользователи',
            ],
            [
                'name' => 'Редактирование прав пользователей',
                'slug' => 'users_modes_modify',
                'group' => 'Пользователи',
            ],
            [
                'name' => 'Создание нового сотрудника',
                'slug' => 'users_employee_add',
                'group' => 'Пользователи',
            ],
            [
                'name' => 'Просмотр карточки сотрудника',
                'slug' => 'users_employee_view',
                'group' => 'Пользователи',
            ],
            [
                'name' => 'Редактирование карточки сотрудника',
                'slug' => 'users_employee_modify',
                'group' => 'Пользователи',
            ],
            [
                'name' => 'Удаление сотрудника',
                'slug' => 'users_employee_delete',
                'group' => 'Пользователи',
            ],
            [
                'name' => 'Создание нового специалиста',
                'slug' => 'users_specialist_add',
                'group' => 'Пользователи',
            ],
            [
                'name' => 'Просмотр карточки специалиста',
                'slug' => 'users_specialist_view',
                'group' => 'Пользователи',
            ],
            [
                'name' => 'Редактирование карточки специалиста',
                'slug' => 'users_specialist_modify',
                'group' => 'Пользователи',
            ],
            [
                'name' => 'Удаление специалиста',
                'slug' => 'users_specialist_delete',
                'group' => 'Пользователи',
            ],
            [
                'name' => 'Создание нового руководителя',
                'slug' => 'users_head_add',
                'group' => 'Пользователи',
            ],
            [
                'name' => 'Просмотр карточки руководителя',
                'slug' => 'users_head_view',
                'group' => 'Пользователи',
            ],
            [
                'name' => 'Редактирование карточки руководителя',
                'slug' => 'users_head_modify',
                'group' => 'Пользователи',
            ],
            [
                'name' => 'Удаление руководителя',
                'slug' => 'users_head_delete',
                'group' => 'Пользователи',
            ],
            [
                'name' => 'Создание нового владельца',
                'slug' => 'users_owner_add',
                'group' => 'Пользователи',
            ],
            [
                'name' => 'Просмотр карточки владельца',
                'slug' => 'users_owner_view',
                'group' => 'Пользователи',
            ],
            [
                'name' => 'Редактирование карточки владельца',
                'slug' => 'users_owner_modify',
                'group' => 'Пользователи',
            ],
            [
                'name' => 'Удаление владельца',
                'slug' => 'users_owner_delete',
                'group' => 'Пользователи',
            ],
            [
                'name' => 'Создание новой должности',
                'slug' => 'users_position_create',
                'group' => 'Пользователи / Должности',
            ],
            [
                'name' => 'Просмотр списка должностей',
                'slug' => 'users_positions_view',
                'group' => 'Пользователи / Должности',
            ],
            [
                'name' => 'Просмотр карточки должности',
                'slug' => 'users_position_view',
                'group' => 'Пользователи / Должности',
            ],
            [
                'name' => 'Редактирование карточки должности',
                'slug' => 'users_position_modify',
                'group' => 'Пользователи / Должности',
            ],
            [
                'name' => 'Просмотр настроек',
                'slug' => 'settings_view',
                'group' => 'Настройки',
            ],
            [
                'name' => 'Редактирование настроек',
                'slug' => 'settings_modify',
                'group' => 'Настройки',
            ],
            [
                'name' => 'Добавление адреса в настройках',
                'slug' => 'settings_address_add',
                'group' => 'Настройки',
            ],
            [
                'name' => 'Просмотр мониторинга',
                'slug' => 'monitoring_view',
                'group' => 'Мониторинг',
            ],
            [
                'name' => 'Просмотр незавершенных текущих заказов',
                'slug' => 'monitoring_unsent_view',
                'group' => 'Мониторинг',
            ],
            [
                'name' => 'Очистка незавершенных текущих заказов',
                'slug' => 'monitoring_unsent_clear',
                'group' => 'Мониторинг',
            ],
            [
                'name' => 'Удаление должности',
                'slug' => 'users_position_delete',
                'group' => 'Пользователи / Должности',
            ],
        ];

        foreach($array as $row) {
            Permission::updateOrCreate(
                [
                    'slug' => $row['slug']
                ],
                [
                    'group' => $row['group'],
                    'name' => $row['name'],
                    'slug' => $row['slug'],
                ]
            );
        }
    }
}
