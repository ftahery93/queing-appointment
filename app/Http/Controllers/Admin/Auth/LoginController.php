<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Exceptions\NoActiveAccountException;
use Illuminate\Support\Facades\Auth;
use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Admin\User;
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
    protected $redirectTo = '/admin/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /* --login page--- */

    public function index() {
        return view('admin.auth.login');
    }

    /**
     * Authentication.
     */
    public function login(Request $request) {

// validate the info, create rules for the inputs
        $rules = array(
            'username' => 'required', // make sure the email is an actual email
            'password' => 'required|min:6' // password can only be alphanumeric and has to be greater than 3 characters
        );

// run the validation rules on the inputs from the form
        $validator = Validator::make($request->all(), $rules);

// if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return Redirect::to('admin')
                            ->withErrors($validator) // send back all errors to the login form
                            ->withInput($request->except('password')); // send back the input (not the password) so that we can repopulate the form
        } else {

            //check Username from database
            if (!User::where('username', '=', $request->get('username'))->exists()) {
                return Redirect::to('admin')->with("error", "Your have enter wrong Username.");
            }
            //check Password from database
            elseif (!User::where('original_password', '=', $request->get('password'))
                    ->where('username', '=', $request->get('username'))
                    ->exists()) {
                // The passwords matches
                return Redirect::to('admin')->with("error", "Your have enter wrong Password")->withInput($request->except('password'));
            }
           elseif (!User::where('original_password', '=', $request->get('password'))
                    ->where('username', '=', $request->get('username'))->where('status', '=', 1)
                    ->exists()) {
                return Redirect::to('admin')->with("error", "Your account has been deactivated, Kindly contact Administrator.");
            }

            // create our user data for the authentication
            $userdata = array(
                'username' => $request->username,
                'password' => $request->password
            );

            // attempt to do the login
            if (Auth::attempt($userdata)) {
                return Redirect::to('/admin/dashboard');
                // validation successful!
                // redirect them to the secure section or whatever
                // return Redirect::to('secure');
                // for now we'll just echo success (even though echoing in a controller is bad)
                //$this->redirectTo;
            } else {
                // validation not successful, send back to form
                return Redirect::to('admin')->with("error", "Your have enter wrong Credentials.");
            }
        }
    }

    public function logout() {
        Auth::logout(); // log the user out of our application
        return Redirect::to('admin'); // redirect the user to the login screen
    }

}
