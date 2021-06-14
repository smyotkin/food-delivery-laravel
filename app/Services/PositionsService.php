<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Role;

class PositionsService
{
    /**
     * Статусы(фильтр) должностей при создании пользователя
     */
    public const statuses = [
        'owner' => [
            'name' => 'Владелец',
        ],
        'head' => [
            'name' => 'Руководитель',
        ],
        'specialist' => [
            'name' => 'Специалист',
        ],
        'employee' => [
            'name' => 'Сотрудник',
        ],
    ];

    /**
     * Создание или редактирование(если указан id) должности
     *
     * @param array|null $array
     * @return Role
     * @throws \Throwable
     */
    public static function createOrUpdate(?array $array = null): Role
    {
        $basicParams = collect($array)
            ->only(['name', 'slug', 'status'])
            ->all();

        if ($role = Role::find($array['id'] ?? 0)) {
            $role->update($basicParams);
            $role->saveOrFail();

            DB::table('roles_permissions')->where('role_id', '=', $role->id)->delete();
            $role->givePermissionsArray($array['permissions']);

            Log::info("UPDATE_POSITION: id({$role->id})");
        } else {
            $role = Role::create($basicParams);

            DB::table('roles_permissions')->where('role_id', '=', $role->id)->delete();
            $role->givePermissionsArray($array['permissions']);

            Log::info("CREATE_NEW_POSITION(id: {$role->id}, name: {$role->name}, slug: {$role->slug})");
        }

        return $role;
    }

    /**
     * Метод поиска должностей
     *
     * @param array|null $array
     * @return mixed
     */
    public static function find(?array $array = null)
    {
        $users = Role::query()
            ->when(isset($array['query']), function ($query) use ($array) {
                $query
                    ->where('name', 'like', '%' . $array['query'] . '%')
                    ->orWhere('slug', 'like', '%' . $array['query'] . '%');
            })
            ->when(isset($array['status']), function ($query) use ($array) {
                $query
                    ->where('status', '=', $array['status']);
            })
            ->orderBy('created_at', 'desc');

        return $users->simplePaginate();
    }

    /**
     * Возвращает должность
     *
     * @param array $array
     * @return Role
     */
    public static function get(array $array): Role
    {
        return Role::find($array['id']);
    }

    /**
     * Возвращает должность или ошибку
     *
     * @param array $array
     * @return mixed
     */
    public static function getOrFail(array $array)
    {
        return Role::findOrFail($array['id']);
    }

    /**
     * Возвращает должность c правами
     *
     * @param array $array
     * @return Role
     */
    public static function getWithPermissions(array $array): Role
    {
        return Role::where('id', $array['id'])->with('permissions')->firstOrFail();
    }
}
