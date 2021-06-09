<?php

namespace App\Http\Controllers\Users;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\SaveUserRequest;
use App\Http\Controllers\Controller;
use App\Services\UsersService;

class UsersController extends Controller
{

    /**
     * Шаблон отображения всех пользователей
     *
     * @return string
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
     * @param int $id
     * @return string
     */
    public function detail(int $id): string
    {
        return view('users/user', [
            'user' => UsersService::get(['id' => $id]),
        ])->render();
    }

    /**
     * Шаблон добавления пользователя
     *
     * @param Request $request
     * @return string
     */
    public function add(Request $request): string
    {
        return view('users/user-add')->render();
    }

    /**
     * Создание или редактирование нового пользователя
     *
     * @param SaveUserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function save(SaveUserRequest $request): \Illuminate\Http\RedirectResponse
    {
        UsersService::createOrUpdate($request->validated()); // input() $user =

        return redirect()->route('users')->withInput();
    }

    /**
     * Шаблон профиля авторизованного пользователя
     *
     * @param Request $request
     * @return string
     */
    public function profile(Request $request): string
    {
        return view('users/profile', [
            'user' => UsersService::get(Auth::user()->id),
        ])->render();
    }
}
