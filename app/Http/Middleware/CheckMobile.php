<?php

namespace App\Http\Middleware;

use Closure;

class CheckMobile
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->method() == 'POST' && auth()->check() && !auth()->user()->mobile)
            return abort(403);

        return $next($request);
    }
}
