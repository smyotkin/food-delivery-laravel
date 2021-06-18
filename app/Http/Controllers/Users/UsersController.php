<?php

namespace App\Http\Controllers\Users;

use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Requests\Users\CreateOrUpdateUserRequest;
use App\Http\Controllers\Controller;

use App\Services\UsersService;
use App\Services\PositionsService;

class UsersController extends Controller
{

    /**
     * Настройка доступа через Middleware
     */
    public function __construct()
    {
        $this->middleware('permissions:users_modes_modify')->only([
            'update',
            'create',
            'store',
        ]);
    }

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
        $user = UsersService::getOrFail(['id' => $id]);
        $role = UsersService::getRoleWithPermissions(['id' => $id]);
        $role_permissions = !empty($role) ? $role->permissions->pluck('slug')->toArray() : [];

        if ($is_custom_permissions = !empty($user->is_custom_permissions)) {
            $current_permissions = UsersService::getPermissions(['id' => $id])->pluck('slug')->toArray();
        } else {
            $current_permissions = $role_permissions;
        }

        return view('users/user', [
            'user' => $user,
            'role' => $role,
            'statuses' => PositionsService::statuses,
            'positions' => PositionsService::find(['status' => old('status') ?? $role->status ?? '']),
            'permissions' => Permission::orderBy('group', 'desc')->get(),
            'is_custom_permissions' => $is_custom_permissions,
            'role_permissions' => $role_permissions,
            'current_permissions' => $current_permissions,
        ])->render();
    }

    /**
     * Шаблон добавления пользователя
     *
     * @return string
     */
    public function create(): string
    {
        return view('users/user', [
            'statuses' => PositionsService::statuses,
            'positions' => PositionsService::find(['status' => old('status') ?? $role->status ?? '']),
            'permissions' => Permission::orderBy('group', 'desc')->get(),
        ])->render();
    }

    /**
     * Редактирование пользователя
     *
     * @param CreateOrUpdateUserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     * @throws \Throwable
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
     * @throws \Throwable
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
            'roles' => PositionsService::find(null, false),
        ])->render();
    }

    /**
     * Возвращает таблицу со всеми правами и выбранными checkbox'ами по должности или кастомным настройками
     *
     * @param Request $request
     * @return string
     */
    public function getPermissionsCheckedAjax(Request $request): string
    {
        $user = UsersService::getPermissions(['id' => $request->user_id ?? 0]);
        $role = PositionsService::getWithPermissions(['id' => $request->id]);
        $role_permissions = !empty($role) ? $role->permissions->pluck('slug')->toArray() : [];

        if ($is_custom_permissions = !empty($request->boolean('is_custom_permissions'))) {
            $current_permissions = !empty($user) ? $user->pluck('slug')->toArray() : [];
        } else {
            $current_permissions = $role_permissions;
        }

        return view('users/permissions-table', [
            'permissions' => Permission::orderBy('group', 'desc')->get(),
            'is_custom_permissions' => $is_custom_permissions,
            'role_permissions' => $role_permissions,
            'current_permissions' => $current_permissions,
        ])->render();
    }

}
