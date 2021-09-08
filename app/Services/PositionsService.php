<?php

namespace App\Services;

use App\Models\Settings;
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
     * @return void
     * @throws \Throwable
     */
    public static function createOrUpdate(?array $array = null): void
    {
        $params = collect($array)->only(['id', 'name', 'slug', 'status', 'permissions']);
        $existingRole = Role::find($id = $params->get('id', 0));

        PositionsService::checkPermission($existingRole ? 'modify' : 'create');

        DB::transaction(function() use ($params, $id, &$role) {
            $role = Role::updateOrCreate(
                ['id' => $id],
                $params->all()
            );

            $role->syncPermissionsArray($params->get('permissions', []));
        });

        SystemService::createEvent(
            $role->wasChanged() ? 'position_updated' : 'position_created',
            $role->wasChanged() ? $existingRole->toArray() : $role->toArray(),
            $role->toArray()
        );
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

        return $paginate ? $roles->simplePaginate(Settings::get('global_rows_per_page')) : $roles->get();
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
        $role = Role::find($id);

        try {
            if ($role && self::checkPermission('delete')) {
                if ($removed = $role->delete()) {
                    SystemService::createEvent('position_removed', $role->toArray());
                }

                return $removed;
            } else {
                return false;
            }
        } catch(\Exception $e) {
            SystemService::createEvent('position_remove_error', $role->toArray(), $role->toArray());

            Log::info("POSITION_ERROR: {$e->getMessage()}");
            abort(500, 'Невозможно удалить должность, она привязана минимум к одному пользователю!');
        }

        return false;
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
        return Role::where('id', $array['id'])->with('permissions')->first();
    }

    /**
     * Возвращает должность c правами или исключение в случае отсутствия
     *
     * @param array $array
     * @return Role|null
     */
    public static function getWithPermissionsOrFail(array $array): ?Role
    {
        return Role::where('id', $array['id'])->with('permissions')->firstOrFail();
    }
}
