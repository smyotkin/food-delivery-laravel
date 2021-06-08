<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\User;
use Jenssegers\Date\Date;
use Cache;

class UsersService
{
    /**
     * Метод добавления нового пользователя
     * @param array|null $request
     * @return object
     * @throws \Exception
     */
    public static function createUser(array $request = null): object
    {
        $phone = User::toDigit($request->phone);
        $request->merge(array('phone' => $phone));

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|digits:11',
        ]);

        $password = random_int(100000, 999999);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'city_id' => 0,
            'position_id' => 0,
            'phone' => $request->phone,
            'password' => Hash::make($password),
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        Log::info("Create new user: id({$user->id}), first_name({$user->first_name}), last_name({$user->last_name}), city_id({$user->city_id}), position_id({$user->position_id}), phone({$user->phone}), password($password), is_active({$user->is_active})");

        event(new Registered($user));

        return $user;
    }
}
