<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use DateTime;
use App\Models\Admin\RegisteredUser;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;

class RegisteredUserController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:registeredUsers');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
       
        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('registeredUsers-create');

        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('registeredUsers-delete');

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('registeredUsers-edit');


        $RegisteredUser = RegisteredUser::select('id', 'name', 'email', 'mobile', 'dob', 'status', 'created_at')
                ->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($RegisteredUser)
                            ->editColumn('dob', function ($RegisteredUser) {
                                $dob = new Carbon($RegisteredUser->dob);
                                return $dob->format('d/m/Y');
                            })
                            ->editColumn('created_at', function ($RegisteredUser) {
                                $newYear = new Carbon($RegisteredUser->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('status', function ($RegisteredUser) {
                                return $RegisteredUser->status == 1 ? '<div class="label label-success status" sid="' . $RegisteredUser->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary status"  sid="' . $RegisteredUser->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('id', function ($RegisteredUser) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $RegisteredUser->id . '">';
                            })
                            ->editColumn('action', function ($RegisteredUser) {
                                if ($this->EditAccess)
                                    return '<a href="' . url('admin/registeredUsers') . '/' . $RegisteredUser->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>'
                                            . ' <a href="' . url('admin/registeredUsers') . '/' . $RegisteredUser->id . '/packageHistory" class="btn btn-primary tooltip-primary btn-small package_history" data-toggle="tooltip"  data-original-title="Package History" title="Package History"><i class="entypo-bag"></i></a>';
                            })
                            ->make();
        }

        return view('admin.registeredUsers.index')
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
        $this->CreateAccess = Permit::AccessPermission('registeredUsers-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        //Get all Areas
        $areas = DB::table('areas')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();

        //Get all Gender Type
        $gender_types = DB::table('gender_types')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();

        return view('admin.registeredUsers.create')
                        ->with('areas', $areas)
                        ->with('gender_types', $gender_types);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // validate
        $validator = Validator::make($request->only(['name', 'email', 'password', 'password_confirmation', 'dob', 'mobile', 'area_id', 'gender_id']), [
                    'name' => 'required',
                    'email' => 'required|email|unique:registered_users',
                    'password' => 'required|min:6|confirmed',
                    'dob' => 'required|date_format:d/m/Y|before_or_equal:'.Carbon::now(),
                    'area_id' => 'required',
                    'gender_id' => 'required',
                    'mobile' => 'required|digits:8|unique:registered_users'
        ]);


        //Profile Image Validate
        if ($request->hasFile('profile_image')) {
            $validator = Validator::make($request->only(['profile_image']), [
                        'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
        }

        // validation failed
        if ($validator->fails()) {

            return redirect('admin/registeredUsers/create')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->only(['name', 'email', 'mobile', 'status', 'profile_image']);
            $input = $request->except(['password_confirmation']);
            $input['original_password'] = $request->password;
            $input['password'] = sha1($request->password);

            //Change Date Format
            $datetime = new DateTime();
            $newDate = $datetime->createFromFormat('d/m/Y', $request->dob);
            $input['dob'] = $newDate->format('Y-m-d');

            //Profile Image 
            if ($request->hasFile('profile_image')) {
                $profile_image = $request->file('profile_image');
                $filename = time() . '.' . $profile_image->getClientOriginalExtension();
                $destinationPath = public_path('registeredUsers_images/');
                $profile_image->move($destinationPath, $filename);
                $input['profile_image'] = $filename;
            }

            RegisteredUser::create($input);

            //logActivity
            LogActivity::addToLog('RegisteredUser - ' . $request->name, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect('admin/registeredUsers');
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
        $this->EditAccess = Permit::AccessPermission('registeredUsers-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');


        $RegisteredUser = RegisteredUser::find($id);

        //Get all Areas
        $areas = DB::table('areas')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();

        //Get all Gender Type
        $gender_types = DB::table('gender_types')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();

        //Change Date Format
        $newdate = new Carbon($RegisteredUser->dob);
        $RegisteredUser->dob = $newdate->format('d/m/Y');

        // show the edit form and pass the nerd
        return View::make('admin.registeredUsers.edit')
                        ->with('RegisteredUser', $RegisteredUser)
                        ->with('areas', $areas)
                        ->with('gender_types', $gender_types);
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
        $this->EditAccess = Permit::AccessPermission('registeredUsers-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            $RegisteredUser = RegisteredUser::findOrFail($id);
            $RegisteredUser->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $RegisteredUser = RegisteredUser::findOrFail($id);

        // validate
        $validator = Validator::make($request->only(['name', 'email', 'dob', 'mobile', 'area_id', 'gender_id']), [
                    'name' => 'required',
                    'email' => 'required|unique:registered_users,email,' . $id,
                    'dob' => 'required|date_format:d/m/Y|before_or_equal:'.Carbon::now(),
                    'area_id' => 'required',
                    'gender_id' => 'required',
                    'mobile' => 'required|digits:8|unique:registered_users,mobile,' . $id,
        ]);

        //Password Validate
        // validate
        if ($request->has('password')) {        	
            $validator = Validator::make($request->only(['password','password_confirmation']), [
                        'password' => 'required|min:6|confirmed'
            ]);
        }


        //Profile Image Validate
        if ($request->hasFile('profile_image')) {
            $validator = Validator::make($request->only(['profile_image']), [
                        'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
        }
        // validation failed
        if ($validator->fails()) {
            return redirect('admin/registeredUsers/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->only(['name', 'email', 'password', 'mobile', 'status', 'profile_image']);
            $input = $request->except(['password_confirmation']);

            if ($request->has('password')) {
                $input['password'] = sha1($request->password);
                $input['original_password'] = $request->password;
            } else {
                $input = $request->except(['password']);
            }

            //Change Date Format
            $datetime = new DateTime();
            $newDate = $datetime->createFromFormat('d/m/Y', $request->dob);
            $input['dob'] = $newDate->format('Y-m-d');

            //Profile Image 
            if ($request->hasFile('profile_image')) {
                $profile_image = $request->file('profile_image');
                $filename = time() . '.' . $profile_image->getClientOriginalExtension();
                $destinationPath = public_path('registeredUsers_images/');
                $profile_image->move($destinationPath, $filename);
                //Remove previous images
                if (file_exists($destinationPath . $RegisteredUser->profile_image) && $RegisteredUser->profile_image != '') {
                    unlink($destinationPath . $RegisteredUser->profile_image);
                }
                $input['profile_image'] = $filename;
            }

            //If Uploaded Image removed
            if ($request->uploaded_image_removed != 0) {
                //Remove previous images
                $destinationPath = public_path('registeredUsers_images/');
                if (file_exists($destinationPath . $RegisteredUser->profile_image) && $RegisteredUser->profile_image != '') {
                    unlink($destinationPath . $RegisteredUser->profile_image);
                }
                $input['profile_image'] = '';
            }

            $RegisteredUser->fill($input)->save();

            //logActivity
            LogActivity::addToLog('RegisteredUser - ' . $request->name, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect('admin/registeredUsers');
        }
    }

    /**
     * Display a Trashed listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashedlist(Request $request) {
        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('registeredUsers-delete');
        $RegisteredUser = RegisteredUser::
                        select('id', 'name', 'deleted_at')
                        ->onlyTrashed()->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($RegisteredUser)
                            ->editColumn('deleted_at', function ($RegisteredUser) {
                                $newYear = new Carbon($RegisteredUser->deleted_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('action', function ($RegisteredUser) {
                                if ($this->DeleteAccess)
                                    return '<a  class="btn btn-success tooltip-primary btn-small restore" data-id="' . $RegisteredUser->id . '"  data-toggle="tooltip" data-placement="top" title="Restore Record" data-original-title="Restore Record"><i class="entypo-ccw"></i></a>';
                                           // . '<a  class="btn btn-danger tooltip-primary btn-small delete" data-id="' . $RegisteredUser->id . '"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete Record" style="margin-left:10px;"><i class="entypo-cancel"></i></a>';
                            })
                            ->make();
        }

        return view('admin.registeredUsers.trashedlist')->with('DeleteAccess', $this->DeleteAccess);
    }

    /**
     * ForceDelete Record.
     *
     * @param  int  $ids
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('registeredUsers-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            //Delete profile image for vendor
            $RegisteredUser = RegisteredUser::withTrashed()->find($id);

            $destinationPath = public_path('registeredUsers_images/');
            if (!empty($RegisteredUser)) {
                if (file_exists($destinationPath . $RegisteredUser->profile_image) && $RegisteredUser->profile_image != '') {
                    @unlink($destinationPath . $RegisteredUser->profile_image);
                }
            }
            //logActivity
            //fetch title                        
            $groupname = $RegisteredUser->name;

            LogActivity::addToLog('RegisteredUser - ' . $groupname, 'deleted');

            RegisteredUser::onlyTrashed()->where('id', '=', $id)->forceDelete();
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
        $this->DeleteAccess = Permit::AccessPermission('registeredUsers-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {

            //logActivity
            //fetch title
            $RegisteredUser = RegisteredUser::withTrashed()->find($id);
            $groupname = $RegisteredUser->name;

            LogActivity::addToLog('RegisteredUser - ' . $groupname, 'restore');
            RegisteredUser::withTrashed()->find($id)->restore();

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
        $this->DeleteAccess = Permit::AccessPermission('registeredUsers-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $all_data = $request->except('_token', 'table-4_length');
        //logActivity
        //fetch title
        $RegisteredUser = RegisteredUser::select('name')
                ->whereIn('id', $all_data['ids'])
                ->get();

        $name = $RegisteredUser->pluck('name');
        $groupname = $name->toJson();

        LogActivity::addToLog('RegisteredUser - ' . $groupname, 'trashed');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            //Check if records exist in payment details
            $RegisteredUser = RegisteredUser::findOrFail($id);
            if ($RegisteredUser->paymentdetail($id) == 0) {
                RegisteredUser::destroy($id);
                // redirect
                Session::flash('message', config('global.trashedRecords'));
            } else {
                // redirect
                Session::flash('error', config('global.relationExist'));
            }
        }

        return redirect('admin/registeredUsers');
    }

    //pacakge History
    public function packageHistory($id) {

        //Get Subscriber name
        $username = DB::table('registered_users')
                ->select('registered_users.name')
                ->where('registered_users.id', $id)
                ->first();

        //Sum Amount
        $Amount = DB::table('subscribers_package_details AS spd')
                ->where(array('spd.subscriber_id' => $id))
                ->sum('price');

        //Get all Archived Subscriber History
        $packageHistory = DB::table('subscribers_package_details AS spd')
                ->select('spd.name_en', 'spd.price', 'spd.start_date', 'spd.end_date', 'spd.num_points', 'spd.num_days', 'spd.payment_id')
                //->whereDate('spd.end_date', '<', date('Y-m-d'))
                ->where(array('spd.subscriber_id' => $id))
                ->get();


        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($packageHistory)
                            ->editColumn('start_date', function ($packageHistory) {
                                $newYear = new Carbon($packageHistory->start_date);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('end_date', function ($packageHistory) {
                                $newYear = new Carbon($packageHistory->end_date);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('num_points', function ($packageHistory) {
                                return $packageHistory->num_points == 0 ? 'Unlimited' : $packageHistory->num_points;
                            })
                            ->editColumn('action', function ($RegisteredUser) {
                                return '<a  class="btn btn-green tooltip-primary btn-small package_details" data-toggle="modal"  data-original-title="Package Details" title="Package Details"  href="#myModal" data-val="' . $RegisteredUser->payment_id . '"><i class="fa fa-money"></i></a>'
                                        . ' <a  class="btn btn-gold tooltip-primary btn-small owner" data-toggle="modal"  data-original-title="Owner" title="Owner"  href="#myModal2" data-val="' . $RegisteredUser->payment_id . '"><i class="fa fa-user"></i></a>';
                            })
                            ->make();
        }

        return view('admin.registeredUsers.packageHistory')
                        ->with('id', $id)
                        ->with('username', $username)
                        ->with('Amount', $Amount);
    }

    public function packagePayment($payment_id) {
        //Get package payment details
        $payment = DB::table('payment_details')
                ->select('reference_id', 'amount', 'post_date', 'result', DB::raw('(CASE WHEN card_type = 1 THEN "KNET" ELSE "Credit Card" END) AS card_type'))
                ->where(array('id' => $payment_id))
                ->first();



        //Change Start Date Format
        $newdate = new Carbon($payment->post_date);
        $payment->post_date = $newdate->format('d/m/Y');

        $returnHTML = view('admin.registeredUsers.packagePayment')->with('payment', $payment)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

    public function ownerDetail($payment_id) {

        //Get package owner detail
        $owner = DB::table('subscribers_package_details AS spd')
                ->select('spd.vendor_id', 'spd.trainer_id')
                ->where(array('spd.payment_id' => $payment_id))
                ->first();

        //Check Vendor
        if (!is_null($owner->vendor_id)) {

            $ownerDetail = DB::table('vendors')
                    ->select('name', 'mobile')
                    ->where(array('id' => $owner->vendor_id))
                    ->first();
        }

        //Check Trainer
        if (!is_null($owner->trainer_id)) {

            $ownerDetail = DB::table('trainers')
                    ->select('name', 'mobile')
                    ->where(array('id' => $owner->trainer_id))
                    ->first();
        }


        $returnHTML = view('admin.registeredUsers.ownerDetail')->with('ownerDetail', $ownerDetail)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

}
