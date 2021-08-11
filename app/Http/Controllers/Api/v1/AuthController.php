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
    public function login(Request $request) {
        $fields = $request->validate([
            'phone' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('phone', $fields['phone'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            throw ValidationException::withMessages([
                'phone' => ['Введенные данные некорректны.'],
            ]);
        }

        $token = $user->createToken('API')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response, 201);
    }

    public function logout() {
        Auth::user()->tokens()->delete();

        return [
            'message' => 'Токен успешно удален.'
        ];
    }
}
