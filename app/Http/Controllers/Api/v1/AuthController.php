<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Авторизация пользователя(выдача токена) по номеру телефона и паролю через API
     *
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $fields = $request->validate([
            'phone' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('phone', $fields['phone'])->firstOrFail();

        if (!Hash::check($fields['password'], $user->password)) {
            throw ValidationException::withMessages([
                'msg' => ['Введенные данные некорректны.'],
            ])->status(401);
        }

        return response([
            'user' => $user,
            'token' => $user->createToken('API')->plainTextToken,
        ], 200);
    }

    /**
     * Удаляет все токены у авторизованного пользователя через API
     *
     * @return mixed
     */
    public function logout()
    {
        Auth::user()->tokens()->delete();

        return response([
            'message' => 'Токен успешно удален.'
        ], 200);
    }
}
