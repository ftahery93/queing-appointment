<?php

namespace App\Http\Controllers\Trainer\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller {
    /*
      |--------------------------------------------------------------------------
      | Password Reset Controller
      |--------------------------------------------------------------------------
      |
      | This controller is responsible for handling password reset emails and
      | includes a trait which assists in sending these notifications from
      | your application to your users. Feel free to explore this trait.
      |
     */

use SendsPasswordResetEmails;

    protected $redirectTo = '/trainer';
    protected $guard = 'trainer';
    protected $broker = 'trainers';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest:trainer', ['except' => ['showLinkRequestForm',
                'sendResetLinkEmail']]);
    }

    public function showLinkRequestForm() {
        if (property_exists($this, 'linkRequestView')) {
            return view($this->linkRequestView);
        }

        if (view()->exists('trainer.auth.passwords.email')) {
            return view('trainer.auth.passwords.email');
        }

        return view('trainer.auth.password');
    }
    
    public function broker()
    {
        return Password::broker('trainers');
    }
    
    protected function guard()
    {
        return Auth::guard('trainer');
    }

}
