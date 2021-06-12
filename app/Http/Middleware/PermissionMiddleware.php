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
//        if ($permission !== null && !auth()->user()->can($permission)) {
//            abort(403);
//        }
        $permissions = is_array($permissions) ? $permissions : explode('|', $permissions);

        dd($permissions);

        foreach ($permissions as $permission) {
            if (!auth()->user()->can($permission)) {
//                return $next($request);
                abort(403);
            }
        }

        return $next($request);
    }
}
