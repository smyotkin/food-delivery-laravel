<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Settings;
use App\Models\Role;
use App\Models\User;
use Throwable;

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
     * @throws Throwable
     */
    public static function createOrUpdate(?array $array = null): void
    {
        $params = collect($array)->only([
            'id',
            'name',
            'slug',
            'status',
            'permissions',
        ]);
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
        $positions = Role::query()
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

        return $paginate ? $positions->simplePaginate(Settings::get('global_rows_per_page')) : $positions->get();
    }

    /**
     * Возвращает должность
     *
     * @param array $array
     * @return Role|null
     */
    public static function get(array $array): ?Role
    {
        self::checkPermission('view');

        return Role::find($array['id']);
    }

    /**
     * Удаляет должность
     *
     * @param int $id
     * @throws Throwable
     */
    public static function destroy(int $id): void
    {
        self::checkPermission('delete');

        $role = Role::findOrFail($id);

        try {
            $role->delete();
        } catch(Throwable $e) {
            SystemService::createEvent('position_remove_error', $role->toArray(), ['msg' => $e->getMessage()]);

            Log::error($e);

            abort(500, 'Невозможно удалить должность');
        }

        SystemService::createEvent('position_removed', $role->toArray());
    }

    /**
     * Проверяет права на действие(action) у авторизированного пользователя
     *
     * @param $action
     * @return void
     */
    public static function checkPermission($action): void
    {
        $permission = "users_position_$action";

        if (!User::isRoot() && !Auth::user()->hasPermission($permission)) {
            abort(403, "Нет права: $permission");
        }
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
     * @return Role
     */
    public static function getWithPermissionsOrFail(array $array): Role
    {
        return Role::where('id', $array['id'])->with('permissions')->firstOrFail();
    }
}
