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
        $str_permissions = '
            Колл-центр	Просмотр (доступ к экрану) экрана Колл-центр	callcenter_view
            Колл-центр	Назначение курьера на заказ на экране КЦ	callcenter_courier_set
            Колл-центр	Переключение (вперед) статусов на экране КЦ	callcenter_status_change
            Колл-центр	Откат статусов на экране КЦ	callcenter_status_rollback
            Колл-центр	Перемещение заказа на другую точку на экране КЦ	callcenter_shop_change
            Колл-центр	Право открыть смену точки	callcenter_shift_open
            Колл-центр	Право закрыть смену точки	callcenter_shift_close
            Колл-центр	Создать новый заказ	callcenter_order_create
            Колл-центр	Отменить заказ	callcenter_order_cancel
            Колл-центр	Просмотр заказа в текущей смене	callcenter_order_view
            Колл-центр	Редактировать принятый заказ	callcenter_order_modify
            Колл-центр	Отправить гостю смс/пуш об опоздании	callcenter_fuckedup_send
            Колл-центр	Добавить второй тип оплаты	callcenter_paytype_split
            Колл-центр	Разблокировать заказ (если заказ кем-то занят, возможность выкинуть этого человека из заказа)	callcenter_detail_unlock

            Экран управляющего	Просмотр экрана управляющего	manager_view
            Экран управляющего	Настройка времени доставки/самовывоза по торговой точке	manager_timer_set
            Экран управляющего	Включение / отключение, доставки / самовывоза на торговой точке	manager_stop_set

            Кухня	Просмотр экрана кухни	kitchen_view
            Кухня	"Запись" экран кухни	kitchen_modify

            Сборка	Просмотр экрана сборки	packing_view
            Сборка	"Запись" экран сборки	packing_modify

            Доставка	Просмотр экрана доставки	delivery_view
            Доставка	"Запись" экрана доставки	delivery_modify

            CRM	Доступ к разделу CRM	crm_view
            CRM	Выгрузка списка гостей	crm_download
            CRM	Просмотр профиля гостя	crm_guest_view
            CRM	Редактирование профиля гостя	crm_guest_modify
            CRM / ЧС	Просмотр черного списка	crm_bl_view
            CRM / ЧС	Редактирование черного списка	crm_bl_modify
            CRM / Сообщения	Просмотр заданий рассылки	crm_messages_view
            CRM / Сообщения	Добавление заданий рассылки	crm_messages_add
            CRM / Сообщения	Редактирование заданий рассылки	crm_messages_modify
            CRM / Отзывы	Просмотр списка отзывов (доступ к разделу отзывы)	crm_reviews_view
            CRM / Отзывы	Добавить отзыв	crm_reviews_add
            CRM / Отзывы	Выгрузка списка отзывов	crm_reviews_download
            CRM / Отзывы	Просмотр отзыва	crm_reviews_detail_view
            CRM / Отзывы	Редактирование отзывов	crm_reviews_detail_modify
            CRM / Заказы	Просмотр списка заказов	crm_orders_view
            CRM / Заказы / Конкретный заказ	Просмотр заказа из прошлых смен	crm_orders_detail_view

            Меню	Просмотр 	menu_view
            Меню	Создание и редактирование блюд, групп и модификаторов	menu_modify
            Меню	Настройка приказов	menu_price_tasks
            Меню	Настройка стопов блюд	menu_stop_tasks

            Программа лояльности	Просмотр акций	loyalty_view
            Программа лояльности	Создание и редактирование акций	loyalty_modify
            Программа лояльности	Включение и выключение существующих акций	loyalty_activate

            Города	Просмотр	cities_view
            Города	Редактирование	cities_modify
            Города	Просмотр профиля доставки	cities_profiles_view
            Города	Редактирование профиля доставки	cities_profiles_modify

            Заведения	Просмотр	shops_view
            Заведения	Редактирование	shops_modify
            Заведения	Просмотр профиля доставки	shops_profiles_view
            Заведения	Редактирование профиля доставки	shops_profiles_modify

            Пользователи	Просмотр списка сотрудников	users_view
            Пользователи	Выгрузка списка сотрудников	users_download
            Пользователи	Редактирование прав у пользователей	users_modes_modify
            Пользователи	Создание новых сотрудников	users_employee_add
            Пользователи	Просмотр карточки сотрудника	users_employee_view
            Пользователи	Редактирование карточки сотрудника	users_employee_modify
            Пользователи	Удаление сотрудника	users_employee_delete
            Пользователи	Создание новых сотрудников	users_specialist_add
            Пользователи	Просмотр карточки сотрудника	users_specialist_view
            Пользователи	Редактирование карточки сотрудника	users_specialist_modify
            Пользователи	Удаление сотрудника	users_specialist_delete
            Пользователи	Создание новых сотрудников	users_head_add
            Пользователи	Просмотр карточки сотрудника	users_head_view
            Пользователи	Редактирование карточки сотрудника	users_head_modify
            Пользователи	Удаление сотрудника	users_head_delete
            Пользователи	Создание новых сотрудников	users_owner_add
            Пользователи	Просмотр карточки сотрудника	users_owner_view
            Пользователи	Редактирование карточки сотрудника	users_owner_modify
            Пользователи	Удаление сотрудника	users_owner_delete
            Пользователи / Должности	Создавать новые должности	users_position_create
            Пользователи / Должности	Просматривать права у текущих должностей	users_position_view
            Пользователи / Должности	Редактировать права у текущих должностей	users_position_modify

            Настройки	Просмотр	settings_view
            Настройки	Редактирование	settings_modify
            Настройки	Добавление адреса	settings_address_add

            Мониторинг	Просмотр мониторинга	monitoring_view
            Мониторинг	Просмотр незавершенных текущих заказов	monitoring_unsent_view
            Мониторинг	Очистка незавершенных текущих заказов	monitoring_unsent_clear
        ';

        $rows = explode("\n", $str_permissions);

        foreach($rows as $row) {
            $row = trim($row);

            if (!empty($row) && strpos($row, "\t") !== false) {
                list($group, $name, $slug) = explode("\t", trim($row));

                Permission::updateOrCreate(
                    [
                        'slug' => $slug
                    ],
                    [
                        'group' => $group,
                        'name' => $name,
                        'slug' => $slug,
                    ]
                );
            } else {
                continue;
            }
        }
    }
}
