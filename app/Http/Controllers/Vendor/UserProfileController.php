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
use App\Helpers\LogActivity;
use App\Helpers\VendorDetail;

class UserProfileController extends Controller {

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
    public function profile() {
   
        $userInfo = Auth::guard($this->guard)->user();
        $User = User::find($userInfo->id);


        // show the edit form and pass the nerd
        return View::make('fitflowVendor.profile.edit')
                        ->with('User', $User);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request) {

//        if (!Auth::check()) {
//           return redirect('fitflowVendor');
//           }
        $userInfo = Auth::guard($this->guard)->user();
        $User = User::findOrFail($userInfo->id);



        // validate
        $validator = Validator::make($request->only(['name', 'email', 'mobile']), [
                    'name' => 'required',
                    'email' => 'required|unique:vendor_users,email,' . $User->id,
                    'mobile' => 'required|digits:8|unique:vendor_users,mobile,' . $User->id,
        ]);


        // validation failed        
        if ($validator->fails()) {
            return redirect($this->configName . '/user/profile')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->only(['name', 'email', 'mobile']);
            $User->fill($input)->save();

            //Update record in vendor user table 
            if ($User->user_role_id == 1) {
                $Vendor = Vendor::findOrFail($User->vendor_id);
                $Vendor->fill($input)->save();
                //logActivity
                LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . ']', 'updated profile');
            } else {
                //logActivity
                LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] User - ' . $userInfo->username, 'updated profile');
            }

            Session::flash('message', config('global.updatedRecords'));
            return redirect($this->configName .'/user/profile');
        }
    }

}
