<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Services\UsersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Шаблон профиля авторизованного пользователя
     *
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        return view('users/profile', [
            'user' => UsersService::get(Auth::user()->id),
        ])->render();
    }
}
