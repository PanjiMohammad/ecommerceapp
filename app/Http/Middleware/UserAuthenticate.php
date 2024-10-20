<?php

namespace App\Http\Middleware;

use Closure;

class UserAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!auth()->guard('web')->check()) {
            return redirect(route('login'));
        }

        return $next($request);
    }
}
