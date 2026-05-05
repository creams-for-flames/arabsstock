<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminLog
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
        if (auth()->check() && auth()->user()->role != 'normal' && config('app.admins_log') && $request->method() == 'POST')
            DB::enableQueryLog();
        $response = $next($request);
        if (auth()->check() && auth()->user()->role != 'normal' && config('app.admins_log') && $request->method() == 'POST') {
            $data = $request->all();
            if (file_get_contents('php://input') && @json_decode(file_get_contents('php://input'), true))
                $data = array_merge($data, json_decode(file_get_contents('php://input'), true));
            $id = DB::table('admins_log')->insertGetId([
                'user_id' => auth()->id(),
                'user_email' => auth()->user()->email,
                'route' => optional($request->route())->getName(),
                'request' => json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'created_at' => now()
            ]);
            foreach (ql() as $query)
                DB::table('admins_query_log')->insert([
                    'request_id' => $id,
                    'query' => $query['query'],
                    'bindings' => json_encode($query['bindings'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                ]);
        }
        return $response;
    }
}
