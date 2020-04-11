<?php

namespace App\Http\Controllers\Vendor\Module2;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Input;
use Carbon\Carbon;
use App\Models\Vendor\Classes;
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

class ClassController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;
    protected $guard = 'vendor';
    protected $configName;

    public function __construct() {
        $this->middleware($this->guard);
        $this->middleware('vendorPermission:classes');
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

        $class_master_id = $request->class_master_id;

        //Get Class name
        $className = DB::table('class_master')
                ->select('name_en', 'status')
                ->where('id', $request->class_master_id)
                ->first();

        //check class master record with status
        if ($className->status == 0)
            return redirect($this->configName . '/classMaster');

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('classes-create');

//Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('classes-delete');

//Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('classes-edit');

        $Classes = Classes::
                join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->leftjoin('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select('classes.id', 'vendor_branches.name_en As branch', 'classes.num_seats', 'classes.available_seats', 'classes.fitflow_seats', 'classes.status', 'classes.created_at', 'classes.rating', 'classes.approved_status')
                ->where('classes.vendor_id', VendorDetail::getID())
                ->where('classes.class_master_id', $class_master_id)
                ->get();


        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Classes)
                            ->editColumn('created_at', function ($Classes) {
                                $newYear = new Carbon($Classes->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('status', function ($Classes) {
                                return $Classes->status == 1 ? '<div class="label label-success status" sid="' . $Classes->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary status"  sid="' . $Classes->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('id', function ($Classes) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $Classes->id . '">';
                            })
                            ->editColumn('action', function ($Classes) use($class_master_id) {
                                $str = '';
                                if ($this->EditAccess)
                                    $str .= '<a href="' . url($this->configName) . '/' . $class_master_id . '/classes' . '/' . $Classes->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>'
                                            . ' <a href="' . url($this->configName . '/classSchedules') . '/' . $Classes->id . '" class="btn btn-success tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Schedules" data-original-title="Schedules"><i class="entypo-newspaper"></i></a>';
                                //. ' <a data-id="' . $Classes->id . '" href="#myModal" class="btn btn-orange tooltip-primary btn-small  changeRequest" data-toggle="modal" data-placement="top" title="Change Request" data-original-title="Change Request"><i class="entypo-reply"></i></a>';
                                //. ' <a data-id="' . $Classes->id . '" class="btn btn-orange tooltip-primary btn-small  sendApproval" data-toggle="tooltip" data-placement="top" title="SendEmail" data-original-title="SendEmail"><i class="entypo-mail"></i></a>';

                                if ($Classes->approved_status == 1)
                                    $str .= ' <a href="' . url($this->configName) . '/' . $Classes->id . '/manageSchedule" class="btn btn-danger tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Add Schedules" data-original-title="Add Schedules"><i class="entypo-clock"></i></a>';

                                return $str;
                            })
                            ->make();
        }

        return view('fitflowVendor.module2.classes.index')
                        ->with('classMasterID', $class_master_id)
                        ->with('className', $className->name_en)
                        ->with('CreateAccess', $this->CreateAccess)
                        ->with('DeleteAccess', $this->DeleteAccess)
                        ->with('EditAccess', $this->EditAccess);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        $class_master_id = $request->class_master_id;

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('classes-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        //Get all Gender Types
        $branches = DB::table('vendor_branches')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->where('vendor_id', VendorDetail::getID())
                ->get();

        //Get all Gender Types
        $gender_types = DB::table('gender_types')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();


        //Get Class name
        $className = DB::table('class_master')
                ->select('name_en')
                ->where('id', $request->class_master_id)
                ->first();


        return view('fitflowVendor.module2.classes.create')
                        ->with('classMasterID', $class_master_id)
                        ->with('branches', $branches)
                        ->with('gender_types', $gender_types)
                        ->with('className', $className->name_en);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $class_master_id = $request->class_master_id;


        // validate
        Validator::extend('less_than', function($attribute, $value, $parameters) {
            $other = Input::get($parameters[0]);

            return isset($other) and intval($value) < intval($other);
        });
        $messsages = array(
            'fitflow_seats.less_than' => config('global.lessthanSeatsValidate'),
        );
        $validator = Validator::make($request->only(['vendor_id', 'class_master_id', 'branch_id', 'num_seats', 'available_seats', 'fitflow_seats', 'gender_type', 'hours', 'price']), [
                    'vendor_id' => 'required',
                    'branch_id' => 'required',
                    'class_master_id' => 'required',
                    'num_seats' => 'required|numeric',
                    'available_seats' => 'required|numeric',
                    'fitflow_seats' => 'required|numeric|less_than:num_seats',
                    'gender_type' => 'required',
                    'hours' => 'required|numeric',
                        //'price' => array('required', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/')
                        ], $messsages);


        // validation failed
        if ($validator->fails()) {

            return redirect($this->configName . '/' . $class_master_id . '/classes/create')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->all();



            if ($request->has('num_seats')) {
                $input['available_seats'] = $request->num_seats - $request->fitflow_seats;
                $input['temp_gym_seats'] = $request->num_seats - $request->fitflow_seats;
            } else {
                $input['available_seats'] = 0;
                $input['temp_gym_seats'] = null;
            }

            //temporary column add
            $input['temp_num_seats'] = $input['num_seats'];
            $input['temp_fitflow_seats'] = $input['fitflow_seats'];
            //$input['temp_price'] = $input['price'];
            $input['approved_status'] = 1;

            $id = Classes::create($input)->id;


            //Get Class name
            $branchName = DB::table('vendor_branches')
                    ->select('name_en')
                    ->where('id', $request->branch_id)
                    ->first();

            $className = DB::table('class_master')
                    ->select('name_en')
                    ->where('id', $request->class_master_id)
                    ->first();

            $Classes = Classes::findOrFail($id);
            $Classes->name_en = $className->name_en . '-' . $branchName->name_en;

            //Email for Approval
            $Classes->changeRequest = 1;
            Mail::to(config('mail.from.address'))->send(new fitflowSeatApproval($Classes));


            //logActivity
            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Class - ' . $className->name_en . ' Branch - ' . $branchName->name_en, 'created');


            Session::flash('message', config('global.addedRecords'));

            return redirect($this->configName . '/' . $class_master_id . '/classes');
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
        $class_master_id = $request->class_master_id;
        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('classes-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        $Classes = Classes::find($id);

        //Get all Gender Types
        $branches = DB::table('vendor_branches')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->where('vendor_id', VendorDetail::getID())
                ->get();

        //Get all Gender Types
        $gender_types = DB::table('gender_types')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();

        //Get Class name
        $className = DB::table('class_master')
                ->select('name_en')
                ->where('id', $request->class_master_id)
                ->first();

        // show the edit form and pass the nerd
        return View::make('fitflowVendor.module2.classes.edit')
                        ->with('branches', $branches)
                        ->with('Classes', $Classes)
                        ->with('classMasterID', $class_master_id)
                        ->with('className', $className->name_en)
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
        $id = $request->id;
        $class_master_id = $request->class_master_id;


        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('classes-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            $Classes = Classes::findOrFail($id);
            $Classes->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $Classes = Classes::findOrFail($id);
        // validate

        $validator = Validator::make($request->only(['vendor_id', 'branch_id', 'class_master_id', 'num_seats', 'available_seats', 'fitflow_seats', 'gender_type', 'hours', 'price']), [
                    'vendor_id' => 'required',
                    'class_master_id' => 'required',
                    'branch_id' => 'required',
                    'num_seats' => 'required|numeric',
                    'available_seats' => 'required|numeric',
                    'fitflow_seats' => 'required|numeric',
                    'gender_type' => 'required',
                    'hours' => 'required|numeric',
                        // 'price' => array('required', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/')
        ]);

        // validation failed
        if ($validator->fails()) {
            return redirect($this->configName . '/' . $class_master_id . '/classes/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->all();


            if ($request->has('num_seats')) {
                $input['available_seats'] = $request->num_seats - $request->fitflow_seats;
            } else {
                $input['available_seats'] = 0;
            }
            $input['approved_status'] = 1;


            $Classes->fill($input)->save();

            //Get Class name
            $branchName = DB::table('vendor_branches')
                    ->select('name_en')
                    ->where('id', $request->branch_id)
                    ->first();
            $className = DB::table('class_master')
                    ->select('name_en')
                    ->where('id', $request->class_master_id)
                    ->first();

            //logActivity
            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Class - ' . $className->name_en . ' Branch - ' . $branchName->name_en, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect($this->configName . '/' . $class_master_id . '/classes');
        }
    }

    /**
     * Remove the Multiple resource from storage.
     *
     * @param  int  $ids
     * @return \Illuminate\Http\Response
     */
    public function destroyMany(Request $request) {
        $this->bookingTable = VendorDetail::getPrefix() . 'bookings';
        $class_master_id = $request->class_master_id;

        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('classes-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $all_data = $request->except('_token', 'table-4_length');

        //logActivity
        //fetch title
        $Classes = Classes::
                join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select(DB::raw('CONCAT("Class - ",class_master.name_en, " ", "Branch - ", vendor_branches.name_en) AS name_en'))
                ->whereIn('classes.id', $all_data['ids'])
                ->get();

        $name = $Classes->pluck('name_en');
        $groupname = $name->toJson();

        LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            //To delete record, check is it exist in booking table and vendor booking table
            if (!DB::table($this->bookingTable)->where('class_id', '=', $id)->exists() && !DB::table('bookings')->where('class_id', '=', $id)->exists()) {
                Classes::destroy($id);
                Session::flash('message', config('global.deletedRecords'));
            } else {
                //array_push($error, $id);
                Session::flash('error', config('global.deleteclassRecords'));
            }
        }


        return redirect($this->configName . '/' . $class_master_id . '/classes');
    }

    public function sendApproval(Request $request, $id) {
        $id = $request->id;
        $Classes = Classes::findOrFail($id);

        //Get class name
        $className = Classes::
                join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select(DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS name_en'))
                ->whereIn('classes.id', $id)
                ->first();

        $Classes->name_en = $className->name_en;

        //Ajax request
        if (request()->ajax()) {

            //Email for Approval
            $Classes->changeRequest = 1;
            Mail::to(config('mail.from.address'))->send(new fitflowSeatApproval($Classes));
            return response()->json(['response' => config('global.sentEmail')]);
        }
    }

    public function manageSchedule(Request $request) {


        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('classSchedules-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        $classList = DB::table('classes')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select(DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS name_en'), 'classes.id', 'classes.hours')
                ->where('classes.vendor_id', VendorDetail::getID())
                ->where(array('classes.approved_status' => 1, 'classes.status' => 1, 'class_master.status' => 1));

        //Ajax request
        if (request()->ajax()) {
            if ($request->has('id') && $request->get('id') != 0) {
                $ID = $request->get('id');
                $classList->where('classes.branch_id', $ID);
            }
            $classes = $classList->get();
            $returnHTML = view('fitflowVendor.module2.classes.ajaxClassOptions')->with('classes', $classes)->with('class_id', $request->id)->render();
            return response()->json(array('success' => true, 'html' => $returnHTML));
        }

        $classes = $classList->get();

        $classNameList = DB::table('classes')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select(DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS name_en'), 'class_master.id');


        //Get all Branch Types
        $branches = DB::table('vendor_branches')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->where('vendor_id', VendorDetail::getID())
                ->get();

        $class_schedules_list = DB::table('class_schedules')
                ->join('classes', 'classes.id', '=', 'class_schedules.class_id')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select(DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS class_name'), DB::raw('CONCAT(class_schedules.schedule_date, " ", class_schedules.start) AS start'), DB::raw('CONCAT(class_schedules.schedule_date, " ", class_schedules.end) AS end'), 'class_schedules.id')
                ->where(array('classes.vendor_id' => VendorDetail::getID(), 'classes.status' => 1, 'class_master.status' => 1));

        if ($request->id != 0) {
            $class_schedules_list->where('classes.id', $request->id);
            $classNameList->where('classes.id', $request->id);
            $class_id = $request->id;
            $className = $classNameList->first();
        }

        $class_schedules = $class_schedules_list->get();


        if ($request->id == 0) {
            $className = new \stdClass();
            $className->name_en = '';
            $class_id = 0;
            $className->id = 0;
        }


        return View::make('fitflowVendor.module2.classes.manageSchedule')
                        ->with('classes', $classes)
                        ->with('branches', $branches)
                        ->with('class_name', $className)
                        ->with('class_id', $class_id)
                        ->with('class_master_id', $className->id)
                        ->with('class_schedules', $class_schedules);
    }

    public function addSchedule(Request $request) {
        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('classSchedules-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            // validate
            $validator = Validator::make($request->all(), [
                        'class_id' => 'required',
                        'start' => 'required',
                        'end' => 'required',
                        'schedule_date' => 'required',
            ]);

            // validation failed
            if ($validator->fails()) {
                return response()->json(['error' => config('global.errorClassInput')]);
            } else {

                $input = $request->except('_token');

                $input['vendor_id'] = VendorDetail::getID();
                $input['created_at'] = Carbon::now();
                $input['updated_at'] = Carbon::now();

                //Get class name
                $className = Classes::
                        join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                        ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                        ->select(DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS name_en'), 'classes.available_seats', 'classes.fitflow_seats', 'classes.num_seats')
                        ->where('classes.id', $request->class_id)
                        ->first();


                //Multiple Schedule on same time restriction
                if (!DB::table('class_schedules')
                                ->where('class_id', '=', $input['class_id'])
                                ->where('start', '=', $input['start'])
                                ->where('end', '=', $input['end'])
                                ->where('schedule_date', '=', $input['schedule_date'])
                                ->exists()) {

                    $input['gym_seats'] = $className->available_seats;
                    $input['fitflow_seats'] = $className->fitflow_seats;
                    $input['num_seats'] = $className->num_seats;

                    $lastID = DB::table('class_schedules')->insertGetId($input);
                    $input['id'] = $lastID;

                    //logActivity
                    $input['class_name'] = $className->name_en;
                    $input['start'] = $input['schedule_date'] . ' ' . $input['start'];
                    $input['end'] = $input['schedule_date'] . ' ' . $input['end'];
                    LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Class - ' . $className->name_en . '- Schedule', 'created');

                    return response()->json(['response' => config('global.addedRecords'), 'result' => $input]);
                } else {
                    return response()->json(['error' => config('global.deleteScheduleError')]);
                }
            }
        }
    }

    public function deleteSchedule(Request $request) {
        $this->bookingTable = VendorDetail::getPrefix() . 'bookings';
        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('classSchedules-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        //logActivity
        //fetch title
        $class_schedules = DB::table('class_schedules')
                ->join('classes', 'classes.id', '=', 'class_schedules.class_id')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select(DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS class_name'), 'class_schedules.start', 'class_schedules.end')
                ->where('class_schedules.id', $request->id)
                ->first();

        $schedule = $class_schedules->start . ' - ' . $class_schedules->end;

        //To delete record, check is it exist in booking table and vendor booking table
        if (!DB::table($this->bookingTable)->where('schedule_id', '=', $request->id)->exists() && !DB::table('bookings')->where('schedule_id', '=', $request->id)->exists()) {
            DB::table('class_schedules')->delete($request->id);
            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Class ' . $class_schedules->class_name . ' - Schedule ' . $schedule, 'deleted');
            return response()->json(['response' => config('global.deletedRecords')]);
        } else {
            return response()->json(['response' => config('global.deleteclassRecords')]);
        }
    }

    public function schedules(Request $request) {


        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('classSchedules-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        $classes = DB::table('classes')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->select('class_master.name_en', 'classes.class_master_id AS id')
                ->where(array('classes.vendor_id' => VendorDetail::getID(), 'classes.status' => 1, 'class_master.status' => 1))
                ->groupby('classes.class_master_id')
                ->get();

        $class_schedules_list = DB::table('class_schedules')
                ->join('classes', 'classes.id', '=', 'class_schedules.class_id')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select(DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS class_name'), DB::raw('CONCAT(class_schedules.schedule_date, " ", class_schedules.start) AS start'), DB::raw('CONCAT(class_schedules.schedule_date, " ", class_schedules.end) AS end'), 'class_schedules.id', 'class_schedules.gym_seats As total_seats', 'class_schedules.booked', 'class_schedules.app_booked')
                ->where(array('classes.vendor_id' => VendorDetail::getID(), 'classes.status' => 1, 'class_master.status' => 1));


        //Ajax request
        if (request()->ajax()) {

            if ($request->id != 0) {
                $class_schedules_list->where('classes.class_master_id', $request->id);
            }

            $class_schedules = $class_schedules_list->get();

            $returnHTML = view('fitflowVendor.module2.classes.ajaxSchedules')->with('class_schedules', $class_schedules)->render();
            return response()->json(array('success' => true, 'html' => $returnHTML));
        }

        $class_schedules = $class_schedules_list->get();

        return View::make('fitflowVendor.module2.classes.schedules')
                        ->with('class_schedules', $class_schedules)
                        ->with('classes', $classes);
    }

    public function classDetail(Request $request) {


        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('classSchedules-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        $class_schedules_list = DB::table('class_schedules')
                ->join('classes', 'classes.id', '=', 'class_schedules.class_id')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select(DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS class_name'), 'class_schedules.start', 'class_schedules.end', 'class_schedules.num_seats As total_seats', 'class_schedules.booked', 'class_schedules.gym_seats', 'class_schedules.schedule_date')
                ->where('classes.vendor_id', VendorDetail::getID())
                ->where('class_schedules.id', $request->id);

        $class_schedule = $class_schedules_list->first();

        //Change Start Date Format
        $newdate = new Carbon($class_schedule->schedule_date);
        $class_schedule->schedule_date = $newdate->format('d/m/Y');

        $sdate = new Carbon($class_schedule->start);
        $class_schedule->start = $sdate->format('h:i:A');

        $edate = new Carbon($class_schedule->end);
        $class_schedule->end = $edate->format('h:i:A');

        $returnHTML = view('fitflowVendor.module2.classes.classDetail')->with('class_schedule', $class_schedule)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

    //Change Request
    public function changeRequest(Request $request) {

        //Ajax request
        if (request()->ajax()) {

            // validate
            Validator::extend('less_than', function($attribute, $value, $parameters) {
                $other = Input::get($parameters[0]);

                return isset($other) and intval($value) < intval($other);
            });
            $messsages = array(
                'temp_fitflow_seats.less_than' => config('global.lessthanSeatsValidate'),
            );
            // validate
            $validator = Validator::make($request->all(), [
                        'class_id' => 'required',
                        'temp_num_seats' => 'required|numeric',
                        'temp_gym_seats' => 'required|numeric',
                        'temp_fitflow_seats' => 'required|numeric|less_than:temp_num_seats',
                        'temp_price' => array('required', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
                            ], $messsages);

            // validation failed
            if ($validator->fails()) {
                return response()->json(['error' => config('global.errorInput')]);
            } else {

                $input = $request->except('_token', 'class_id');


                $input['approved_status'] = 0;


                DB::table('classes')
                        ->where('id', $request->class_id)
                        ->update($input);

                //logActivity
                $Classes = Classes::findOrFail($request->class_id);

                //Get class name
                $className = Classes::
                        join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                        ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                        ->select(DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS name_en'))
                        ->where('classes.id', $request->class_id)
                        ->first();

                $Classes->name_en = $className->name_en;

                //Email for Approval
                $Classes->changeRequest = 1;
                Mail::to(config('mail.from.address'))->send(new fitflowSeatApproval($Classes));

                LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Class - ' . $Classes->name_en . ' Change Request', 'updated');

                return response()->json(['response' => config('global.updatedRecords')]);
            }
        }
    }

    //Edit Seats
    public function classSchedule(Request $request) {

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('classSchedules-edit');
         $this->DeleteAccess = Permit::AccessPermission('classSchedules-delete');

        $Classes = DB::table('class_schedules')
                ->join('classes', 'classes.id', '=', 'class_schedules.class_id')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select('class_schedules.id', 'class_schedules.id AS ID', DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS class_name'), 'class_schedules.start', 'class_schedules.end', 'class_schedules.schedule_date', 'class_schedules.num_seats', 'class_schedules.fitflow_seats', 'class_schedules.gym_seats'
                        , 'classes.fitflow_seats AS class_fitflow_seats', 'classes.available_seats')
                ->where(array('classes.vendor_id' => VendorDetail::getID(), 'classes.status' => 1, 'class_master.status' => 1))
                ->where('classes.id', $request->id)
                ->get();


        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Classes)
                            ->editColumn('schedule_date', function ($Classes) {
                                $newYear = new Carbon($Classes->schedule_date);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('start', function ($Classes) {
                                $newYear = new Carbon($Classes->start);
                                return $newYear->format('h:i:A');
                            })
                            ->editColumn('end', function ($Classes) {
                                $newYear = new Carbon($Classes->end);
                                return $newYear->format('h:i:A');
                            })
                            ->editColumn('fitflow_seats', function ($Classes) {
                                return '<input type="tel" class="form-control number_only text-center fitflow_seats" autocomplete="off"  value="' . $Classes->fitflow_seats . '"'
                                        . '  name="fitflow_seats" data-val="' . $Classes->class_fitflow_seats . '"  total="' . $Classes->num_seats . '">'
                                        . '<input type="hidden" value="' . $Classes->num_seats . '" name="total_seats" class="total_seats">'
                                        . '<input type="hidden" value="' . $Classes->class_fitflow_seats . '" name="class_fitflow_seats" class="class_fitflow_seats">';
                            })
                            ->editColumn('gym_seats', function ($Classes) {
                                return '<input type="tel" class="form-control number_only text-center gym_seats"  autocomplete="off"  value="' . $Classes->gym_seats . '" name="gym_seats"'
                                        . 'data-val="' . $Classes->available_seats . '"  total="' . $Classes->num_seats . '">';
                            })
                            ->editColumn('id', function ($Classes) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids" value="' . $Classes->id . '">';
                            })
                            ->setRowId(function ($Classes) {
                                return $Classes->id;
                            })
                            ->make();
        }


        return view('fitflowVendor.module2.classes.classSchedules')
                        ->with('EditAccess', $this->EditAccess)
                        ->with('DeleteAccess', $this->DeleteAccess)
                        ->with('class_id', $request->id);
    }

    public function editSchedule(Request $request) {

        $arrayData = $request->jsonData;
        $arrayData = array_filter($arrayData);
        // dd($arrayData);
        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('classes-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');
        //Ajax request
        if (request()->ajax()) {
            $error = array();

            foreach ($arrayData as $val) {

                $input['fitflow_seats'] = $val['fitflow_seats'];
                $input['gym_seats'] = $val['gym_seats'];
                $totalSeats = $val['fitflow_seats'] + $val['gym_seats'];

                // if (($val['fitflow_seats'] < $val['class_fitflow_seats']) || ($val['fitflow_seats'] > $val['total_seats']) || ($val['gym_seats'] > $val['total_seats']) || ($totalSeats != $val['total_seats'])) {
                if (($val['fitflow_seats'] > $val['total_seats']) || ($val['gym_seats'] > $val['total_seats']) || ($totalSeats != $val['total_seats'])) {
                    array_push($error, $val['ids']);
                } else {
                    DB::table('class_schedules')
                            ->where('id', $val['ids'])
                            ->update($input);
                }
            }
            if ($error) {
                return response()->json(['error' => 'Schedule ID# ' . json_encode($error) . ' cannot be updated']);
            } else {
                //logActivity
                LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Class - Schedule ', 'updated');

                return response()->json(['response' => config('global.updatedRecords')]);
            }
        }
    }

    public function rejectedClasses(Request $request) {

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('classes-edit');

        $Classes = DB::table('classes')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select('classes.id', DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS name_en'), 'classes.temp_num_seats', 'classes.temp_gym_seats', 'classes.temp_fitflow_seats', 'classes.temp_price', 'classes.reason')
                ->where(array('classes.vendor_id' => VendorDetail::getID(), 'classes.status' => 1, 'class_master.status' => 1, 'classes.approved_status' => 2))
                ->get();


        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Classes)
                            ->editColumn('temp_fitflow_seats', function ($Classes) {
                                return '<input type="tel" class="form-control number_only text-center temp_fitflow_seats" autocomplete="off"  value="' . $Classes->temp_fitflow_seats . '"'
                                        . '  name="temp_fitflow_seats"   total="' . $Classes->temp_num_seats . '">'
                                        . '<input type="hidden" value="' . $Classes->temp_num_seats . '" name="total_seats" class="total_seats">';
                            })
                            ->editColumn('temp_gym_seats', function ($Classes) {
                                return '<input type="tel" class="form-control number_only text-center temp_gym_seats"  autocomplete="off"  value="' . $Classes->temp_gym_seats . '" name="temp_gym_seats"'
                                        . ' total="' . $Classes->temp_num_seats . '" '
                                        . 'readonly="readonly">';
                            })
                            ->editColumn('temp_price', function ($Classes) {
                                return '<input type="tel" class="form-control number_only text-center temp_price"  autocomplete="off"  value="' . $Classes->temp_price . '" name="temp_price">';
                            })
                            ->editColumn('id', function ($Classes) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids" value="' . $Classes->id . '">';
                            })
                            ->setRowId(function ($Classes) {
                                return $Classes->id;
                            })
                            ->make();
        }

        return view('fitflowVendor.module2.classes.rejectedClasses')
                        ->with('EditAccess', $this->EditAccess);
    }

    public function editRejectedClasses(Request $request) {

        $arrayData = $request->jsonData;
        $arrayData = array_filter($arrayData);
        // dd($arrayData);
        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('classes-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');
        //Ajax request
        if (request()->ajax()) {
            $error = array();
            $ids = array();

            foreach ($arrayData as $val) {

                $input['temp_fitflow_seats'] = $val['temp_fitflow_seats'];
                $input['temp_gym_seats'] = $val['temp_gym_seats'];
                $input['temp_price'] = $val['temp_price'];
                $input['approved_status'] = 0;
                $totalSeats = $val['temp_fitflow_seats'] + $val['temp_gym_seats'];

                if ($totalSeats <= $val['total_seats']) {

                    DB::table('classes')
                            ->where('id', $val['ids'])
                            ->update($input);

                    //Get  ids for Approval
                    array_push($ids, $val['ids']);
                } else {
                    array_push($error, $val['ids']);
                }
            }
            if ($error) {
                return response()->json(['error' => 'Classes ID# ' . json_encode($error) . 'cannot be updated']);
            } else {

                $vendorClasses = Vendor
                                ::select('name', 'id', 'email')
                                ->where('id', VendorDetail::getID())->first();


                $jsonID = json_encode($ids);
                $vendorClasses->jsonID = $jsonID;

                Mail::to($vendorClasses->email)->send(new classApprovalEmail($vendorClasses));

                LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Rejected Classes ', 'updated');

                return response()->json(['response' => config('global.updatedRecords')]);
            }
        }
    }

    //Branch wise Classes
    public function classBranch(Request $request) {

        $class_master_id = $request->class_master_id;


        $ClassMasters = DB::table('class_master')
                ->select('name_en', 'id')
                ->where('vendor_id', VendorDetail::getID())
                ->get();



        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('classes-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');


        $ClassList = Classes::
                join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->leftjoin('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select('class_master.name_en As classname', 'vendor_branches.name_en As branch', 'classes.num_seats', 'classes.available_seats', 'classes.fitflow_seats', 'classes.created_at', 'classes.rating')
                ->where('classes.vendor_id', VendorDetail::getID())
                ->where('class_master.status', 1);

        // if Request having Class id
        if ($request->has('class_master_id') && $request->get('class_master_id') != 0) {
            $ID = $request->get('class_master_id');
            $ClassList->where('class_master.id', $ID);
        }

        $Classes = $ClassList->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Classes)
                            ->editColumn('created_at', function ($Classes) {
                                $newYear = new Carbon($Classes->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->make();
        }

        return view('fitflowVendor.module2.classes.classBranch')->with('ClassMasters', $ClassMasters);
    }

    public function addWeeklySchedule(Request $request) {

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('classSchedules-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            // validate
            $validator = Validator::make($request->all(), [
                        'class_id' => 'required',
                        'start_date' => 'date_format:d/m/Y',
                        'end_date' => 'date_format:d/m/Y|after:start_date',
                        'start_time' => 'required',
                        'hour' => 'required',
                        'week_day' => 'required',
            ]);

            // validation failed
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->all()]);
            } else {

                $input = $request->except('_token');

                $datetime = new DateTime();
                $newDate = $datetime->createFromFormat('d/m/Y', $request->start_date);
                $input['start_date'] = $newDate->format('Y-m-d');

                $endDate = $datetime->createFromFormat('d/m/Y', $request->end_date);
                $input['end_date'] = $endDate->format('Y-m-d');

                $startTime = $datetime->createFromFormat('h:i:A', $request->start_time);
                $input['start'] = $startTime->format('H:i:s');
                
                $exp = new Carbon($input['start']);
                $exp->addMinutes($input['hour']);
                $input['end'] = $exp->format('H:i:s');
                
                
                //Get All Weekly Dates
                $weekly = [];
                $fromDate = $input['start_date'];
                $toDate = $input['end_date'];

                $week_array['vendor_id'] = VendorDetail::getID();
                $week_array['created_at'] = Carbon::now();
                $week_array['updated_at'] = Carbon::now();
                $week_array['class_id'] = $input['class_id'];
                $week_array['start'] = $input['start'];
                $week_array['end'] = $input['end'];


                //dd($weekly);
                //Get class name
                $className = Classes::
                        join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                        ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                        ->select(DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS name_en'), 'classes.available_seats', 'classes.fitflow_seats', 'classes.num_seats')
                        ->where('classes.id', $request->class_id)
                        ->first();

                $week_array['gym_seats'] = $className->available_seats;
                $week_array['fitflow_seats'] = $className->fitflow_seats;
                $week_array['num_seats'] = $className->num_seats;

                if ($input['week_day'] != 0) {  //Insert Data Every Week
                    $day = "first " . strtolower($input['week_day']) . " of this month";
                    $startDate = Carbon::parse($day);
                    $endDate = Carbon::parse($toDate);

                    for ($date = $startDate; $date->lte($endDate); $date->addWeek()) {
                        // $weekly[] = $date->format('Y-m-d');
                        $week_array['schedule_date'] = $date->format('Y-m-d');
                        $lastID = DB::table('class_schedules')->insertGetId($week_array);
                    }
                } else {  //Insert Data Per Day
                    $startDate = Carbon::parse($fromDate);
                    $endDate = Carbon::parse($toDate);
                    for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                        $week_array['schedule_date'] = $date->format('Y-m-d');
                        $lastID = DB::table('class_schedules')->insertGetId($week_array);
                    }
                }


                //logActivity                   
                LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Class - ' . $className->name_en . '- Schedule', 'created');

                return response()->json(['response' => config('global.addedRecords')]);
            }
        }
    }

    public function deleteMultiSchedule(Request $request) {

        $arrayData = $request->jsonData;
        $arrayData = array_filter($arrayData);

        $this->bookingTable = VendorDetail::getPrefix() . 'bookings';
        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('classSchedules-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');


        //Ajax request
        if (request()->ajax()) {
            $error = array();

            foreach ($arrayData as $val) {

                if (($val['ids'] == 0)) {
                    array_push($error, $val['ids']);
                } else {

                    //logActivity
                    //fetch title
                    $class_schedules = DB::table('class_schedules')
                            ->join('classes', 'classes.id', '=', 'class_schedules.class_id')
                            ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                            ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                            ->select(DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS class_name'), 'class_schedules.start', 'class_schedules.end')
                            ->where('class_schedules.id', $val['ids'])
                            ->first();

                    $schedule = $class_schedules->start . ' - ' . $class_schedules->end;

                    LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Class ' . $class_schedules->class_name . ' - Schedule ' . $schedule, 'deleted');
                    //To delete record, check is it exist in booking table and vendor booking table
                    if (!DB::table($this->bookingTable)->where('schedule_id', '=', $val['ids'])->exists() && !DB::table('bookings')->where('schedule_id', '=', $val['ids'])->exists()) {
                        DB::table('class_schedules')->delete($val['ids']);
                        LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Class ' . $class_schedules->class_name . ' - Schedule ' . $schedule, 'deleted');
                    } else {
                        array_push($error, $val['ids']);
                    }
                }
            }
            if ($error) {
                return response()->json(['error' => 'Schedule ID# ' . json_encode($error) . ' cannot be deleted']);
            } else {
                return response()->json(['response' => config('global.deletedRecords')]);
            }
        }
    }

}
