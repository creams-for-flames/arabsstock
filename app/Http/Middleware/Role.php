<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Role
{

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param Guard $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $roles)
    {
        $roles = explode('|', $roles);
        if ($this->auth->guest()) {
            return redirect()->guest('login')->with(array('login_required' => trans('auth.login_required')));
        } else if ($this->auth->User()->role == 'normal' && $roles == []) {
            return redirect('/');
        } else if (!in_array($this->auth->User()->role, $roles)) {
            return redirect()->back();
        }
        // else if( $this->auth->User()->role == 'normal' ||  $this->auth->User()->role == 'admin_video' ||  $this->auth->User()->role == 'editor_image' || $this->auth->User()->id == 'admin' || $this->auth->User()->id == 'admin_vector') {
        // 	return redirect('/');
        // }

        return $next($request);
    }

}
