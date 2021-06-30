<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Cache;
use Carbon\Carbon;

class LastUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return object
     */
    public function handle(Request $request, Closure $next): object
    {
        if (Auth::check()) {
            $expiresAt = Carbon::now()->addMinutes(1);
            Cache::put('user-is-online-' . Auth::user()->id, true, $expiresAt);

            $user = Auth::user();
            $user->last_seen = now()->format("Y-m-d H:i:s");
            $user->timestamps = false;
            $user->save();
        }

        return $next($request);
    }
}
