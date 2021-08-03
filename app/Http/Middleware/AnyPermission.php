<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AnyPermission
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param                          $permissions
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permissions)
    {
        $permissions = is_array($permissions) ? $permissions : explode('||', $permissions);

        foreach ($permissions as $permission) {
            if (auth()->user()->hasPermission($permission)) {
                return $next($request);
            }
        }

        abort(403, 'Нет любого из прав: ' . implode(', ', $permissions));

        return 0;
    }
}
