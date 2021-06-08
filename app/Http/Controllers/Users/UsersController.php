<?php

namespace App\Http\Controllers\Users;

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

use App\Services\UsersService;

class UsersController extends Controller
{
    /**
     * Шаблон отображения всех пользователей
     *
     * @return object
     */
    public function index(): string
    {
        return view('users/users')->render();
    }

    /**
     * Возвращает список пользователей в таблице, для AJAX
     *
     * @param Request $request
     * @return string
     */
    public function getAjax(Request $request): string
    {
        return view('users/users-table', [
            'data' => UsersService::find($request->toArray()),
        ])->render();
    }

    /**
     * Шаблон отображения пользователя по id
     *
     * @param Request $request
     * @return string
     */
    public function detail(Request $request): string
    {
        return view('users/user', [
            'user' => UsersService::get(['id' => $request->route('id')]),
        ])->render();
    }

    /**
     * Шаблон добавления пользователя
     *
     * @param Request $request
     * @return object
     */
    public function add(Request $request): string
    {
        return view('users/user-add')->render();
    }

    /**
     * Создание или редактирование нового пользователя
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function save(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = UsersService::createOrUpdate($request->toArray());

        return redirect()->route('users');
    }

    /**
     * Шаблон профиля авторизованного пользователя
     *
     * @param Request $request
     * @return object
     */
    public function profile(Request $request): string
    {
        return view('users/profile', [
            'user' => UsersService::get(Auth::user()->id),
        ])->render();
    }
}
