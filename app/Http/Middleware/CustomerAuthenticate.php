<?php

namespace App\Http\Middleware;

use Closure;

class CustomerAuthenticate
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
        if (!auth()->guard('customer')->check()) {
            return redirect(route('customer.login'))->with('error', 'Sesi anda sudah berakhir, silahkan login kembali.');
        }

        return $next($request);
    }
}
