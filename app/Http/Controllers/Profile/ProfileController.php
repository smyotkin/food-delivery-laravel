<?php

namespace App\Http\Controllers\Profile;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Users\UpdateProfileRequest;
use App\Http\Controllers\Controller;
use App\Services\UsersService;
use App\Models\Permission;
use Exception;
use Throwable;

class ProfileController extends Controller
{
    /**
     * Шаблон профиля авторизованного пользователя
     *
     * @return string
     * @throws Exception|Throwable
     */
    public function index(): string
    {
        return view('users/profile', [
            'user' => UsersService::getOrFail(['id' => Auth::user()->id]),
            'role' => UsersService::getRoleWithPermissions(['id' => Auth::user()->id]),
            'timezones' => UsersService::timezones,
        ])->render();
    }

    /**
     * Обновление данных профиля
     *
     * @param UpdateProfileRequest $request
     * @throws Exception|Throwable
     */
    public function update(UpdateProfileRequest $request): void
    {
        UsersService::updateProfile($request->validated());
    }

    /**
     * Возвращает форму профиля AJAX
     *
     * @return string
     * @throws Exception|Throwable
     */
    public function getAjax(): string
    {
        $user = UsersService::getOrFail(['id' => Auth::user()->id]);
        $role = UsersService::getRoleWithPermissions(['id' => Auth::user()->id]);

        if (!empty($user->is_custom_permissions)) {
            $currentPermissions = UsersService::getPermissions(['id' => Auth::user()->id])->pluck('slug')->toArray();
        } else {
            $currentPermissions = !empty($role) ? $role->permissions->pluck('slug')->toArray() : [];
        }

        $filteredPermissions = Permission::orderBy('group', 'desc')->get()->filter(function ($item) use
        ($currentPermissions) {
            return in_array($item->slug, $currentPermissions);
        })->values();

        return view('users/profile-form', [
            'user' => $user,
            'role' => $role,
            'timezones' => UsersService::timezones,
            'current_permissions' => $filteredPermissions,
        ])->render();
    }
}
