<?php

namespace App\Http\Controllers\Vendor\Module3;

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
use App\Mail\fitflowSeatApproval;
use Mail;
use DateTime;

class ClassController extends Controller {

    protected $ViewAccess;
    protected $guard = 'vendor';
    protected $configName;

    public function __construct() {
        $this->middleware($this->guard);
        $this->middleware('vendorPermission:M3classSchedules');
        $this->configName = config('global.fitflowVendor') . config('global.storeAddress') . '/' . config('global.M3');
    }

    public function schedules(Request $request) {


        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('M3classSchedules-view');
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
                ->leftjoin('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select(DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS class_name'), DB::raw('CONCAT(class_schedules.schedule_date, " ", class_schedules.start) AS start'), DB::raw('CONCAT(class_schedules.schedule_date, " ", class_schedules.end) AS end'), 'class_schedules.id',
                        'class_schedules.fitflow_seats As total_seats', 'class_schedules.booked', 'class_schedules.app_booked')
                ->where(array('classes.vendor_id' => VendorDetail::getID(), 'classes.status' => 1, 'class_master.status' => 1))
                ->havingRaw('total_seats > 0');


        //Ajax request
        if (request()->ajax()) {

            if ($request->id != 0) {
                $class_schedules_list->where('classes.class_master_id', $request->id);
            }

            $class_schedules = $class_schedules_list->get();

            $returnHTML = view('fitflowVendor.module3.classes.ajaxSchedules')->with('class_schedules', $class_schedules)->render();
            return response()->json(array('success' => true, 'html' => $returnHTML));
        }

        $class_schedules = $class_schedules_list->get();

        return View::make('fitflowVendor.module3.classes.schedules')
                        ->with('class_schedules', $class_schedules)
                        ->with('classes', $classes);
    }

    public function classDetail(Request $request) {


        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('M3classSchedules-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        $class_schedules_list = DB::table('class_schedules')
                ->join('classes', 'classes.id', '=', 'class_schedules.class_id')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->leftjoin('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select(DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS class_name'), 'class_schedules.start', 'class_schedules.end', 'classes.num_seats As total_seats', 'class_schedules.app_booked', 'class_schedules.fitflow_seats', 'class_schedules.schedule_date')
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

        $returnHTML = view('fitflowVendor.module3.classes.classDetail')->with('class_schedule', $class_schedule)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

}
