<?php

namespace App\Http\Controllers\Users;

use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Requests\Users\CreateOrUpdateUserRequest;
use App\Http\Controllers\Controller;

use App\Services\UsersService;
use App\Services\PositionsService;

use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{

    /**
     * Настройка доступа через Middleware
     */
    public function __construct() {}

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
            'available_statuses' => Auth::user()->availableStatusesByType('modify'),
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
            'available_statuses' => Auth::user()->availableStatusesByType('add'),
        ])->render();
    }

    /**
     * Редактирование пользователя
     *
     * @param CreateOrUpdateUserRequest $request
     * @return string
     * @throws \Exception
     * @throws \Throwable
     */
    public function update(CreateOrUpdateUserRequest $request): string
    {
        UsersService::createOrUpdate($request->validated());

        return json_encode([
            'success' => true,
        ], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_FORCE_OBJECT|JSON_UNESCAPED_UNICODE);
    }

    /**
     * Создание нового пользователя
     *
     * @param CreateOrUpdateUserRequest $request
     * @return string
     * @throws \Exception
     * @throws \Throwable
     */
    public function store(CreateOrUpdateUserRequest $request): string
    {
        UsersService::createOrUpdate($request->validated());

        return json_encode([
            'success' => true,
        ], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_FORCE_OBJECT|JSON_UNESCAPED_UNICODE);
    }

    /**
     * Удаление должности
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        UsersService::destroy($id);

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
            'statuses' => PositionsService::statuses,
            'data' => UsersService::find($request->toArray()),
            'roles' => PositionsService::find(null, false),
        ])->render();
    }

    /**
     * Возвращает форму пользователя(принимает $action - show(все данные) или create(пусто)), для AJAX
     *
     * @param Request $request
     * @return string
     */
    public function getUserFormAjax(Request $request): string
    {
        $role = UsersService::getRoleWithPermissions(['id' => $request->id]);
        $returnedData = [
            'statuses' => PositionsService::statuses,
            'positions' => PositionsService::find(['status' => old('status') ?? $role->status ?? '']),
            'permissions' => Permission::orderBy('group', 'desc')->get(),
            'available_statuses' => Auth::user()->availableStatusesByType('add'),
        ];

        if ($request->action == 'show') {
            $user = UsersService::getOrFail(['id' => $request->id]);
            $role = UsersService::getRoleWithPermissions(['id' => $request->id]);
            $role_permissions = !empty($role) ? $role->permissions->pluck('slug')->toArray() : [];

            if ($is_custom_permissions = !empty($user->is_custom_permissions)) {
                $current_permissions = UsersService::getPermissions(['id' => $request->id])->pluck('slug')->toArray();
            } else {
                $current_permissions = $role_permissions;
            }

            $returnedData = $returnedData + [
                'user' => $user,
                'role' => $role,
                'is_custom_permissions' => $is_custom_permissions,
                'role_permissions' => $role_permissions,
                'current_permissions' => $current_permissions,
                'available_statuses' => Auth::user()->availableStatusesByType('modify'),
            ];
        }

        return view('users/user-form', $returnedData)->render();
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
            $current_permissions = !empty($user) ? $user->pluck('slug')->toArray() : $role_permissions;
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
