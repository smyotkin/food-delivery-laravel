<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Cache;
use Carbon\Carbon;

class LastUserPage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): object
    {
        if (Auth::check()) {
            Cache::put('user-last-page-' . Auth::user()->id, $request->getPathInfo());

            $user = Auth::user();
            $user->last_page = $request->getPathInfo();
            $user->timestamps = false;
            $user->save();
        }

        return $next($request);
    }
}
