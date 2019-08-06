<?php

namespace App\Http\Controllers\Trainer;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use App\Models\Trainer\Trainer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Helpers\LogActivity;

class ChangePasswordController extends Controller {

     protected $guard = 'trainer';
     
    public function __construct() {
        $this->middleware('trainer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('trainer.changepassword.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request) {


        if (!(Hash::check($request->get('current_password'), Auth::guard($this->guard)->user()->password))) {
            // The passwords matches
            return redirect()->back()->with("error", "Your current password does not matches with the password you provided. Please try again.");
        }

        if (strcmp($request->get('current_password'), $request->get('new_password')) == 0) {
            //Current password and new password are same
            return redirect()->back()->with("error", "New Password cannot be same as your current password. Please choose a different password.");
        }

        // validate
        $validator = Validator::make($request->all(), [
                    'current_password' => 'required',
                    'new_password' => 'required|min:6|confirmed',
        ]);

        // validation failed
        if ($validator->fails()) {
            return redirect('trainer/user/changepassword')
                            ->withErrors($validator);
        } else {

            //Change Password
            $user = Auth::guard($this->guard)->user();
            $user->password = bcrypt($request->get('new_password'));
            $user->original_password = $request->get('new_password');
            $user->save();
            
             //logActivity
            LogActivity::addToLog('Trainer - ' . $user->username, 'change password');
            
            Session::flash('message', config('global.updatedRecords'));
            return redirect('trainer/user/changepassword');
        }
    }

}
