<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UpdateProfileRequest;
use App\Models\Permission;
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
            'user' => UsersService::getOrFail(['id' => Auth::user()->id]),
            'timezones' => UsersService::timezones,
        ])->render();
    }

    /**
     * Обновление данных профиля
     *
     * @param UpdateProfileRequest $request
     * @return string
     * @throws \Exception
     * @throws \Throwable
     */
    public function update(UpdateProfileRequest $request): string
    {
        UsersService::updateProfile($request->validated());

        return json_encode([
            'success' => true,
        ], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_FORCE_OBJECT|JSON_UNESCAPED_UNICODE);
    }

    /**
     * Возвращает форму профиля AJAX
     *
     * @param UpdateProfileRequest $request
     * @return string
     */
    public function getAjax(UpdateProfileRequest $request): string
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
