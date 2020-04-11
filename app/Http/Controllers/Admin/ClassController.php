<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use DateTime;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Models\Vendor\Classes;

class ClassController extends Controller {

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:classes');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function module2ClassSchedules(Request $request) {

        $classes_list = DB::table('classes')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->select('class_master.name_en', 'classes.class_master_id AS id')
                ->where(array('classes.status' => 1, 'class_master.status' => 1))
                ->groupby('classes.class_master_id');

        $class_schedules_list = DB::table('class_schedules')
                ->join('classes', 'classes.id', '=', 'class_schedules.class_id')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->leftjoin('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select(DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS class_name'), DB::raw('CONCAT(class_schedules.schedule_date, " ", class_schedules.start) AS start'), DB::raw('CONCAT(class_schedules.schedule_date, " ", class_schedules.end) AS end'), 'class_schedules.id'
                        , 'class_schedules.gym_seats As total_seats', 'class_schedules.booked', 'class_schedules.app_booked')
                ->where(array('classes.status' => 1, 'class_master.status' => 1));

           $str = '';

        //Ajax request
        if (request()->ajax()) {

            if ($request->class_id != 0) { //Class
                $class_schedules_list->where('classes.class_master_id', $request->class_id);
            } if ($request->vendor_id != 0) {  //Vendor
                $class_schedules_list->where('classes.vendor_id', $request->vendor_id);
                $classes_list->where('class_master.vendor_id', $request->vendor_id);
            }

            $class_schedules_list->havingRaw('total_seats > 0');
            $class_schedules = $class_schedules_list->get();
            $classes = $classes_list->get();

            //change class dropdown list
            $str .= '<option value="0">--All--</option>';
            foreach ($classes as $class) {
                $str .= '<option value="' . $class->id . '">' . $class->name_en . '</option>';
            }
            

            $module_id = 2;

            $returnHTML = view('admin.classes.ajaxSchedules')
                            ->with('module_id', $module_id)
                            ->with('class_schedules', $class_schedules)->render();
            return response()->json(array('success' => true, 'html' => $returnHTML, 'classes' => $str));
        }

        $class_schedules_list->havingRaw('total_seats > 0');
        $class_schedules = $class_schedules_list->get();
        $classes = $classes_list->get();

        $Vendors = DB::table('vendors')
                ->select('name', 'id')
                ->where('status', 1)
                ->whereNull('vendors.deleted_at')
                ->get();

        return View::make('admin.classes.module2ClassSchedules')
                        ->with('class_schedules', $class_schedules)
                        ->with('classes', $classes)
                        ->with('Vendors', $Vendors);
    }

    public function module3ClassSchedules(Request $request) {

        $classes_list = DB::table('classes')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->select('class_master.name_en', 'classes.class_master_id AS id')
                ->where(array('classes.status' => 1, 'class_master.status' => 1))
                ->groupby('classes.class_master_id');

        $class_schedules_list = DB::table('class_schedules')
                ->join('classes', 'classes.id', '=', 'class_schedules.class_id')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->leftjoin('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select(DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS class_name'), DB::raw('CONCAT(class_schedules.schedule_date, " ", class_schedules.start) AS start'), DB::raw('CONCAT(class_schedules.schedule_date, " ", class_schedules.end) AS end'), 'class_schedules.id', 'class_schedules.fitflow_seats As total_seats', 'class_schedules.booked', 'class_schedules.app_booked')
                ->where(array('classes.status' => 1, 'class_master.status' => 1));

         $str = '';

        //Ajax request
        if (request()->ajax()) {

            if ($request->class_id != 0) { //Class
                $class_schedules_list->where('classes.class_master_id', $request->class_id);
            } if ($request->vendor_id != 0) {  //Vendor
                $class_schedules_list->where('classes.vendor_id', $request->vendor_id);
                $classes_list->where('class_master.vendor_id', $request->vendor_id);
            }

            $class_schedules_list->havingRaw('total_seats > 0');
            $class_schedules = $class_schedules_list->get();
             $classes = $classes_list->get();

            $module_id = 3;
            
            //change class dropdown list
            $str .= '<option value="0">--All--</option>';
            foreach ($classes as $class) {
                $str .= '<option value="' . $class->id . '">' . $class->name_en . '</option>';
            }

            $returnHTML = view('admin.classes.ajaxSchedules')->with('module_id', $module_id)->with('class_schedules', $class_schedules)->render();
            return response()->json(array('success' => true, 'html' => $returnHTML, 'classes' => $str));
        }

        $class_schedules_list->havingRaw('total_seats > 0');
        $class_schedules = $class_schedules_list->get();
         $classes = $classes_list->get();

        $Vendors = DB::table('vendors')
                ->select('name', 'id')
                ->where('status', 1)
                 ->whereNull('vendors.deleted_at')
                ->get();

        return View::make('admin.classes.module3ClassSchedules')
                        ->with('class_schedules', $class_schedules)
                        ->with('classes', $classes)
                        ->with('Vendors', $Vendors);
    }

    public function classDetail(Request $request) {

        $class_schedules_list = DB::table('class_schedules')
                ->join('classes', 'classes.id', '=', 'class_schedules.class_id')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->leftjoin('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select(DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS class_name'), 'class_schedules.start', 'class_schedules.end', 'classes.num_seats As total_seats', 'class_schedules.app_booked', 'class_schedules.fitflow_seats', 'class_schedules.schedule_date', 'class_schedules.booked', 'class_schedules.gym_seats')
                ->where('class_schedules.id', $request->id);

        $class_schedule = $class_schedules_list->first();

        //Change Start Date Format
        $newdate = new Carbon($class_schedule->schedule_date);
        $class_schedule->schedule_date = $newdate->format('d/m/Y');

        $sdate = new Carbon($class_schedule->start);
        $class_schedule->start = $sdate->format('h:i:A');

        $edate = new Carbon($class_schedule->end);
        $class_schedule->end = $edate->format('h:i:A');

        $module_id = $request->module_id;


        $returnHTML = view('admin.classes.classDetail')->with('module_id', $module_id)->with('class_schedule', $class_schedule)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

}
