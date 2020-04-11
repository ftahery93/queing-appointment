<?php

namespace App\Http\Controllers\Vendor\Module2;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Input;
use Carbon\Carbon;
use App\Models\Vendor\ClassMaster;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;
use App\Helpers\VendorDetail;
use App\Helpers\Cron;
use App\Mail\fitflowSeatApproval;
use App\Mail\classApprovalEmail;
use App\Models\Admin\Vendor;
use Mail;
use DateTime;

class ClassMasterController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;
    protected $guard = 'vendor';
    protected $configName;

    public function __construct() {
        $this->middleware($this->guard);
        $this->middleware('vendorPermission:classMaster');
        $this->configName = config('global.fitflowVendor') . config('global.storeAddress') . '/' . config('global.M2');
        //Update Cron
        Cron::moveClassSeats();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {


        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('classMaster-create');

//Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('classMaster-delete');

//Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('classMaster-edit');

        $ClassMaster = ClassMaster::
                select('id', 'name_en', 'status', 'created_at')
                ->where('vendor_id', VendorDetail::getID())
                ->get();


        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($ClassMaster)
                            ->editColumn('created_at', function ($ClassMaster) {
                                $newYear = new Carbon($ClassMaster->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('status', function ($ClassMaster) {
                                return $ClassMaster->status == 1 ? '<div class="label label-success status" sid="' . $ClassMaster->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary status"  sid="' . $ClassMaster->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('id', function ($ClassMaster) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $ClassMaster->id . '">';
                            })
                            ->editColumn('action', function ($ClassMaster) {
                                $str = '';
                                if ($this->EditAccess)
                                    $str .= '<a href="' . url($this->configName . '/classMaster') . '/' . $ClassMaster->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>';

                                if ($ClassMaster->status != 0)
                                    $str .= ' <a href="' . url($this->configName) . '/' . $ClassMaster->id . '/classes' . '" class="btn btn-primary tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Branch wise Class" data-original-title="Branch wise Class"><i class="entypo-doc-text-inv"></i></a>';

                                return $str;
                            })
                            ->make();
        }

        return view('fitflowVendor.module2.classMaster.index')
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
        $this->CreateAccess = Permit::AccessPermission('classMaster-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        //Get all Areas
        $activities = DB::table('activities')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();


        return view('fitflowVendor.module2.classMaster.create')
                        ->with('activities', $activities);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        // validate

        $validator = Validator::make($request->only(['vendor_id', 'name_en', 'name_ar', 'activities']), [
                    'vendor_id' => 'required',
                    'name_en' => 'required',
                    'name_ar' => 'required',
                    'activities' => 'required|array|min:1'
        ]);


        // validation failed
        if ($validator->fails()) {

            return redirect($this->configName . '/classMaster/create')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->all();

            //Activities
            $request->activities = array_filter($request->activities);
            if (!empty($request->activities)) {
                $collection = collect($request->activities);
                $input['activities'] = $collection->toJson();
            } else {
                $input['activities'] = '';
            }


            $id = ClassMaster::create($input)->id;


            //logActivity
            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Class - ' . $request->name_en, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect($this->configName . '/classMaster');
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
    public function edit(Request $request, $id) {
        $id = $request->id;
        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('classMaster-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        $ClassMaster = ClassMaster::find($id);

        //Get all Areas
        $activities = DB::table('activities')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();

        $collection = collect(json_decode($ClassMaster->activities, true));

        // show the edit form and pass the nerd
        return View::make('fitflowVendor.module2.classMaster.edit')
                        ->with('ClassMaster', $ClassMaster)
                        ->with('collection', $collection)
                        ->with('activities', $activities);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $id = $request->id;
        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('classMaster-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            $ClassMaster = ClassMaster::findOrFail($id);
            $ClassMaster->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $ClassMaster = ClassMaster::findOrFail($id);
        // validate

        $validator = Validator::make($request->only(['vendor_id', 'name_en', 'name_ar', 'activities']), [
                    'vendor_id' => 'required',
                    'name_en' => 'required',
                    'name_ar' => 'required',
                    'activities' => 'required|array|min:1'
        ]);

        // validation failed
        if ($validator->fails()) {
            return redirect($this->configName . '/classMaster/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->all();

            //Activities
            $request->activities = array_filter($request->activities);
            if (!empty($request->activities)) {
                $collection = collect($request->activities);
                $input['activities'] = $collection->toJson();
            } else {
                $input['activities'] = '';
            }


            $ClassMaster->fill($input)->save();

            //logActivity
            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Class - ' . $request->name_en, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect($this->configName . '/classMaster');
        }
    }

    /**
     * Remove the Multiple resource from storage.
     *
     * @param  int  $ids
     * @return \Illuminate\Http\Response
     */
    public function destroyMany(Request $request) {
        //$error = array();
        $this->bookingTable = VendorDetail::getPrefix() . 'bookings';
        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('classMaster-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $all_data = $request->except('_token', 'table-4_length');

        //logActivity
        //fetch title
        $ClassMaster = ClassMaster::
                select('name_en')
                ->whereIn('id', $all_data['ids'])
                ->get();

        $name = $ClassMaster->pluck('name_en');
        $groupname = $name->toJson();

         LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Class - ' . $groupname, 'deleted');
         
        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            //To delete record, check is it exist in booking table and vendor booking table
            if (!DB::table($this->bookingTable)->where('class_master_id', '=', $id)->exists() && !DB::table('bookings')->where('class_master_id', '=', $id)->exists()) {                
                ClassMaster::destroy($id);
                Session::flash('message', config('global.deletedRecords'));
            } else {
                //array_push($error, $id);
                Session::flash('error', config('global.deleteclassRecords'));
            }
        }



        return redirect($this->configName . '/classMaster');
    }

}
