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
            Просмотр экрана КЦ	callcenter_view
            Назначение курьера на заказ КЦ	callcenter_courier_set
            Переключение (вперед) статусов КЦ	callcenter_status_change
            Откат статусов КЦ	callcenter_status_rollback
            Перемещение заказа на другую точку КЦ	callcenter_shop_change
            Право открыть смену точки КЦ	callcenter_shift_open
            Право закрыть смену точки КЦ	callcenter_shift_close
            Создать новый заказ КЦ	callcenter_order_create
            Отменить заказ КЦ	callcenter_order_cancel
            Просмотр заказа в текущей смене КЦ	callcenter_order_view
            Редактировать принятый заказ КЦ	callcenter_order_modify
            Отправить гостю смс/пуш об опоздании КЦ	callcenter_fuckedup_send
            Добавить второй тип оплаты КЦ	callcenter_paytype_split
            Разблокировать заказ КЦ	callcenter_detail_unlock

            Просмотр экрана управляющего УПРАВЛЕНИЕ	manager_view
            Настройка времени доставки/самовывоза по торговой точке УПРАВЛЕНИЕ	manager_timer_set
            Включение/отключение, доставки/самовывоза на торговой точке УПРАВЛЕНИЕ	manager_stop_set

            Просмотр экрана КУХНЯ	kitchen_view
            "Запись" экран КУХНЯ	kitchen_modify

            Просмотр экрана СБОРКА	packing_view
            "Запись" экран СБОРКА	packing_modify

            Просмотр экрана ДОСТАВКА	delivery_view
            "Запись" экрана ДОСТАВКА	delivery_modify

            Доступ к разделу CRM CRM	crm_view
            Выгрузка списка гостей CRM	crm_download
            Просмотр профиля гостя CRM	crm_guest_view
            Редактирование профиля гостя CRM	crm_guest_modify
            Просмотр черного списка CRM/ЧС	crm_bl_view
            Редактирование черного списка CRM/ЧС	crm_bl_modify
            Просмотр заданий рассылки CRM/СООБЩЕНИЯ	crm_messages_view
            Добавление заданий рассылки CRM/СООБЩЕНИЯ	crm_messages_add
            Редактирование заданий рассылки CRM/СООБЩЕНИЯ	crm_messages_modify
            Просмотр списка отзывов CRM/ОТЗЫВЫ	crm_reviews_view
            Добавить отзыв CRM/ОТЗЫВЫ	crm_reviews_add
            Выгрузка списка отзывов CRM/ОТЗЫВЫ	crm_reviews_download
            Просмотр отзыва CRM/ОТЗЫВЫ	crm_reviews_detail_view
            Редактирование отзывов CRM/ОТЗЫВЫ	crm_reviews_detail_modify
            Просмотр списка заказов CRM/ЗАКАЗЫ	crm_orders_view
            Просмотр заказа из прошлых смен CRM/ЗАКАЗЫ	crm_orders_detail_view

            Просмотр МЕНЮ 	menu_view
            Создание и редактирование блюд, групп и модификаторов МЕНЮ	menu_modify
            Настройка приказов МЕНЮ	menu_price_tasks
            Настройка стопов блюд МЕНЮ	menu_stop_tasks

            Просмотр акций ПЛ	loyalty_view
            Создание и редактирование акций ПЛ	loyalty_modify
            Включение и выключение существующих акций ПЛ	loyalty_activate

            Просмотр ГОРОДА	cities_view
            Редактирование ГОРОДА	cities_modify
            Просмотр профиля доставки ГОРОДА	cities_profiles_view
            Редактирование профиля доставки ГОРОДА	cities_profiles_modify

            Просмотр ЗАВЕДЕНИЯ	shops_view
            Редактирование ЗАВЕДЕНИЯ	shops_modify
            Просмотр профиля доставки ЗАВЕДЕНИЯ	shops_profiles_view
            Редактирование профиля доставки ЗАВЕДЕНИЯ	shops_profiles_modify

            Просмотр списка сотрудников ПОЛЬЗОВАТЕЛИ	users_view
            Выгрузка списка сотрудников ПОЛЬЗОВАТЕЛИ	users_download
            Редактирование прав у пользователей ПОЛЬЗОВАТЕЛИ	users_modes_modify
            Создание новых сотрудников ПОЛЬЗОВАТЕЛИ	users_employee_add
            Просмотр карточки сотрудника ПОЛЬЗОВАТЕЛИ	users_employee_view
            Редактирование карточки сотрудника ПОЛЬЗОВАТЕЛИ	users_employee_modify
            Удаление сотрудника ПОЛЬЗОВАТЕЛИ	users_employee_delete
            Создание новых сотрудников ПОЛЬЗОВАТЕЛИ	users_specialist_add
            Просмотр карточки сотрудника ПОЛЬЗОВАТЕЛИ	users_specialist_view
            Редактирование карточки сотрудника ПОЛЬЗОВАТЕЛИ	users_specialist_modify
            Удаление сотрудника ПОЛЬЗОВАТЕЛИ	users_specialist_delete
            Создание новых сотрудников ПОЛЬЗОВАТЕЛИ	users_head_add
            Просмотр карточки сотрудника ПОЛЬЗОВАТЕЛИ	users_head_view
            Редактирование карточки сотрудника ПОЛЬЗОВАТЕЛИ	users_head_modify
            Удаление сотрудника ПОЛЬЗОВАТЕЛИ	users_head_delete
            Создание новых сотрудников ПОЛЬЗОВАТЕЛИ	users_owner_add
            Просмотр карточки сотрудника ПОЛЬЗОВАТЕЛИ	users_owner_view
            Редактирование карточки сотрудника ПОЛЬЗОВАТЕЛИ	users_owner_modify
            Удаление сотрудникаПОЛЬЗОВАТЕЛИ	users_owner_delete
            Создавать новые должности ПОЛЬЗОВАТЕЛИ/ДОЛЖНОСТИ	users_position_create
            Просматривать права у текущих должностей ПОЛЬЗОВАТЕЛИ/ДОЛЖНОСТИ	users_position_view
            Редактировать права у текущих должностей ПОЛЬЗОВАТЕЛИ/ДОЛЖНОСТИ	users_position_modify

            Просмотр НАСТРОЙКИ	settings_view
            Редактирование НАСТРОЙКИ	settings_modify
            Добавление адреса НАСТРОЙКИ	settings_address_add

            Просмотр МОНИТОРИНГ	monitoring_view
            Просмотр незавершенных текущих заказов МОНИТОРИНГ	monitoring_unsent_view
            Очистка незавершенных текущих заказов МОНИТОРИНГ	monitoring_unsent_clear
        ';

        $rows = explode("\n", $str_permissions);

        foreach($rows as $row) {
            $row = trim($row);

            if (!empty($row) && strpos($row, "\t") !== false) {
                list($name, $slug) = explode("\t", trim($row));

                Permission::updateOrCreate(
                    [
                        'slug' => $slug
                    ],
                    [
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
