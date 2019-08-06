<?php

namespace App\Http\Controllers\Vendor;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use App\Models\Vendor\User;
use App\Models\Admin\Vendor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Helpers\LogActivity;

class ChangePasswordController extends Controller {

    protected $guard = 'vendor';
    protected $configName;

    public function __construct() {
        $this->middleware('vendor');
        $this->configName = config('global.fitflowVendor').config('global.storeAddress');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('fitflowVendor.changepassword.edit');
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
            return redirect($this->configName.'/user/changepassword')
                            ->withErrors($validator);
        } else {

            //Change Password
            $user = Auth::guard($this->guard)->user();
            $user->password = bcrypt($request->get('new_password'));
            $user->original_password = $request->get('new_password');
            $user->save();

            //Update record in vendor user table 
            if ($user->user_role_id == 1) {
                $Vendor = Vendor::findOrFail($user->vendor_id);
                $input['password']=bcrypt($request->get('new_password'));
                $input['original_password']=$request->get('new_password');
                $Vendor->fill($input)->save();
                //logActivity
                LogActivity::addToLog('Vendor' . $user->username, 'change password');
            } else {
                //logActivity
                LogActivity::addToLog('Vendor ' . $Vendor->name . ' - User - ' . $userInfo->username, 'change password');
            }

            Session::flash('message', config('global.updatedRecords'));
            return redirect($this->configName.'/user/changepassword');
        }
    }

}
