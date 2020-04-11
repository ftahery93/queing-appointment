<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Exceptions\NoActiveAccountException;
use Illuminate\Support\Facades\Auth;
use Validator;
use Redirect;
use Session;
use DB;
use Cookie;
use View;
use Crypt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Vendor\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller {
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
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo;
    protected $guard = 'vendor';
    protected $configName;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest:vendor', ['except' => 'logout']);
        $this->configName = config('global.fitflowVendor');
        $this->redirectTo = config('global.fitflowVendor');
    }

    /* --login page--- */

    public function index(Request $request) {
        if ($request->hasCookie('code')) {
            $code = Cookie::get('code');
            return Redirect::to($this->configName . '/' . $code);
        }
        return view('fitflowVendor.auth.index');
    }

    public function loginindex(Request $request) {
        if (!$request->hasCookie('code')) {
            return Redirect::to($this->configName);
        }
        return view('fitflowVendor.auth.login')->with('code', $request->code);
    }

    public function store(Request $request) {
        // validate the info, create rules for the inputs
        $rules = array(
            'code' => 'required',
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::to($this->configName)
                            ->withErrors($validator) // send back all errors to the login form
                            ->withInput();
        } else {

            //check Username from database
            if (!User::where('code', '=', $request->get('code'))->where('status', '=', 1)->where('user_role_id', '=', 1)->exists()) {
                return Redirect::to(config('global.fitflowVendor'))->with("error", "Your have enter wrong code.");
            }
            //forever
            return Redirect::to($this->configName . '/' . $request->get('code'))->withCookie(Cookie::forever('code', $request->get('code')));
        }
    }

    /**
     * Authentication.
     */
    public function login(Request $request) {
        //Check cookies       
        if ($request->hasCookie('code')) {
            //$value = Crypt::decrypt($request->code);
            if (Cookie::get('code') != $request->code) {
                return Redirect::to($this->configName)->withCookie(Cookie::forget('code'))->with("error", "Invalid code.");
            }
            $code = Cookie::get('code');
        }

// validate the info, create rules for the inputs
        $rules = array(
            'username' => 'required', // make sure the email is an actual email
            'password' => 'required|min:6' // password can only be alphanumeric and has to be greater than 3 characters
        );

// run the validation rules on the inputs from the form
        $validator = Validator::make($request->all(), $rules);

// if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return Redirect::to($this->configName . '/' . $code)
                            ->withErrors($validator) // send back all errors to the login form
                            ->withInput($request->except('password')); // send back the input (not the password) so that we can repopulate the form
        } else {
            //check Username from database
            if (!User::where('code', '=', $code)->where('status', '=', 1)->where('user_role_id', '=', 1)->exists()) {
                 return Redirect::to($this->configName . '/' . $code)->with("error", "Your account has been deactivated, Kindly contact Administrator.");
            }
            elseif (!User::where('username', '=', $request->get('username'))->where('code', '=', $code)->exists()) {
                return Redirect::to($this->configName . '/' . $code)->with("error", "Your have enter wrong Username.");
            }
            //check Password from database
            elseif (!User::where('original_password', '=', $request->get('password'))
                            ->where('username', '=', $request->get('username'))
                            ->where('code', '=', $code)
                            ->exists()) {
                // The passwords matches
                return Redirect::to($this->configName . '/' . $code)->with("error", "Your have enter wrong Password")->withInput($request->except('password'));
            } elseif (!User::where('original_password', '=', $request->get('password'))
                            ->where('username', '=', $request->get('username'))
                            ->where('code', '=', $code)
                            ->where('status', '=', 1)
                            ->exists()) {
                return Redirect::to($this->configName . '/' . $code)->with("error", "Your account has been deactivated, Kindly contact Administrator.")->withInput($request->except('password'));
            }

            // create our user data for the authentication
            $userdata = array(
                'username' => $request->username,
                'password' => $request->password
            );

            // attempt to do the login
            if (Auth::guard($this->guard)->attempt($userdata)) {
                return Redirect::to($this->configName . '/' . $code . '/home');
                // validation successful!
                // redirect them to the secure section or whatever
                // return Redirect::to('secure');
                // for now we'll just echo success (even though echoing in a controller is bad)
                //$this->redirectTo;
            } else {
                // validation not successful, send back to form
                return Redirect::to($this->configName . '/' . $code)->with("error", "Your have enter wrong Credentials.");
            }
        }
    }

    public function logout(Request $request) {
        Auth::guard('vendor')->logout(); // log the user out of our application
        //Check cookies
        if ($request->hasCookie('code')) {
            $code = Cookie::get('code');
        }
        return Redirect::to($this->configName . '/' . $code); // redirect the user to the login screen
    }

}
