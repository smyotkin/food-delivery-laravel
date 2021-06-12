<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Requests\Users\CreateOrUpdateUserRequest;
use App\Http\Controllers\Controller;

use App\Services\UsersService;
use App\Services\PositionsService;

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
     * Шаблон отображения пользователя по id
     *
     * @param int $id
     * @return string
     */
    public function show(int $id): string
    {
        return view('users/user', [
            'user' => UsersService::get(['id' => $id]),
            'statuses' => PositionsService::statuses,
        ])->render();
    }

    /**
     * Шаблон добавления пользователя
     *
     * @return string
     */
    public function create(): string
    {
        return view('users/user')->render();
    }

    /**
     * Редактирование пользователя
     *
     * @param CreateOrUpdateUserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(CreateOrUpdateUserRequest $request): \Illuminate\Http\RedirectResponse
    {
        UsersService::createOrUpdate($request->validated());

        return redirect()->route('users.index');
    }

    /**
     * Создание нового пользователя
     *
     * @param CreateOrUpdateUserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(CreateOrUpdateUserRequest $request): \Illuminate\Http\RedirectResponse
    {
        UsersService::createOrUpdate($request->validated());

        return redirect()->route('users.index');
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

}
