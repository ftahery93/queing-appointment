<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use App\Models\Admin\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogActivity;

class UserProfileController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile() {


        $userInfo = Auth::user();

        $User = User::find($userInfo->id);


        // show the edit form and pass the nerd
        return View::make('admin.profile.edit')
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
//           return redirect('admin');
//           }
        $userInfo = Auth::user();
        $User = User::findOrFail($userInfo->id);



        // validate
        $validator = Validator::make($request->only(['name', 'email', 'mobile']), [
                    'name' => 'required',
                    'email' => 'required|unique:users,email,' . $userInfo->id,
                    'mobile' => 'sometimes|numeric|digits:8',
        ]);

       
        // validation failed        
        if ($validator->fails()) {
            return redirect('admin/user/profile')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->only(['name', 'email', 'civilid', 'mobile']);


            $User->fill($input)->save();

            //logActivity
            LogActivity::addToLog('User - ' . $userInfo->username, 'updated profile');

            Session::flash('message', config('global.updatedRecords'));
            return redirect('admin/user/profile');
        }
    }

}
