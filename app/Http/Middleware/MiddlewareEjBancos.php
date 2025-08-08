<?php

namespace App\Http\Middleware;

use Closure;

class MiddlewareEjBancos
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
        $user=Auth::user();
        if (! $user->isEjBancos()) {
            return redirect()->route('/');
        }
        return $next($request);
    }
}
