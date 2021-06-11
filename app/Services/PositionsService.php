<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\User;
use App\Models\Role;

class PositionsService
{

    /**
     * Создание или редактирование(если указан id) должности
     *
     * @param array|null $array
     * @return Role
     * @throws \Exception
     */
    public static function createOrUpdate(?array $array = null): Role
    {
        $basicParams = collect($array)
            ->only(['name', 'slug', 'status'])
            ->all();

        if ($role = Role::find($array['id'] ?? 0)) {
            $role->update($basicParams);
            $role->saveOrFail();

            Log::info("UPDATE_POSITION: id({$role->id})");
        } else {
            $role = Role::create($basicParams);

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
            ->orderBy('created_at', 'desc');

        return $users->simplePaginate();
    }

    /**
     * Возвращает одну должность
     *
     * @param array $array
     * @return mixed
     */
    public static function get(array $array)
    {
        return Role::findOrFail($array['id']);
    }

    /**
     * Возвращает базовые права должности
     *
     * @param string $status
     * @return array
     */
    public static function getStatusPermissions(string $status): array
    {
        return config('custom.statuses.' . $status . '.permissions');
    }

}
