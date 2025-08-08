<?php

namespace App\Http\Middleware;

use Closure;

class MiddlewareBroker
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
        if (! $user->isBroker()) {
            return redirect()->route('/');
        }
        return $next($request);
    }
}
