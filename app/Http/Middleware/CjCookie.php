<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;

class CjCookie
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
        if (@$_SERVER['SERVER_NAME']) {
            $cookie_name = "cje";
            $domain = ".{$_SERVER['SERVER_NAME']}"; // use your domain, for instance ".example.com"
            if ($request->has('cjevent')) {
                return $next($request)->withCookie(cookie($cookie_name, $request->cjevent, Carbon::now()->addMonths(13)->diffInMinutes(), '/', $domain, true, false));
            }
        }
        return $next($request);
    }
}
