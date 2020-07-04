<?php

namespace App\Http\Controllers\Trainer\Auth;

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
use App\Models\Trainer\Trainer;
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
    protected $redirectTo = '/trainer/branches';
    protected $guard = 'trainer';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest:trainer', ['except' => 'logout']);
    }

    /* --login page--- */

    public function index() {
        return view('trainer.auth.login');
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
            return Redirect::to('trainer')
                            ->withErrors($validator) // send back all errors to the login form
                            ->withInput($request->except('password')); // send back the input (not the password) so that we can repopulate the form
        } else {

            //check Username from database
            if (!Trainer::where('username', '=', $request->get('username'))->exists()) {
                return Redirect::to('trainer')->with("error", "Your have enter wrong username.");
            }
            //check Password from database
            elseif (!Trainer::where('original_password', '=', $request->get('password'))
                            ->where('username', '=', $request->get('username'))
                            ->exists()) {
                // The passwords matches
                return Redirect::to('trainer')->with("error", "Your have enter wrong Password")->withInput($request->except('password'));
            }
             elseif (!Trainer::where('original_password', '=', $request->get('password'))
                    ->where('username', '=', $request->get('username'))->where('status', '=', 1)
                    ->exists()) {
                return Redirect::to('trainer')->with("error", "Your account has been deactivated, Kindly contact Administrator.");
            }

            // create our user data for the authentication
            $userdata = array(
                'username' => $request->username,
                'password' => $request->password
            );

            // attempt to do the login
            if (Auth::guard($this->guard)->attempt($userdata)) {
                return Redirect::to('/trainer/branches');
                // validation successful!
                // redirect them to the secure section or whatever
                // return Redirect::to('secure');
                // for now we'll just echo success (even though echoing in a controller is bad)
                //$this->redirectTo;
            } else {
                // validation not successful, send back to form
                return Redirect::to('trainer')->with("error", "Your have enter wrong Credentials.");
            }
        }
    }

    public function logout() {
        Auth::guard('trainer')->logout(); // log the user out of our application
        return Redirect::to('trainer'); // redirect the user to the login screen
    }

}
