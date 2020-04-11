<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use DateTime;
use App\Models\Admin\Vendor;
use App\Models\Admin\VendorUser;
use App\Models\Admin\VendorImage;
use App\Models\Admin\ImportDataTable;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;
use App\Helpers\CreateTable;
use App\Helpers\VendorDetail;
use App\Helpers\Common;

class VendorController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:vendors');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('vendors-create');

        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('vendors-delete');

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('vendors-edit');


        $Vendor = Vendor::
                select('id', 'username', 'contract_name', 'contract_startdate', 'contract_enddate', 'status', 'created_at')
                ->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Vendor)
                            ->editColumn('created_at', function ($Vendor) {
                                $newYear = new Carbon($Vendor->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('contract_enddate', function ($Vendor) {
                                if (!empty($Vendor->contract_startdate) && !empty($Vendor->contract_enddate)) {
                                    $newYear = new Carbon($Vendor->contract_startdate);
                                    $eYear = new Carbon($Vendor->contract_enddate);
                                    return $newYear->format('d/m/Y') . '-' . $eYear->format('d/m/Y');
                                } else {
                                    return '';
                                }
                            })
                            ->editColumn('status', function ($Vendor) {
                                return $Vendor->status == 1 ? '<div class="label label-success status" sid="' . $Vendor->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary status"  sid="' . $Vendor->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('id', function ($Vendor) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $Vendor->id . '">';
                            })
                            ->editColumn('action', function ($Vendor) {
                                if ($this->EditAccess)
                                    return '<a href="' . url('admin/vendors') . '/' . $Vendor->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>'
                                            . ' <a href="' . url('admin') . '/' . $Vendor->id . '/vendorBranches" class="btn btn-black tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Branches" data-original-title="Branches"><i class="entypo-flow-tree"></i></a>'
                                            . ' <a href="' . url('admin') . '/' . $Vendor->id . '/vendortransactions" class="btn btn-gold tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Transactions" data-original-title="Transactions"><i class="entypo-cc-share"></i></a>'
                                            . ' <a href="' . url('admin') . '/' . $Vendor->id . '/members" class="btn btn-orange tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Subscribers" data-original-title="Subscribers"><i class="entypo-users"></i></a>';
                            })
                            ->make();
        }

        return view('admin.vendors.index')
                        ->with('CreateAccess', $this->CreateAccess)
                        ->with('DeleteAccess', $this->DeleteAccess)
                        ->with('EditAccess', $this->EditAccess);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('vendors-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        //Get all modules
        $modules = DB::table('modules')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();

        //Get all banks
        $banks = DB::table('banks')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();

        return view('admin.vendors.create')
                        ->with('modules', $modules)
                        ->with('banks', $banks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        //custom message
        $messages = [
            'code.unique' => 'The code has already been taken.',
        ];
        // validate
        $validator = Validator::make($request->only(['name', 'name_ar', 'profile_image', 'username', 'code', 'email', 'password', 
            'password_confirmation', 'mobile', 'modules', 'commission', 'acc_name', 'acc_num', 'ibn_num', 'bank_id',
            'contract_startdate', 'contract_enddate', 'sale_setting', 'description_en', 'description_ar', 'estore_image']), [
                    'name' => 'required',
                    'name_ar' => 'required',
                    'description_en' => 'required',
                    'description_ar' => 'required',
                    'username' => 'required|alpha_dash|unique:vendors',
                    'code' => 'required|alpha_dash|unique:vendors',
                    'email' => 'required|email|unique:vendors',
                    'password' => 'required|min:6|confirmed',
                    'modules' => 'required|array|min:1',
                    'commission' => 'required|array|min:3',
                    'mobile' => 'required|digits:8|unique:vendors',
                    'acc_name' => 'required',
                    'acc_num' => 'numeric',
                    'ibn_num' => 'required|alpha_num',
                    'bank_id' => 'required',
                    'contract_startdate' => 'date_format:d/m/Y',
                    'contract_enddate' => 'date_format:d/m/Y',
                    'sale_setting' => 'date_format:d/m/Y',
                    'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'estore_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
                        ], $messages);




        // validation failed
        if ($validator->fails()) {

            return redirect('admin/vendors/create')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->only(['name', 'name_ar', 'username', 'code', 'email', 'modules', 'mobile', 'status',
                'commission', 'profile_image', 'acc_name', 'acc_num', 'ibn_num', 'bank_id',
                'contract_name', 'contract_startdate', 'contract_enddate', 'sale_setting',
                'description_en', 'description_ar', 'estore_image', 'estore_offer_text_en', 'estore_offer_text_ar']);
            $input = $request->except(['password_confirmation']);
            $input['original_password'] = $request->password;
            $input['password'] = bcrypt($request->password);

            //Modules in json format
            $collection = collect($request->modules);
            $input['modules'] = $collection->toJson();

            //Commission in json format
            $commission = collect($request->commission);
            $input['commission'] = $commission->toJson();

            //Change Date Format
            if (strlen($request->contract_startdate)) {
                $datetime = new DateTime();
                $newDate = $datetime->createFromFormat('d/m/Y', $request->contract_startdate);
                $input['contract_startdate'] = $newDate->format('Y-m-d');
            } else {
                $input['contract_startdate'] = null;
            }

            if (strlen($request->contract_enddate)) {
                $datetime = new DateTime();
                $newDate = $datetime->createFromFormat('d/m/Y', $request->contract_enddate);
                $input['contract_enddate'] = $newDate->format('Y-m-d');
            } else {
                $input['contract_enddate'] = null;
            }

            //Change Date Format
            if (strlen($request->sale_setting)) {
                $datetime = new DateTime();
                $newDate = $datetime->createFromFormat('d/m/Y', $request->sale_setting);
                $input['sale_setting'] = $newDate->format('Y-m-d');
            } else {
                $input['sale_setting'] = null;
            }


            //Profile Image 
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

                $input['profile_image'] = $filename;
            }
            
            //Estore Image
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

                $input['estore_image'] = $filename;
            }

            $input['delivery_charge'] = $request->delivery_charge;
            $id = Vendor::create($input)->id;

            //Table Prefix 
            $prefix = 'v' . $id . '_';
            DB::table('vendors')->where('id', $id)->update(['table_prefix' => $prefix]);
            //Create Table
            CreateTable::createTable($prefix . 'members', 1); //1 for members
            CreateTable::createTable($prefix . 'subscribers_package_details', 2); //2 for Subscribers Package Details
            CreateTable::createTable($prefix . 'member_invoices', 3); //3 for Invoice
            CreateTable::createTable($prefix . 'bookings', 4); //3 for Bookings
            //Insert record in vendor user table             
            $vendorUser['vendor_id'] = $id;
            $vendorUser['name'] = $request->name;
            $vendorUser['username'] = $request->username;
            $vendorUser['email'] = $request->email;
            $vendorUser['code'] = $request->code;
            $vendorUser['password'] = $input['password'];
            $vendorUser['original_password'] = $request->password;
            $vendorUser['mobile'] = $request->mobile;
            $vendorUser['user_role_id'] = 1;
            $vendorUser['permission_id'] = 1;
            VendorUser::create($vendorUser);

            //insert record in import data table for member
            //table type 1:member ;2:uploadSchedule
            $input_importtable['table_name'] = 'members';
            $input_importtable['status'] = 1;
            $input_importtable['table_type'] = 1;
            $input_importtable['vendor_id'] = $id;
            ImportDataTable::updateOrCreate($input_importtable);


            $input_schedule['table_name'] = 'upload schedule';
            $input_schedule['status'] = 1;
            $input_schedule['table_type'] = 2;
            $input_schedule['vendor_id'] = $id;
            ImportDataTable::updateOrCreate($input_schedule);

            //logActivity
            LogActivity::addToLog('Vendor - ' . $request->name, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect('admin/vendors');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('vendors-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Get all User Role
        $modules = DB::table('modules')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();

        //Get all banks
        $banks = DB::table('banks')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();

        $Vendor = Vendor::find($id);
        //Get json value
        $collection = collect(json_decode($Vendor->modules, true));
        $commission = collect(json_decode($Vendor->commission, true));

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

        // show the edit form and pass the nerd
        return View::make('admin.vendors.edit')
                        ->with('Vendor', $Vendor)
                        ->with('collection', $collection)
                        ->with('commission', $commission)
                        ->with('modules', $modules)
                        ->with('banks', $banks);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('vendors-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            $Vendor = Vendor::findOrFail($id);
            $Vendor->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $Vendor = Vendor::findOrFail($id);

        //custom message
        $messages = [
            'code.unique' => 'The code has already been taken.',
        ];
        // validate
        $validator = Validator::make($request->only(['name', 'name_ar', 'username', 'code', 'email', 'modules', 'mobile', 'status', 'commission', 'profile_image', 'acc_name',
                            'acc_num', 'ibn_num', 'bank_id', 'contract_startdate', 'contract_enddate', 'sale_setting',
                          'description_en', 'description_ar', 'estore_image']), [
                    'name' => 'required',
                    'name_ar' => 'required',
                    'description_en' => 'required',
                    'description_ar' => 'required',
                    'username' => 'required|alpha_dash|unique:vendors,username,' . $id,
                    'code' => 'required|alpha_dash|unique:vendors,code,' . $id,
                    'email' => 'required|unique:vendors,email,' . $id,
                    'modules' => 'required|array|min:1',
                    'commission' => 'required|array|min:3',
                    //'civilid' => 'numeric|digits:12',
                    'mobile' => 'required|digits:8|unique:vendors,mobile,' . $id,
                    'acc_name' => 'required',
                    'acc_num' => 'numeric',
                    'ibn_num' => 'required|alpha_num',
                    'bank_id' => 'required',
                    'contract_startdate' => 'date_format:d/m/Y',
                    'contract_enddate' => 'date_format:d/m/Y',
                    'sale_setting' => 'date_format:d/m/Y',
                        ], $messages);

        //Password Validate
        // validate
        if ($request->has('password')) {
            $validator = Validator::make($request->only(['password', 'password_confirmation']), [
                        'password' => 'required|min:6|confirmed'
            ]);
        }


        // Image Validate
        //If Uploaded Image removed
        if ($request->uploaded_image_removed != 0) {
            $validator = Validator::make($request->only(['profile_image']), [
                        'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
        }
        
         if ($request->uploaded_image_removed_estore != 0) {
            $validator = Validator::make($request->only(['estore_image']), [
                        'estore_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
        }

        // validation failed
        if ($validator->fails()) {
            return redirect('admin/vendors/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->only(['name', 'name_ar', 'username', 'code', 'email', 'modules', 'mobile', 
                'status', 'commission', 'profile_image', 'ac_name', 'acc_num', 'ibn_num', 'bank_id',
                'contract_name', 'contract_startdate', 'contract_enddate', 'sale_setting',
                'description_en', 'description_ar', 'estore_image', 'estore_offer_text_en', 'estore_offer_text_ar']);
            $input = $request->except(['password_confirmation']);

            if ($request->has('password')) {
                $input['password'] = bcrypt($request->password);
                $input['original_password'] = $request->password;
            } else {
                $input = $request->except(['password']);
            }

            //Modules in json format
            $collection = collect($request->modules);
            $input['modules'] = $collection->toJson();

            //Commission in json format
            $commission = collect($request->commission);
            $input['commission'] = $commission->toJson();

            //Change Date Format
            if (strlen($request->contract_startdate)) {
                $datetime = new DateTime();
                $newDate = $datetime->createFromFormat('d/m/Y', $request->contract_startdate);
                $input['contract_startdate'] = $newDate->format('Y-m-d');
            } else {
                $input['contract_startdate'] = null;
            }

            if (strlen($request->contract_enddate)) {
                $datetime = new DateTime();
                $newDate = $datetime->createFromFormat('d/m/Y', $request->contract_enddate);
                $input['contract_enddate'] = $newDate->format('Y-m-d');
            } else {
                $input['contract_enddate'] = null;
            }

            //Change Date Format
            if (strlen($request->sale_setting)) {
                $datetime = new DateTime();
                $newDate = $datetime->createFromFormat('d/m/Y', $request->sale_setting);
                $input['sale_setting'] = $newDate->format('Y-m-d');
            } else {
                $input['sale_setting'] = null;
            }


            //If Uploaded Image removed
            if ($request->uploaded_image_removed != 0 && !$request->hasFile('profile_image')) {
                //Remove previous images
                $destinationPath = public_path('vendors_images/');
                $destinationPath2 = public_path('vendors_images/640-250/');
                if (file_exists($destinationPath . $Vendor->profile_image) && $Vendor->profile_image != '') {
                    @unlink($destinationPath . $Vendor->profile_image);
                    @unlink($destinationPath2 . $Vendor->profile_image);
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
                        @unlink($destinationPath . $Vendor->profile_image);
                        @unlink($destinationPath2 . $Vendor->profile_image);
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


            //Table Prefix 
            $input['table_prefix'] = 'v' . $id . '_';
            $input['delivery_charge'] = $request->delivery_charge;
            $Vendor->fill($input)->save();

            //Create Table
            CreateTable::createTable($input['table_prefix'] . 'members', 1); //1 for members
            CreateTable::createTable($input['table_prefix'] . 'subscribers_package_details', 2); //2 for Subscribers Package Details
            CreateTable::createTable($input['table_prefix'] . 'member_invoices', 3); //3 for Invoice
            CreateTable::createTable($input['table_prefix'] . 'bookings', 4); //3 for Bookings
            //Insert record in vendor user table 
            $vendorUser['vendor_id'] = $id;
            $vendorUser['name'] = $request->name;
            $vendorUser['username'] = $request->username;
            $vendorUser['email'] = $request->email;
            $vendorUser['code'] = $request->code;
            if ($request->has('password')) {
                $vendorUser['password'] = $input['password'];
                $vendorUser['original_password'] = $request->password;
            }
            $vendorUser['mobile'] = $request->mobile;
            $vendorUser['user_role_id'] = 1;
            $vendorUser['permission_id'] = 1;
            //VendorUser::updateOrCreate($vendorUser);
            VendorUser::where('user_role_id', 1)
                    ->where('vendor_id', $id)
                    ->update($vendorUser);

            //insert record in import data table for member
            //table type 1:member ;2:uploadSchedule
            $input_importtable['table_name'] = 'members';
            $input_importtable['status'] = 1;
            $input_importtable['table_type'] = 1;
            $input_importtable['vendor_id'] = $id;
            ImportDataTable::updateOrCreate($input_importtable);


            $input_schedule['table_name'] = 'upload schedule';
            $input_schedule['status'] = 1;
            $input_schedule['table_type'] = 2;
            $input_schedule['vendor_id'] = $id;
            ImportDataTable::updateOrCreate($input_schedule);


            //logActivity
            LogActivity::addToLog('Vendor - ' . $request->name, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect('admin/vendors');
        }
    }

    /**
     * Display a Trashed listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashedlist(Request $request) {
        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('vendors-delete');
        $Vendor = Vendor::
                        select('id', 'name', 'deleted_at')
                        ->onlyTrashed()->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Vendor)
                            ->editColumn('deleted_at', function ($Vendor) {
                                $newYear = new Carbon($Vendor->deleted_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('action', function ($Vendor) {
                                if ($this->DeleteAccess)
                                    return '<a  class="btn btn-success tooltip-primary btn-small restore" data-id="' . $Vendor->id . '"  data-toggle="tooltip" data-placement="top" title="Restore Record" data-original-title="Restore Record"><i class="entypo-ccw"></i></a>';
                                //. '<a  class="btn btn-danger tooltip-primary btn-small delete" data-id="' . $Vendor->id . '"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete Record" style="margin-left:10px;"><i class="entypo-cancel"></i></a>';
                            })
                            ->make();
        }

        return view('admin.vendors.trashedlist')->with('DeleteAccess', $this->DeleteAccess);
    }

    /**
     * ForceDelete Record.
     *
     * @param  int  $ids
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('vendors-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            //Delete profile image for vendor
            $Vendor = Vendor::withTrashed()->find($id);

            $destinationPath = public_path('vendors_images/');
            $destinationPath2 = public_path('vendors_images/640-250/');
            if (!empty($Vendor)) {
                if (file_exists($destinationPath . $Vendor->profile_image) && $Vendor->profile_image != '') {
                    @unlink($destinationPath . $Vendor->profile_image);
                    @unlink($destinationPath2 . $Vendor->profile_image);
                }
                if (file_exists($destinationPath . $Vendor->estore_image) && $Vendor->estore_image != '') {
                    @unlink($destinationPath . $Vendor->estore_image);
                    @unlink($destinationPath2 . $Vendor->estore_image);
                }
            }
            //logActivity
            //fetch title                        
            $groupname = $Vendor->name;

            LogActivity::addToLog('Vendor - ' . $groupname, 'deleted');

            Vendor::onlyTrashed()->where('id', '=', $id)->forceDelete();
            return response()->json(['response' => config('global.deletedRecords')]);
        }
    }

    /**
     * Restore Record.
     *
     * @param  int  $ids
     * @return \Illuminate\Http\Response
     */
    public function restore($id) {
        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('vendors-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {

            //logActivity
            //fetch title
            $Vendor = Vendor::withTrashed()->find($id);
            $groupname = $Vendor->name;
            
            $vendorUser['status'] =1;              
                VendorUser::where('user_role_id', 1)
                    ->where('vendor_id', $id)
                    ->update($vendorUser);

            LogActivity::addToLog('Vendor - ' . $groupname, 'restore');
            Vendor::withTrashed()->find($id)->restore();

            return response()->json(['response' => config('global.restoreRecord')]);
        }
    }

    /**
     * Remove the Multiple resource from storage.
     *
     * @param  int  $ids
     * @return \Illuminate\Http\Response
     */
    public function trashMany(Request $request) {
        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('vendors-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $all_data = $request->except('_token', 'table-4_length');

        //logActivity
        //fetch title
        $Vendor = Vendor::
                select('name')
                ->whereIn('id', $all_data['ids'])
                ->get();

        $name = $Vendor->pluck('name');
        $groupname = $name->toJson();

        LogActivity::addToLog('Vendor - ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            //Check if records exist in payment details
            $Vendor = Vendor::findOrFail($id);
            if ($Vendor->paymentdetail($id) == 0) {
                Vendor::destroy($id);
                $vendorUser['status'] =0;              
                VendorUser::where('user_role_id', 1)
                    ->where('vendor_id', $id)
                    ->update($vendorUser);
                // redirect
                Session::flash('message', config('global.deletedRecords'));
            } else {
                // redirect
                Session::flash('error', config('global.relationExist'));
            }
        }

        return redirect('admin/vendors');
    }

}
