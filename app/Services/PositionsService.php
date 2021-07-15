<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
            self::checkPermission('modify');

            $role->update($basicParams);
            $role->saveOrFail();

            DB::table('roles_permissions')->where('role_id', '=', $role->id)->delete();
            $role->givePermissionsArray($array['permissions'] ?? []);

            Log::info("UPDATE_POSITION: id({$role->id})");
        } else {
            self::checkPermission('create');

            $role = Role::create($basicParams);

            DB::table('roles_permissions')->where('role_id', '=', $role->id)->delete();
            $role->givePermissionsArray($array['permissions'] ?? []);

            Log::info("CREATE_NEW_POSITION(id: {$role->id}, name: {$role->name}, slug: {$role->slug})");
        }

        return $role;
    }

    /**
     * Метод поиска должностей
     *
     * @param array|null $array
     * @param bool $paginate
     * @return mixed
     */
    public static function find(?array $array = null, bool $paginate = true)
    {
        $roles = Role::query()
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

        return $paginate ? $roles->simplePaginate() : $roles->get();
    }

    /**
     * Возвращает должность
     *
     * @param array $array
     * @return Role|null
     */
    public static function get(array $array): ?Role
    {
        return self::checkPermission('view') ? Role::find($array['id']) : null;
    }

    /**
     * Удаляет должность
     *
     * @param int $id
     * @return bool
     */
    public static function destroy(int $id): bool
    {
        try {
            return self::checkPermission('delete') ? Role::find($id)->delete() : false;
        } catch(\Exception $e) {
            Log::info("POSITION_ERROR: {$e->getMessage()}");
            abort(500, 'Невозможно удалить должность, она привязана минимум к одному пользователю!');
        }
    }

    /**
     * Проверяет права на действие(action) у авторизированного пользователя
     *
     * @param $action
     * @return mixed
     */
    public static function checkPermission($action)
    {
        $permission = "users_position_$action";

        if (!User::isRoot() && !Auth::user()->hasPermission($permission)) {
            abort(403, "Нет права: $permission");
        }

        return true;
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
     * @return Role|null
     */
    public static function getWithPermissions(array $array): ?Role
    {
        return self::checkPermission('view') ? Role::where('id', $array['id'])->with('permissions')->first() : null;
    }
}
