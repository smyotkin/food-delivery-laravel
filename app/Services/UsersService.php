<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UsersService
{
    /**
     * Метод добавления нового пользователя
     * @param array|null $array
     * @return object
     * @throws \Exception
     */
    public static function createUser(array $array = null): object
    {
        $array['phone'] = User::toDigit($array['phone']);

        $validator = Validator::make($array, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|digits:11',
        ]);

        if ($validator->fails()) {
            dd($validator->errors());
        }

        $password = random_int(100000, 999999);

        $user = User::create([
            'first_name' => $array['first_name'],
            'last_name' => $array['last_name'],
            'city_id' => 0,
            'position_id' => 0,
            'phone' => $array['phone'],
            'password' => Hash::make($password),
            'is_active' => !empty($array['is_active']) ? 1 : 0,
        ]);

        Log::info("Create new user: id({$user->id}), phone({$user->phone}), password($password))");

        event(new Registered($user));

        return $user;
    }
}
