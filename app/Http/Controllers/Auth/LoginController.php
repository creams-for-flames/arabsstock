<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use Mail;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';
    protected $redirectToAdmin = '/panel/v2/dashboard';
    protected $redirectToAdminVideo = '/panel/v2/videos/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function login(Request $request)
    {
        // get our login input
        $login = $request->input('email');

        // check login field
        $login_type = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // merge our login field into the request with either email or username as key
        $request->merge([$login_type => $login]);

        // let's validate and set our credentials
        if ($login_type == 'email') {
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required',
            ]);
            $credentials = $request->only('email', 'password');
        } else {
            $this->validate($request, [
                'email' => 'required',
                'password' => 'required',
            ]);
            $credentials = $request->only('username', 'password');
        }

        if ($this->auth->attempt($credentials, $request->has('remember'))) {
            $user = auth()->user();
            if ($user->free_images || $user->free_videos || $user->free_vectors)
                Auth::logoutOtherDevices($request->password);
            if ($this->auth->User()->status == 'active') {
                $role = optional($this->guard()->user())->role;
                $redirect_url = route(config("roles.{$role}.redirect_after_login"));
                if ($request->ajax()) {
                    if ($user->free_images || $user->free_videos || $user->free_vectors)
                        return response(['status' => config("roles.{$role}.login_status"), 'url' => $redirect_url])->withCookie(cookie('stay', 1, 60 * 24 * 30));
                    return ['status' => config("roles.{$role}.login_status"), 'url' => $redirect_url];
                }
                if ($user->free_images || $user->free_videos || $user->free_vectors)
                    return redirect()->to($redirect_url);
                return redirect()->to($redirect_url)->withCookie(cookie('stay', 1, 60 * 24 * 30));

            } else {
                if ($this->auth->User()->status == 'suspended') {
                    $this->auth->logout();
                    if ($request->ajax()) {
                        return ['status' => trans('validation.user_suspended')];
                    }
                } else {
                    if ($this->auth->User()->status == 'pending') {
                        $this->auth->logout();
                        if ($request->ajax()) {
                            return ['status' => trans('validation.account_not_confirmed')];
                        }
                    }
                }
            }
        }

        if ($request->ajax()) {
            return ['status' => $this->getFailedLoginMessage()];
        }
    }

    public function loginAdmin(Request $request)
    {


        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponseAdmin($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }


    protected function sendLoginResponseAdmin(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($this->guard()->user() && $this->guard()->user()->role == 'admin_video') {
            return $this->authenticated($request, $this->guard()->user())
                ?: redirect()->intended($this->redirectPathAdminVideo());
        } elseif ($this->guard()->user() && $this->guard()->user()->role == 'admin') {
            return $this->authenticated($request, $this->guard()->user())
                ?: redirect()->intended($this->redirectPathAdmin());
        }

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath());

    }


    public function redirectPathAdmin()
    {
        if (method_exists($this, 'redirectToAdmin')) {
            return $this->redirectToAdmin();
        }

        return property_exists($this, 'redirectToAdmin') ? $this->redirectToAdmin : '/admin/home';
    }

    public function redirectPathAdminVideo()
    {
        if (method_exists($this, 'redirectToAdminVideo')) {
            return $this->redirectToAdminVideo();
        }

        return property_exists($this, 'redirectToAdminVideo') ? $this->redirectToAdminVideo : 'video/admin/home';
    }


    /**
     * Get the failed login message.
     *
     * @return string
     */
    protected function getFailedLoginMessage()
    {
        return trans('auth.error_logging');
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        return redirect()->route('landPage');
    }


    public function showLoginFormAdmin()
    {
        return view('auth.loginAdmin');
    }

}
