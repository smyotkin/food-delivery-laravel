<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UsersService
{
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
                ->only(['first_name', 'last_name', 'phone', 'is_active', 'position_id'])
                ->all();

            $permissionsParams = collect($array)
                ->only(['status', 'position_id', 'permissions'])
                ->all();

            DB::transaction(function() use ($array, $basicParams, $permissionsParams) {
                if ($user = User::find($array['id'] ?? 0)) {
                    $user->update($basicParams);
                    $user->saveOrFail();

                    DB::table('users_roles')->updateOrInsert(
                        ['user_id' => $user->id],
                        ['role_id' => $permissionsParams['position_id']],
                    );

                    DB::table('users_permissions')->where('user_id', '=', $user->id)->delete();
                    $user->givePermissionsArray($permissionsParams['permissions'] ?? []);

                    Log::info("UPDATE_USER: id({$user->id})");

                } else {
                    $newUser = User::create($basicParams + [
                        'city_id' => 0,
                        'password' => Hash::make($password = random_int(100000, 999999)),
                    ]);

                    DB::table('users_roles')->updateOrInsert(
                        ['user_id' => $newUser->id],
                        ['role_id' => $permissionsParams['position_id']],
                    );

                    $newUser->givePermissionsArray($permissionsParams['permissions'] ?? []);

                    // todo Удалить пароль с логгера
                    Log::info("CREATE_NEW_USER(id: {$newUser->id}, phone: {$newUser->phone}, password: $password)");
                }
            });
        } catch (\Exception $e) {
            Log::info("CREATE_OR_UPDATE_ERROR:{$e->getMessage()}");
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
            ->orderBy('updated_at', 'desc');

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
        return User::findOrFail($array['id']);
    }

    /**
     * Возвращает права пользователя
     *
     * @param array $array
     * @return \Illuminate\Support\Collection|null
     */
    public static function getPermissions(array $array): ?\Illuminate\Support\Collection
    {
        return DB::table('users_permissions')->where('user_id', '=', $array['id'])->get();
    }

}
