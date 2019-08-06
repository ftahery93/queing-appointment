<?php

namespace App\Http\Controllers\Trainer;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use DateTime;
use App\Models\Trainer\Trainer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogActivity;
use App\Helpers\Common;


class UserProfileController extends Controller {

    protected $guard = 'trainer';

    public function __construct() {
        $this->middleware('trainer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile() {

        $userInfo = Auth::guard($this->guard)->user();

        $Trainer = Trainer::find($userInfo->id);

        //Get all User Role
        $activities = DB::table('activities')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();

        //Get all banks
        $banks = DB::table('banks')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();

        //Get all Gender Types
        $gender_types = DB::table('gender_types')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->limit(2)
                ->get();

        //Get all Areas
        $areas = DB::table('areas')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();

        //Get permissions json value
        $collection = collect(json_decode($Trainer->activities, true));

        //Change Date Format
        if (!is_null($Trainer->contract_startdate)) {
            $newdate = new Carbon($Trainer->contract_startdate);
            $Trainer->contract_startdate = $newdate->format('d/m/Y');
        }

        if (!is_null($Trainer->contract_enddate)) {
            $enddate = new Carbon($Trainer->contract_enddate);
            $Trainer->contract_enddate = $enddate->format('d/m/Y');
        }



        // show the edit form and pass the nerd
        return View::make('trainer.profile.edit')
                        ->with('Trainer', $Trainer)
                        ->with('collection', $collection)
                        ->with('activities', $activities)
                        ->with('gender_types', $gender_types)
                        ->with('areas', $areas)
                        ->with('banks', $banks);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request) {

        $userInfo = Auth::guard($this->guard)->user();
        $Trainer = Trainer::findOrFail($userInfo->id);

        // validate
        $validator = Validator::make($request->only(['name', 'name_ar',  'email', 'activities',  'civilid', 'mobile', 'area', 'gender_type', 
                             'description_en', 'description_ar',]), [
                    'name' => 'required',
                    'name_ar' => 'required',
                    'description_en' => 'required',
                    'description_ar' => 'required',
                    'email' => 'required|unique:trainers,email,' . $userInfo->id,
                    'activities' => 'required|array|min:1',
                    'civilid' => 'numeric|digits:12',
                    'mobile' => 'required|digits:8|unique:trainers,mobile,' . $userInfo->id,
                    'area' => 'required',
                    'gender_type' => 'required'
        ]);


        // Image Validate
        //If Uploaded Image removed
        if ($request->uploaded_image_removed != 0) {
            $validator = Validator::make($request->only(['profile_image']), [
                        'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
        }
        // validation failed
        if ($validator->fails()) {
            return redirect('trainer/user/profile')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->only(['name', 'name_ar',  'civilid', 'mobile', 'area', 'gender_type', 'description_en', 'description_ar',
                'status',  'profile_image']);

            $collection = collect($request->activities);
            $input['activities'] = $collection->toJson();

            //If Uploaded Image removed
            if ($request->uploaded_image_removed != 0 && !$request->hasFile('profile_image')) {
                //Remove previous images
                $destinationPath = public_path('trainers_images/');
                $destinationPath2 = public_path('trainers_images/640-250/');
                if (file_exists($destinationPath . $Trainer->profile_image) && $Trainer->profile_image != '') {
                    @unlink($destinationPath . $Trainer->profile_image);
                    @unlink($destinationPath2 . $Trainer->profile_image);
                }
                $input['profile_image'] = '';
            } else {

                if ($request->hasFile('profile_image')) {
                    $profile_image = $request->file('profile_image');
                    $filename = time() . '.' . $profile_image->getClientOriginalExtension();
                    $destinationPath = public_path('trainers_images/');
                    $destinationPath2 = public_path('trainers_images/640-250/');
                    $profile_image->move($destinationPath, $filename);
                    //Create fix Primary image size 
                    $primary_image_path = public_path('trainers_images/' . $filename);
                    $source_primary_image_path = public_path('trainers_images/' . $filename);
                    $PrimaryMaxWidth = config('global.vendorPrimaryImageW');
                    $PrimaryMaxHeight = config('global.vendorPrimaryImageH');

                    //Create fix Secondary image size 
                    $secondary_image_path = public_path('trainers_images/640-250/' . $filename);
                    $source_secondary_image_path = public_path('trainers_images/' . $filename);
                    $SecondaryMaxWidth = config('global.vendorSecondaryImageW');
                    $SecondaryMaxHeight = config('global.vendorSecondaryImageH');

                    $reduceSize = false;
                    $cropImage = true;
                    $reduceSizePercentage = 1;
                    $maintainAspectRatio = false;
                    $SecondarymaintainAspectRatio = false;
                    $bgColor = config('global.thumbnailColor');
                    $quality = 100;
                    Common::generateThumbnails($source_primary_image_path, $primary_image_path, $reduceSize, $reduceSizePercentage, $PrimaryMaxWidth, $PrimaryMaxHeight, $maintainAspectRatio, $cropImage, $bgColor);
                    Common::generateThumbnails($source_secondary_image_path, $secondary_image_path, $reduceSize, $reduceSizePercentage, $SecondaryMaxWidth, $SecondaryMaxHeight, $SecondarymaintainAspectRatio, $cropImage, $bgColor);

                    //Remove previous images
                    if (file_exists($destinationPath . $Trainer->profile_image) && $Trainer->profile_image != '') {
                        @unlink($destinationPath . $Trainer->profile_image);
                        @unlink($destinationPath2 . $Trainer->profile_image);
                    }
                    $input['profile_image'] = $filename;
                }
            }
            
            $Trainer->fill($input)->save();

            //logActivity
            LogActivity::addToLog('Trainer - ' . $Trainer->username, 'updated');

            Session::flash('message', config('global.updatedRecords'));
            return redirect('trainer/user/profile');
        }
    }

}
