<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\User;


class UsersService
{
    /**
     * Создание или редактирование(если указан id) пользователя
     *
     * @param array|null $array
     * @return User
     * @throws \Exception
     */
    public static function createOrUpdate(?array $array = null): User
    {
        $basicParams = collect($array)
            ->only(['first_name', 'last_name', 'phone', 'is_active'])
            ->all();

        if ($user = User::find($array['id'] ?? 0)) {
            $user->update($basicParams);
            $user->saveOrFail();

            Log::info("UPDATE_USER: id({$user->id})");
        } else {
            $user = User::create($basicParams + [
                    'city_id' => 0,
                    'position_id' => 0,
                    'password' => Hash::make($password = random_int(100000, 999999)),
                ]);

            // todo Удалить пароль с логгера
            Log::info("CREATE_NEW_USER(id: {$user->id}, phone: {$user->phone}, password: $password)");
        }

        return $user;
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
        try {
            $user = User::findOrFail($array['id']);
        } catch (ModelNotFoundException $exception) {
            return back()->withError($exception->getMessage());
        }

        return $user;
    }
}
