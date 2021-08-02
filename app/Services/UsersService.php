<?php

namespace App\Services;

use App\Models\Settings;
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

                    $oldUser = $user->toArray();

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

                    SystemService::createEvent('user_updated', $oldUser, $user->toArray());

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

                    SystemService::createEvent('user_created', $newUser->toArray());

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

                    $old = $user->toArray();

                    $user->update($basicParams->all());
                    $user->saveOrFail();

                    SystemService::createEvent('user_updated', $old, $user->toArray());

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

        return $users->simplePaginate(Settings::get('global_rows_per_page'));
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
     * Получение пользователя по номеру телефона
     *
     * @param string $phone
     * @return mixed
     */
    public static function getByPhone(string $phone)
    {
        try {
            return User::where('phone', $phone)->firstOrFail();
        } catch(\Exception $e) {
            abort(404, 'Пользователь не найден');
        }
    }

    /**
     * Функция смены пароля у пользователя по id
     *
     * @param array $array
     * @throws \Throwable
     */
    public static function changePassword(array $array)
    {
        try {
            $params = collect($array)
                ->only(['password'])
                ->all();

            DB::transaction(function() use ($array, $params) {
                if ($user = User::find($array['id'] ?? 0)) {
                    if ($params['password']) {
                        $params['password'] = Hash::make($params['password']);
                    }

                    $user->update($params);
                    $user->saveOrFail();

                    Log::info("UPDATE_USER_PASSWORD: phone({$user->phone})");
                } else {
                    return false;
                }
            });
        } catch (\Exception $e) {
            Log::info("UPDATE_USER_PASSWORD_ERROR: {$e->getMessage()}");
            abort(500);
        }
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
     * @return mixed
     */
    public static function destroy(int $id): bool
    {
        try {
            $user = User::find($id);

            if (self::checkRoleAndPermission($id, 'delete')) {
                if ($removed = $user->delete()) {
                    SystemService::createEvent('user_removed', $user->toArray());
                }

                return $removed;
            } else {
                return false;
            }
        } catch(\Exception $e) {
            Log::info("USER_ERROR: {$e->getMessage()}");
            abort(500, 'Невозможно удалить пользователя');
        }

        return false;
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
