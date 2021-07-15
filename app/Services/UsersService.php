<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use App\Notifications\SmsCenter;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\SmscRu\SmscRuChannel;

class UsersService
{
    public const timezones = [
        'Europe/Kaliningrad' => 'Калининградское время',
        'Europe/Moscow' => 'Московское время',
        'Europe/Samara' => 'Самарское время',
        'Asia/Yekaterinburg' => 'Екатеринбургское время',
        'Asia/Omsk' => 'Омское время',
        'Asia/Krasnoyarsk' => 'Красноярское время',
        'Asia/Irkutsk' => 'Иркутское время',
        'Asia/Yakutsk' => 'Якутское время',
        'Asia/Vladivostok' => 'Владивостокское время',
        'Asia/Magadan' => 'Магаданское время',
        'Asia/Kamchatka' => 'Камчатское время',
    ];

    /**
     * Создание или редактирование(если указан id) пользователя
     *
     * @param array|null $array
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public static function createOrUpdate(?array $array = null): void
    {
        try {
            $basicParams = collect($array)
                ->only(['first_name', 'last_name', 'phone', 'is_active', 'is_custom_permissions', 'timezone'])
                ->all();

            $permissionsParams = collect($array)
                ->only(['status', 'position_id', 'permissions'])
                ->all();

            DB::transaction(function() use ($array, $basicParams, $permissionsParams) {
                if ($user = User::find($array['id'] ?? 0)) {
                    if (
                        Role::find($permissionsParams['position_id'])->status != $permissionsParams['status'] ||
                        Auth::user()->hasPermission("users_{$permissionsParams['status']}_modify") === false
                    ) {
                        abort(403, 'Нет права: ' . "users_{$permissionsParams['status']}_modify");
                    }

                    $user->update($basicParams);
                    $user->saveOrFail();

                    DB::table('users_roles')->updateOrInsert(
                        ['user_id' => $user->id],
                        ['role_id' => $permissionsParams['position_id']],
                    );

                    if ($basicParams['is_custom_permissions'] == true) {
                        DB::table('users_permissions')->where('user_id', '=', $user->id)->delete();
                        $user->givePermissionsArray($permissionsParams['permissions'] ?? []);
                    }

                    Log::info("UPDATE_USER: id({$user->id})");
                } else {
                    if (
                        Role::find($permissionsParams['position_id'])->status != $permissionsParams['status'] ||
                        Auth::user()->hasPermission("users_{$permissionsParams['status']}_add") === false
                    ) {
                        abort(403, 'Нет права: ' . "users_{$permissionsParams['status']}_add");
                    }

                    $newUser = User::create($basicParams + [
                        'city_id' => 0,
                        'password' => Hash::make($password = random_int(100000, 999999)),
                    ]);

                    if ($newUser) {
                        $newUser->notify(new SmsCenter([
                            'password' => $password
                        ]));
                    }

                    DB::table('users_roles')->insert([
                        'user_id' => $newUser->id,
                        'role_id' => $permissionsParams['position_id'],
                    ]);

                    $newUser->givePermissionsArray($permissionsParams['permissions'] ?? []);

                    // todo Удалить пароль с логгера
                    Log::info("CREATE_NEW_USER(id: {$newUser->id}, phone: {$newUser->phone}, password: $password)");
                }
            });
        } catch (\Exception $e) {
            Log::info("CREATE_OR_UPDATE_ERROR: {$e->getMessage()}");
            abort(500);
        }
    }

    public static function updateProfile(?array $array = null): void
    {
        try {
            $basicParams = collect($array)
                ->only(['timezone', 'password']);

            DB::transaction(function() use ($basicParams) {
                if ($user = Auth::user()) {
                    $password = $basicParams['password'] ?? false;

                    if ($password) {
                        $basicParams['password'] = Hash::make($basicParams['password']);
                    }

                    $user->update($basicParams->all());
                    $user->saveOrFail();

                    Log::info("UPDATE_USER: id({$user->id})");
                } else {
                    return false;
                }
            });
        } catch (\Exception $e) {
            Log::info("UPDATE_PROFILE_ERROR: {$e->getMessage()}");
            abort(500);
        }
    }

    /**
     * Метод поиска пользователей
     *
     * @param array|null $array
     * @return mixed
     */
    public static function find(?array $array = null)
    {
        $users = User::query()
            ->when(isset($array['query']), function ($query) use ($array) {
                $query
                    ->where('phone', 'like', '%' . $array['query'] . '%')
                    ->orWhere('last_name', 'like', '%' . $array['query'] . '%');
            })
            ->orderBy('last_seen', 'desc')
            ->orderBy('updated_at', 'desc')
            ->where('id', '!=', 1)
            ->with('roles');

        return $users->simplePaginate();
    }

    /**
     * Возвращает одного пользователя
     *
     * @param array $array
     * @return User
     */
    public static function get(array $array): User
    {
        return User::find($array['id']);
    }

    /**
     * Возвращает одного пользователя или ошибку
     *
     * @param array $array
     * @return mixed
     */
    public static function getOrFail(array $array)
    {
        return self::checkRoleAndPermission($array['id'], 'view') ? User::findOrFail($array['id']) : false;
    }

    /**
     * Удаляет пользователя
     *
     * @param int $id
     * @return bool
     */
    public static function destroy(int $id): bool
    {
        return self::checkRoleAndPermission($id, 'delete') ? User::find($id)->delete() : false;
    }

    /**
     * Проверяет наличие должности, право на действие(action) у авторизированного пользователя
     *
     * @param $id
     * @param $action
     * @return mixed
     */
    public static function checkRoleAndPermission($id, $action)
    {
        if (($action == 'view' && Auth::id() == $id) || User::isRoot()) {
            return true;
        }

        $user = User::find($id);
        $role = self::getRoleWithPermissions(['id' => $id]);
        $permission = isset($role->status) ? "users_{$role->status}_$action" : null;

        if (User::getRoot()->id == $id || $user === null) {
            abort(403, 'Пользователь не найден');
        } elseif (!isset($permission)) {
            abort(403, 'Пользователь не имеет должности');
        } elseif (!Auth::user()->hasPermission($permission)) {
            abort(403, "Нет права: $permission");
        } elseif ($action == 'delete' && (Auth::user()->id == $id || User::isRoot($id))) {
            abort(403, "Запрещено");
        }

        return true;
    }

    /**
     * Возвращает права пользователя
     *
     * @param array $array
     * @return \Illuminate\Support\Collection|null
     */
    public static function getPermissions(array $array): ?\Illuminate\Support\Collection
    {
        $user = User::with('permissions')->where('id', '=', $array['id'])->first();

        return !empty($user) ? $user->permissions : null;
    }

    /**
     * Возвращает должность пользователя
     *
     * @param array $array
     * @return \App\Models\Role|null
     */
    public static function getRoleWithPermissions(array $array): ?\App\Models\Role
    {
        $userRole = DB::table('users_roles')->where('user_id', '=', $array['id'])->first();

        return !empty($userRole->role_id) ? PositionsService::getWithPermissions(['id' => $userRole->role_id]) : null;
    }
}
