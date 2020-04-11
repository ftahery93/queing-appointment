<?php

namespace App\Http\Controllers\Vendor;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use DateTime;
use Carbon\Carbon;
use App\Models\Admin\Vendor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogActivity;
use App\Helpers\VendorDetail;
use App\Helpers\Common;

class AccountDetailController extends Controller {

    protected $guard = 'vendor';
    protected $configName;

    public function __construct() {
        $this->middleware('vendor');
        $this->configName = config('global.fitflowVendor') . config('global.storeAddress');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $userInfo = Auth::guard($this->guard)->user();

        $Vendor = Vendor::find($userInfo->vendor_id);

        //Change Date Format
        if (!is_null($Vendor->contract_startdate)) {
            $newdate = new Carbon($Vendor->contract_startdate);
            $Vendor->contract_startdate = $newdate->format('d/m/Y');
        }

        if (!is_null($Vendor->contract_enddate)) {
            $enddate = new Carbon($Vendor->contract_enddate);
            $Vendor->contract_enddate = $enddate->format('d/m/Y');
        }

        if (!is_null($Vendor->sale_setting)) {
            $sdate = new Carbon($Vendor->sale_setting);
            $Vendor->sale_setting = $sdate->format('d/m/Y');
        }

        //Get all User Role
        $modules = DB::table('modules')
                        ->select('name_en')
                        ->where('status', 1)
                        ->orderby('id', 'desc')->first();


        // show the edit form and pass the nerd
        return View::make('fitflowVendor.accountDetails.edit')
                        ->with('Vendor', $Vendor)
                        ->with('module_name', $modules->name_en);
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
        $Vendor = Vendor::findOrFail($userInfo->vendor_id);

        // validate
        $validator = Validator::make($request->only(['sale_setting', 'name_ar', 'description_en', 'description_ar']), [
                    'sale_setting' => 'required',
                    'name_ar' => 'required',
                    'description_en' => 'required',
                    'description_ar' => 'required',
        ]);

        //Profile Image Validate
        if ($request->hasFile('profile_image')) {
            $validator = Validator::make($request->only(['profile_image']), [
                        'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
        }

        //Estore Image Validate
        if ($request->hasFile('estore_image')) {
            $validator = Validator::make($request->only(['estore_image']), [
                        'estore_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
        }
        // validation failed
        if ($validator->fails()) {
            return redirect($this->configName . '/user/info')
                            ->withErrors($validator)->withInput();
        } else {


            $input = $request->all();

            //If Uploaded Image removed
            if ($request->uploaded_image_removed != 0 && !$request->hasFile('profile_image')) {
                //Remove previous images
                $destinationPath = public_path('vendors_images/');
                $destinationPath2 = public_path('vendors_images/640-250/');
                if (file_exists($destinationPath . $Vendor->profile_image) && $Vendor->profile_image != '') {
                    unlink($destinationPath . $Vendor->profile_image);
                    unlink($destinationPath2 . $Vendor->profile_image);
                }
                $input['profile_image'] = '';
            } else {

                if ($request->hasFile('profile_image')) {
                    $profile_image = $request->file('profile_image');
                    $filename = time() . '.' . $profile_image->getClientOriginalExtension();
                    $destinationPath = public_path('vendors_images/');
                    $destinationPath2 = public_path('vendors_images/640-250/');
                    $profile_image->move($destinationPath, $filename);
                    //Create fix Primary image size 
                    $primary_image_path = public_path('vendors_images/' . $filename);
                    $source_primary_image_path = public_path('vendors_images/' . $filename);
                    $PrimaryMaxWidth = config('global.vendorPrimaryImageW');
                    $PrimaryMaxHeight = config('global.vendorPrimaryImageH');

                    //Create fix Secondary image size 
                    $secondary_image_path = public_path('vendors_images/640-250/' . $filename);
                    $source_secondary_image_path = public_path('vendors_images/' . $filename);
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
                    if (file_exists($destinationPath . $Vendor->profile_image) && $Vendor->profile_image != '') {
                        unlink($destinationPath . $Vendor->profile_image);
                        unlink($destinationPath2 . $Vendor->profile_image);
                    }
                    $input['profile_image'] = $filename;
                }
            }

            //If Estore Uploaded Image removed
            if ($request->uploaded_image_removed_estore != 0 && !$request->hasFile('estore_image')) {
                //Remove previous images
                $destinationPath = public_path('vendors_images/');
                $destinationPath2 = public_path('vendors_images/640-250/');
                if (file_exists($destinationPath . $Vendor->estore_image) && $Vendor->estore_image != '') {
                    @unlink($destinationPath . $Vendor->estore_image);
                    @unlink($destinationPath2 . $Vendor->estore_image);
                }
                $input['estore_image'] = '';
            } else {

                if ($request->hasFile('estore_image')) {
                    $profile_image = $request->file('estore_image');
                    $filename = time() . '.' . $profile_image->getClientOriginalExtension();
                    $destinationPath = public_path('vendors_images/');
                    $destinationPath2 = public_path('vendors_images/640-250/');
                    $profile_image->move($destinationPath, $filename);
                    //Create fix Primary image size 
                    $primary_image_path = public_path('vendors_images/' . $filename);
                    $source_primary_image_path = public_path('vendors_images/' . $filename);
                    $PrimaryMaxWidth = config('global.vendorEstorePrimaryImageW');
                    $PrimaryMaxHeight = config('global.vendorEstorePrimaryImageH');

                    //Create fix Secondary image size 
                    $secondary_image_path = public_path('vendors_images/640-250/' . $filename);
                    $source_secondary_image_path = public_path('vendors_images/' . $filename);
                    $SecondaryMaxWidth = config('global.vendorEstoreSecondaryImageW');
                    $SecondaryMaxHeight = config('global.vendorEstoreSecondaryImageH');

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
                    if (file_exists($destinationPath . $Vendor->estore_image) && $Vendor->estore_image != '') {
                        @unlink($destinationPath . $Vendor->estore_image);
                        @unlink($destinationPath2 . $Vendor->estore_image);
                    }
                    $input['estore_image'] = $filename;
                }
            }

            //Change Date Format
            $datetime = new DateTime();
            $newDate = $datetime->createFromFormat('d/m/Y', $request->sale_setting);
            $input['sale_setting'] = $newDate->format('Y-m-d');

            $Vendor->fill($input)->save();

            //logActivity
            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Profile Image', 'updated');

            Session::flash('message', config('global.updatedRecords'));
            return redirect($this->configName . '/user/info');
        }
    }

}
