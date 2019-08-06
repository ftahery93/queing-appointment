<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use DateTime;
use App\Models\Admin\Trainer;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;
use App\Notifications\WorkoutAssigned;
use App\Helpers\Common;

class TrainerController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:trainers');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('trainers-create');

        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('trainers-delete');

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('trainers-edit');


        $Trainer = Trainer::
                select('id', 'username', 'contract_name', 'contract_startdate', 'contract_enddate', 'status', 'created_at')
                ->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Trainer)
                            ->editColumn('created_at', function ($Trainer) {
                                $newYear = new Carbon($Trainer->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('contract_enddate', function ($Trainer) {
                                if (!empty($Trainer->contract_startdate) && !empty($Trainer->contract_enddate)) {
                                    $newYear = new Carbon($Trainer->contract_startdate);
                                    $eYear = new Carbon($Trainer->contract_enddate);
                                    return $newYear->format('d/m/Y') . '-' . $eYear->format('d/m/Y');
                                } else {
                                    return '';
                                }
                            })
                            ->editColumn('status', function ($Trainer) {
                                return $Trainer->status == 1 ? '<div class="label label-success status" sid="' . $Trainer->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary status"  sid="' . $Trainer->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('id', function ($Trainer) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $Trainer->id . '">';
                            })
                            ->editColumn('action', function ($Trainer) {
                                if ($this->EditAccess)
                                    return '<a href="' . url('admin/trainers') . '/' . $Trainer->id . '/edit" class="btn btn-info tooltip-primary btn-small " data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>'
                                            . ' <a data-id="' . $Trainer->id . '" class="btn btn-orange tooltip-primary btn-small  sendCredential" data-toggle="tooltip" data-placement="top" title="Send Credentials" data-original-title="Send Credentials"><i class="entypo-mail"></i></a>'
                                            . ' <a href="' . url('admin') . '/' . $Trainer->id . '/trainertransactions" class="btn btn-gold tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Transactions" data-original-title="Transactions"><i class="entypo-cc-share"></i></a>'
                                            . ' <a href="' . url('admin') . '/' . $Trainer->id . '/subscribers" class="btn btn-orange tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Subscribers" data-original-title="Subscribers"><i class="entypo-users"></i></a>'
                                            . ' <a href="' . url('admin') . '/trainers/' . $Trainer->id . '/packages" class="btn btn-success tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Packages" data-original-title="Packages"><i class="entypo-bag"></i></a>';
                            })
                            ->make();
        }

        return view('admin.trainers.index')
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
        $this->CreateAccess = Permit::AccessPermission('trainers-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

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


        return view('admin.trainers.create')
                        ->with('activities', $activities)
                        ->with('gender_types', $gender_types)
                        ->with('areas', $areas)
                        ->with('banks', $banks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        // validate
        $validator = Validator::make($request->only(['name', 'name_ar', 'profile_image', 'username', 'email', 'password', 'password_confirmation', 'activities', 'commission', 'mobile', 'acc_name',
                            'acc_num', 'ibn_num', 'bank_id', 'contract_startdate', 'contract_enddate', 'description_en', 'description_ar', 'area', 'gender_type']), [
                    'name' => 'required',
                    'name_ar' => 'required',
                    'description_en' => 'required',
                    'description_ar' => 'required',
                    'username' => 'required|alpha_dash|unique:trainers',
                    'email' => 'required|email|unique:trainers',
                    'password' => 'required|min:6|confirmed',
                    'activities' => 'required|array|min:1',
                    'civilid' => 'numeric|digits:12',
                    'mobile' => 'required|digits:8|unique:trainers',
                    'acc_name' => 'required',
                    'area' => 'required',
                    'gender_type' => 'required',
                    'acc_num' => 'numeric',
                    'ibn_num' => 'required|alpha_num',
                    'bank_id' => 'required',
                    'commission' => array('required', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
                    'contract_startdate' => 'date_format:d/m/Y',
                    'contract_enddate' => 'date_format:d/m/Y',
                    'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);




        // validation failed
        if ($validator->fails()) {

            return redirect('admin/trainers/create')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->only(['name', 'name_ar', 'username', 'email', 'mobile', 'area', 'gender_type', 'description_en', 'description_ar', 'status', 'commission', 'profile_image', 'acc_name', 'acc_num', 'ibn_num', 'bank_id', 'contract_name', 'contract_startdate', 'contract_enddate']);
            $input = $request->except(['password_confirmation']);
            $input['original_password'] = $request->password;
            $input['password'] = bcrypt($request->password);
            $collection = collect($request->activities);
            $input['activities'] = $collection->toJson();


            //Profile Image 
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

                $input['profile_image'] = $filename;
            }


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


            Trainer::create($input);

            //logActivity
            LogActivity::addToLog('Trainer - ' . $request->username, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect('admin/trainers');
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
        $this->EditAccess = Permit::AccessPermission('trainers-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

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


        $Trainer = Trainer::find($id);
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
        return View::make('admin.trainers.edit')
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
    public function update(Request $request, $id) {

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('trainers-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            $Trainer = Trainer::findOrFail($id);
            $Trainer->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $Trainer = Trainer::findOrFail($id);
        // validate
        $validator = Validator::make($request->only(['name', 'name_ar', 'username', 'email', 'activities', 'commission', 'mobile', 'area', 'gender_type', 'acc_name', 'acc_num', 'ibn_num', 'bank_id',
                            'contract_startdate', 'contract_enddate', 'description_en', 'description_ar']), [
                    'name' => 'required',
                    'name_ar' => 'required',
                    'description_en' => 'required',
                    'description_ar' => 'required',
                    'username' => 'required|alpha_dash|unique:trainers,username,' . $id,
                    'email' => 'required|unique:trainers,email,' . $id,
                    'activities' => 'required|array|min:1',
                    'civilid' => 'numeric|digits:12',
                    'mobile' => 'required|digits:8|unique:trainers,mobile,' . $id,
                    'acc_name' => 'required',
                    'area' => 'required',
                    'gender_type' => 'required',
                    'acc_num' => 'numeric',
                    'ibn_num' => 'required|alpha_num',
                    'bank_id' => 'required',
                    'commission' => array('required', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
                    'contract_startdate' => 'date_format:d/m/Y',
                    'contract_enddate' => 'date_format:d/m/Y',
        ]);

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

        // validation failed
        if ($validator->fails()) {
            return redirect('admin/trainers/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->only(['name', 'name_ar', 'username', 'email', 'password', 'mobile', 'area', 'gender_type', 'description_en', 'description_ar', 'status', 'commission', 'profile_image', 'acc_name', 'acc_num', 'ibn_num', 'bank_id', 'contract_name',
                'contract_startdate', 'contract_enddate']);
            $input = $request->except(['password_confirmation']);

            if ($request->has('password')) {
                $input['password'] = bcrypt($request->password);
                $input['original_password'] = $request->password;
            } else {
                $input = $request->except(['password']);
            }
            $collection = collect($request->activities);
            $input['activities'] = $collection->toJson();

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
            LogActivity::addToLog('Trainer - ' . $request->username, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect('admin/trainers');
        }
    }

    /**
     * Display a Trashed listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashedlist(Request $request) {
        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('trainers-delete');
        $Trainer = Trainer::
                        select('id', 'name', 'deleted_at')
                        ->onlyTrashed()->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Trainer)
                            ->editColumn('deleted_at', function ($Trainer) {
                                $newYear = new Carbon($Trainer->deleted_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('action', function ($Trainer) {
                                if ($this->DeleteAccess)
                                    return '<a  class="btn btn-success tooltip-primary btn-small restore" data-id="' . $Trainer->id . '"  data-toggle="tooltip" data-placement="top" title="Restore Record" data-original-title="Restore Record"><i class="entypo-ccw"></i></a>';
                                // . '<a  class="btn btn-danger tooltip-primary btn-small delete" data-id="' . $Trainer->id . '"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete Record" style="margin-left:10px;"><i class="entypo-cancel"></i></a>';
                            })
                            ->make();
        }

        return view('admin.trainers.trashedlist')->with('DeleteAccess', $this->DeleteAccess);
    }

    /**
     * ForceDelete Record.
     *
     * @param  int  $ids
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('trainers-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            //Delete profile image for vendor
            $Trainer = Trainer::withTrashed()->find($id);
            
            $destinationPath = public_path('trainers_images/');
            $destinationPath2 = public_path('trainers_images/640-250/');
            if (!empty($Trainer)) {
                if (file_exists($destinationPath . $Trainer->profile_image) && $Trainer->profile_image != '') {
                    @unlink($destinationPath . $Trainer->profile_image);
                    @unlink($destinationPath2 . $Trainer->profile_image);
                }
            }
            //logActivity
            //fetch title                        
            $groupname = $Trainer->name;

            LogActivity::addToLog('Trainer - ' . $groupname, 'deleted');

            Trainer::onlyTrashed()->where('id', '=', $id)->forceDelete();
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
        $this->DeleteAccess = Permit::AccessPermission('trainers-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {

            //logActivity
            //fetch title
            $Trainer = Trainer::withTrashed()->find($id);
            $groupname = $Trainer->name;

            LogActivity::addToLog('Trainer - ' . $groupname, 'restore');
            Trainer::withTrashed()->find($id)->restore();

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
        $this->DeleteAccess = Permit::AccessPermission('trainers-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $all_data = $request->except('_token', 'table-4_length');
        //logActivity
        //fetch title
        $Trainer = Trainer::
                select('username')
                ->whereIn('id', $all_data['ids'])
                ->get();

        $name = $Trainer->pluck('username');
        $groupname = $name->toJson();

        LogActivity::addToLog('Trainer - ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            //Check if records exist in payment details
            $Trainer = Trainer::findOrFail($id);
            if ($Trainer->paymentdetail($id) == 0) {
                Trainer::destroy($id);
                // redirect
                Session::flash('message', config('global.deletedRecords'));
            } else {
                // redirect
                Session::flash('error', config('global.relationExist'));
            }
        }


        return redirect('admin/trainers');
    }

    /**
     * Send credential via Email.
     */
    public function sendCredential($id) {
        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('trainers-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            //fetch Record
            $Trainer = Trainer::
                    select('name', 'id', 'email')
                    ->where(array('status' => 1, 'id' => $id))
                    ->first();
            //check record exist
            $count = Trainer::where(array('status' => 1, 'id' => $id))->count();

            if ($count != 0) {
                $Trainer->assign = 'Trainer';
                $Trainer->notify(new WorkoutAssigned($Trainer));

                return response()->json(['response' => config('global.sentEmail')]);
            } else {

                return response()->json(['response' => config('global.unsentEmail')]);
            }
        }
    }

    //Packages
    public function packages(Request $request) {
        $trainer_id = $request->id;
        $trainerName = Trainer::select('name')->where('id', $trainer_id)->first();

        $TrainerPackage = DB::table('trainer_packages')
                ->join('trainers', 'trainers.id', '=', 'trainer_packages.trainer_id')
                ->select('trainer_packages.name_en', 'trainer_packages.num_points', 'trainer_packages.num_days', 'trainer_packages.price', 'trainer_packages.created_at')
                ->where('trainers.status', 1)
                ->where('trainers.id', $trainer_id)
                ->whereNull('trainers.deleted_at')
                ->get();
        //Ajax request
        if (request()->ajax()) {
            return Datatables::of($TrainerPackage)
                            ->editColumn('created_at', function ($TrainerPackage) {
                                $newYear = new Carbon($TrainerPackage->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('num_points', function ($TrainerPackage) {
                                return $TrainerPackage->num_points == 0 ? 'Unlimited' : $TrainerPackage->num_points;
                            })->make();

            // return $datatable
        }

        return view('admin.trainers.packages')
                        ->with('trainer_id', $trainer_id)
                        ->with('trainerName', $trainerName);
    }

}
