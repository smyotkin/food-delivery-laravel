<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
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
        $user = UsersService::getOrFail(['id' => Auth::user()->id]);
        $role = UsersService::getRoleWithPermissions(['id' => Auth::user()->id]);

        if ($is_custom_permissions = !empty($user->is_custom_permissions)) {
            $currentPermissions = UsersService::getPermissions(['id' => Auth::user()->id])->pluck('slug')->toArray();
        } else {
            $currentPermissions = !empty($role) ? $role->permissions->pluck('slug')->toArray() : [];
        }

        $filteredPermissions = Permission::orderBy('group', 'desc')->get()->filter(function ($item) use
        ($currentPermissions) {
            return in_array($item->slug, $currentPermissions);
        })->values();

        return view('users/profile', [
            'user' => $user,
            'role' => $role,
            'current_permissions' => $filteredPermissions,
        ])->render();
    }
}
