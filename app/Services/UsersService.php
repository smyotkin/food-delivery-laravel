<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Settings;
use App\Models\User;
use App\Models\Role;
use App\Notifications\SmsCenter;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\SmscRu\SmscRuChannel;
use Exception;
use Throwable;

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
     * @throws Exception|Throwable
     */
    public static function createOrUpdate(?array $array = null): void
    {
        $params = collect($array)->only([
            'id',
            'first_name',
            'last_name',
            'phone',
            'is_active',
            'is_custom_permissions',
            'status',
            'position_id',
            'permissions',
        ]);

        $user = User::find($params->get('id', 0));
        $role = Role::findOrFail($params->get('position_id'));

        self::checkPermission([
                'role_status' => $role->status,
                'form_status' => $params->get('status'),
            ], $user ? 'modify' : 'add'
        );

        if (!$user = User::find($params->get('id', 0))) {
            $params
                ->put('city_id', 0)
                ->put('password', Hash::make($password = random_int(100000, 999999)));
        }

        $permissions = $params->get('is_custom_permissions') == true ? $params->get('permissions', []) :
            $role->permissions->pluck('slug')->toArray();

        DB::transaction(function() use (&$newUser, $params, $permissions) {
            $newUser = User::updateOrCreate(
                ['id' => $params->get('id', 0)],
                $params->all()
            );

            $newUser->syncRole($params->get('position_id'));
            $newUser->syncPermissionsArray($permissions);
        });

        if ($newUser->wasRecentlyCreated) {
            // todo Удалить пароль с логгера
            Log::info("CREATE_NEW_USER(id: {$newUser->id}, phone: {$newUser->phone}, password: $password)");

            SystemService::createEvent('user_created', $newUser->toArray());

            if (config('custom.send_sms', 1)) {
                $newUser->notify(new SmsCenter([
                    'password' => $password
                ]));
            }
        } else {
            SystemService::createEvent('user_updated', $user->toArray(), $newUser->toArray());
        }
    }

    /**
     * Проверяет права на действие(action) у авторизированного пользователя
     *
     * @param array  $statuses
     * @param string $action
     */
    public static function checkPermission(array $statuses, string $action): void
    {
        $permission = "users_{$statuses['form_status']}_{$action}";
        $hasPermission = Auth::user()->hasPermission($permission);

        if (!User::isRoot() && $statuses['form_status'] !== $statuses['role_status'] || !$hasPermission) {
            abort(403, 'Нет права: ' . $permission);
        }
    }

    /**
     * Метод обновляет данные в профиле (только timezone или password)
     *
     * @param array|null $array
     * @throws Throwable
     */
    public static function updateProfile(?array $array = null): void
    {
        $params = collect($array)->only([
            'timezone',
            'password',
        ]);
        $user = $updatedUser = Auth::user();

        if ($password = $params->get('password', false)) {
            $params->put('password', Hash::make($password));
        }

        $updatedUser->update($params->all());
        $updatedUser->saveOrFail();

        SystemService::createEvent('user_updated', $user->toArray(), $updatedUser->toArray());
    }

    /**
     * Метод поиска пользователей
     *
     * @param array|null $array
     * @param bool       $paginate
     * @return mixed
     */
    public static function find(?array $array = null, bool $paginate = true)
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

        return $paginate ? $users->simplePaginate(Settings::get('global_rows_per_page')) : $users->get();
    }

    /**
     * Возвращает одного пользователя
     *
     * @param array $array
     * @return User|null
     */
    public static function get(array $array): ?User
    {
        return User::find($array['id']);
    }

    /**
     * Получение пользователя по номеру телефона
     *
     * @param string $phone
     * @return User
     */
    public static function getByPhone(string $phone): User
    {
        return User::where('phone', $phone)->firstOrFail();
    }

    /**
     * Функция смены пароля у пользователя по id
     *
     * @param array $array
     * @throws Throwable
     */
    public static function changePassword(array $array): void
    {
        $params = collect($array)->only([
            'id',
            'password'
        ]);
        $user = $updatedUser = User::findOrFail($params->get('id', 0));

        if ($password = $params->get('password', false)) {
            $params->put('password', Hash::make($password));
        }

        $updatedUser->update($params->all());
        $updatedUser->saveOrFail();

        SystemService::createEvent('user_updated', $user->toArray(), $updatedUser->toArray());
    }

    /**
     * Возвращает одного пользователя или ошибку
     *
     * @param array $array
     * @return User
     * @throws Throwable
     */
    public static function getOrFail(array $array): User
    {
        self::checkRoleAndPermission($array['id'], 'view');

        return User::findOrFail($array['id']);
    }

    /**
     * Удаляет пользователя
     *
     * @param int $id
     * @throws Throwable
     */
    public static function destroy(int $id): void
    {
        self::checkRoleAndPermission($id, 'delete');

        $user = User::findOrFail($id);

        if ($user->delete()) {
            SystemService::createEvent('user_removed', $user->toArray());
        }
    }

    /**
     * Проверяет наличие должности, право на действие(action) у авторизированного пользователя
     *
     * @param int    $id
     * @param string $action
     */
    public static function checkRoleAndPermission(int $id, string $action): void
    {
        $user = User::findOrFail($id);

        // Разрешать пользователю смотреть данные о себе (профиль или карточка) || Проверка на админа
        if (($action == 'view' && Auth::id() == $id) || User::isRoot()) {
            return;
        }

        $role = $user->roles->first();
        $permission = isset($role->status) ? "users_{$role->status}_$action" : null;

        if (User::isRoot($id)) {
            abort(404, 'Пользователь не найден');
        } elseif (!isset($permission)) {
            abort(403, 'Пользователь не имеет должности');
        } elseif (!Auth::user()->hasPermission($permission)) {
            abort(403, "Нет права: $permission");
        } elseif ($action == 'delete' && (Auth::user()->id == $id || User::isRoot($id))) {
            abort(403, "Запрещено");
        }
    }

    /**
     * Возвращает права пользователя
     *
     * @param array $array
     * @return Collection|null
     */
    public static function getPermissions(array $array): ?Collection
    {
        $user = User::with('permissions')->where('id', '=', $array['id'])->first();

        return !empty($user) ? $user->permissions : null;
    }

    /**
     * Возвращает должность пользователя
     *
     * @param array $array
     * @return Role|null
     */
    public static function getRoleWithPermissions(array $array): ?Role
    {
        $userRole = DB::table('users_roles')->where('user_id', '=', $array['id'])->first();

        return !empty($userRole->role_id) ? PositionsService::getWithPermissions(['id' => $userRole->role_id]) : null;
    }
}
