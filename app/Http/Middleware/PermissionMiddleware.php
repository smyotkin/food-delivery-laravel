<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permissions)
    {
        $permissions = is_array($permissions) ? $permissions : explode('|', $permissions);

        foreach ($permissions as $permission) {
            if (!auth()->user()->hasPermission($permission)) {
                abort(403, 'Нет права: ' . $permission);
            }
        }

        return $next($request);
    }
}
