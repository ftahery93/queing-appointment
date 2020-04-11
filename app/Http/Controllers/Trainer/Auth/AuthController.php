<?php

namespace App\Http\Controllers\Trainer\Auth;

use App\Http\Controllers\Controller;
use App\Exceptions\NoActiveAccountException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Requests;

class AuthController extends Controller {

    use AuthenticatesUsers;

    protected $redirectTo = '/trainer/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {

        $this->middleware('guest', ['except' => ['logout',
                'redirectToProvider',
                'handleProviderCallback']
        ]);
    }

    private function checkStatusLevel() {
       
        if (!Auth::users()->isActiveStatus()) {

            Auth::logout();

            throw new NoActiveAccountException;
        }
        
    }

    public function index() {
        return view('trainer.auth.login');
    }

    public function redirectPath() {

        if (Auth::users()->isAdmin()) {

            return 'trainer';
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/';
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function username() {
        return 'username';
    }

    public function login(Request $request) {
 
        $this->validateLogin($request);
       
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.

        if ($lockedOut = $this->hasTooManyLoginAttempts($request)) {
       
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->credentials($request);

        if ($this->guard()->attempt($credentials, $request->has('remember'))) {

            $this->checkStatusLevel();

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.

        if (!$lockedOut) {

            $this->incrementLoginAttempts($request);
        }

        return $this->sendFailedLoginResponse($request);
    }

}
