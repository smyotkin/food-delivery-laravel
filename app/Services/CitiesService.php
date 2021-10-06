<?php

namespace App\Services;

use App\Models\Cities;
use App\Models\CitiesSettings;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Throwable;

class CitiesService
{
    public const timeShift = [
        -30,
        -25,
        -20,
        -15,
        -10,
        -5,
        0,
        5,
        10,
        15,
        20,
        25,
        30
    ];

    /**
     * Метод поиска должностей
     *
     * @param array|null $array
     * @param bool $paginate
     * @return mixed
     */
    public static function find(?array $array = null, bool $paginate = true)
    {
        $cities = Cities::query()
            ->when(isset($array['query']), function ($query) use ($array) {
                $query
                    ->where('name', 'like', '%' . $array['query'] . '%')
                    ->orWhere('phone', 'like', '%' . $array['query'] . '%');
            })
            ->orderBy('id', 'desc');

        return $paginate ? $cities->simplePaginate(Settings::get('global_rows_per_page')) : $cities->get();
    }

    /**
     * Возвращает город
     *
     * @param array $array
     * @return Cities|null
     */
    public static function get(array $array): ?Cities
    {
        self::checkPermission('view');

        return Cities::find($array['id']);
    }

    /**
     * Возвращает одного пользователя или ошибку
     *
     * @param int $id
     * @return Cities
     */
    public static function getOrFail(int $id): Cities
    {
        self::checkPermission('view');

        return Cities::findOrFail($id);
    }

    /**
     * Проверяет права на действие(action) у авторизированного пользователя
     *
     * @param $action
     * @return void
     */
    public static function checkPermission($action): void
    {
        $permission = "cities_$action";

        if (!User::isRoot() && !Auth::user()->hasPermission($permission)) {
            abort(403, "Нет права: $permission");
        }
    }

    /**
     * Создание или обновление(если указан id) города
     *
     * @param array|null $array
     * @throws Throwable
     */
    public static function createOrUpdate(?array $array = null): void
    {
        $params = collect($array)->only([
            'id',
            'name',
            'phone',
            'timezone',
            'working_hours_shift',
            'working_hours',
            'is_active',
        ]);
        $existingModel = Cities::find($id = $params->get('id', 0));

        self::checkPermission('modify');

        $result = Cities::updateOrCreate(
            ['id' => $id],
            $params->all()
        );

        $settings = collect($array)->only([
            'phone_code',
            'multicode',
            'kladr_cities',
            'delivery_price',
            'free_delivery',
            'minimum_order',
        ]);

        $changeSettings = CitiesSettings::updateOrCreate(
            ['city_id' => $id],
            $settings->all()
        );

//        dd(Cities::find(1));

//        SystemService::createEvent(
//            $role->wasChanged() ? 'position_updated' : 'position_created',
//            $role->wasChanged() ? $existingModel->toArray() : $role->toArray(),
//            $role->toArray()
//        );
    }

    public static function getKladrByCityId(int $city_id)
    {
        return CitiesSettings::where('city_id', $city_id)->where('name', 'kladr_cities')->first();
    }
}
