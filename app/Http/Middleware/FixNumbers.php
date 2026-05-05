<?php

namespace App\Http\Middleware;

use Closure;

class FixNumbers
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
        foreach ($request->all() as $k => $value)
            $request->merge([$k => str_replace([
                '٠',
                '١',
                '٢',
                '٣',
                '٤',
                '٥',
                '٦',
                '٧',
                '٨',
                '٩',
            ], [
                '0',
                '1',
                '2',
                '3',
                '4',
                '5',
                '6',
                '7',
                '8',
                '9',
            ], $value)]);
        if ($request->mobile)
            $request->merge(['mobile', str_replace(' ', '', $request->mobile)]);
        return $next($request);
    }
}
