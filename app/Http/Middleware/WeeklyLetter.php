<?php

namespace App\Http\Middleware;

use Closure;

class WeeklyLetter
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
        $status =TRUE;
        if (auth()->check()) {
            $role = auth()->user()->role;
            switch ($role) {
                case 'admin_image_editor':
                    $status =   auth()->user()->email !== "sarahp@arabsstock.com";
                    break;
                case 'admin':
                    $status = FALSE;
                    break;

            }
        }
        if ($status) {
            abort_if($status,404);  
        }

        return $next($request);
    }
}
