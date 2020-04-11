<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller {
    /*
      |--------------------------------------------------------------------------
      | Password Reset Controller
      |--------------------------------------------------------------------------
      |
      | This controller is responsible for handling password reset requests
      | and uses a simple trait to include this behavior. You're free to
      | explore this trait and override any methods you wish to tweak.
      |
     */

use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo ;
    protected $guard = 'vendor';
    protected $broker = 'vendors';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->redirectTo = config('global.fitflowVendor') . config('global.storeAddress') . '/home';
        $this->middleware('guest:vendor', ['except' => ['showResetForm',
                'reset']]);
    }

    public function getEmail() {
        return $this->showLinkRequestForm();
    }

    public function showResetForm(Request $request, $token = null) {        
         $token = $request->token;
        
        if (is_null($token)) {
            return $this->getEmail();
        }
        $email = $request->input('email');
        
        if (property_exists($this, 'resetView')) {
            return view($this->resetView)->with(compact('token', 'email'));
        }

        if (view()->exists('fitflowVendor.auth.passwords.reset')) {
            return view('fitflowVendor.auth.passwords.reset')->with(compact('token', 'email'));
        }

        return view('fitflowVendor.passwords.auth.reset')->with(compact('token', 'email'));
    }
    
     public function broker()
    {
        return Password::broker('vendors');
    }
    
    protected function guard()
    {
        return Auth::guard('vendor');
    }

}
