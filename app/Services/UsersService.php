<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
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

        $updateData = [
            'first_name' => $array['first_name'],
            'last_name' => $array['last_name'],
            'phone' => $array['phone'],
            'is_active' => !empty($array['is_active']) ? 1 : 0,
        ];

        $password = random_int(100000, 999999);

        $registerDate = $updateData + [
            'city_id' => 0,
            'position_id' => 0,
            'password' => Hash::make($password),
        ];

        $user = User::firstOrNew(['id' => $array['id'] ?? 0], $registerDate);

        if ($user->exists) {
            $user->update($updateData);

            Log::info("Update user: id({$user->id})");
        } else {
            event(new Registered($user));

            // todo Удалить пароль с логгера
            Log::info("CREATE_NEW_USER(id: {$user->id}, phone: {$user->phone}, password: $password)");
        }

        $user->save();

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
        if (isset($array['query'])) {
            $users = User::where('phone', 'like', '%' . $array['query'] . '%')
                ->orWhere('last_name', 'like', '%' . $array['query'] . '%')
                ->orderBy('last_seen', 'desc')
                ->orderBy('updated_at', 'desc')
                ->simplePaginate(100);
        } else {
            $users = User::orderBy('last_seen', 'desc')
                ->orderBy('updated_at', 'desc')
                ->simplePaginate(100);
        }

        return $users;
    }

    // todo exceptions and validation

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
}
