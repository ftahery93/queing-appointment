<?php

namespace App\Http\Controllers\Vendor\Module1;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Input;
use Carbon\Carbon;
use App\Models\Vendor\InstructorSubscription;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;
use App\Helpers\VendorDetail;
use DateTime;

class InstructorSubscriptionController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;
    protected $guard = 'vendor';
    protected $configName;

    public function __construct() {
        $this->middleware($this->guard);
        $this->middleware('vendorPermission:instructorSubscriptions');
        $this->configName = config('global.fitflowVendor') . config('global.storeAddress') . '/' . config('global.M1');
    }

    public function index(Request $request) {

        $this->instructorSubscriptionTable = 'instructor_subscribers_package_details';

        $InstructorSubscription = DB::table($this->instructorSubscriptionTable)
                ->select('name_en', 'price', 'package_id')
                ->where('vendor_id', VendorDetail::getID())
                ->whereColumn('num_booked', '!=', 'num_points')
                ->groupby('package_id')
                ->get();


        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($InstructorSubscription)
                            ->editColumn('action', function ($InstructorSubscription) {
                                return '<a href="' . url($this->configName . '/instructorSubscriptions') . '/' . $InstructorSubscription->package_id . '/subscribers" class="btn btn-primary tooltip-primary btn-small" '
                                        . 'data-toggle="tooltip" data-placement="top" title="View Subscribers" data-original-title="View Subscribers"><i class="entypo-users"></i></a>';
                            })
                            ->make();
        }

        return view('fitflowVendor.module1.instructorSubscriptions.index');
    }

    public function subscribers(Request $request) {

        $this->instructorSubscriptionTable = 'instructor_subscribers_package_details';
        $package_id = $request->package_id;
        $this->table = VendorDetail::getPrefix() . 'members';

        $InstructorSubscriptionList = DB::table($this->instructorSubscriptionTable . ' As ins')
                ->join($this->table.' As registered_users', 'ins.member_id', '=', 'registered_users.id')
                ->select('ins.id AS subscription_package_id', 'registered_users.name AS subscriber', 'registered_users.mobile', 'ins.name_en As package_name', 'ins.price', 'ins.num_points', 'ins.num_booked', 'ins.created_at')
                ->where('ins.vendor_id', VendorDetail::getID())
                ->whereColumn('ins.num_booked', '!=', 'ins.num_points')
                ->where('ins.package_id', $package_id);

        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $InstructorSubscriptionList->whereBetween('ins.created_at', [$start_date, $end_date]);
        }

        // if Request having Member id
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            $InstructorSubscriptionList->where('ins.member_id', $ID);
        }

        $InstructorSubscription = $InstructorSubscriptionList->get();
        $Count = $InstructorSubscriptionList->count();


        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($InstructorSubscription)
                            ->editColumn('created_at', function ($InstructorSubscription) {
                                $newYear = new Carbon($InstructorSubscription->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('subscription_package_id', function ($InstructorSubscription) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $InstructorSubscription->subscription_package_id . '">';
                            })
//                            ->editColumn('action', function ($InstructorSubscription) {
//                                return '<a data-val="' . $InstructorSubscription->subscription_package_id . '"  href="#Attendance" class="btn btn-danger tooltip-success btn-small subscriber_attendance" '
//                                        . 'data-toggle="modal"  data-original-title="Attendance" title="Attendance"><i class="entypo-doc-text"></i></a>';
//                            })
                            ->setRowId(function ($InstructorSubscription) {
                                return $InstructorSubscription->subscription_package_id;
                            })
                            ->with('count', $Count)
                            ->make();
        }

        $Members = DB::table($this->instructorSubscriptionTable . ' As ins')
                ->join('registered_users', 'ins.member_id', '=', 'registered_users.id')
                ->select('registered_users.name', 'registered_users.id')
                ->where('vendor_id', VendorDetail::getID())
                ->whereColumn('num_booked', '!=', 'num_points')
                ->where('package_id', $package_id)
                ->where(array('registered_users.status' => 1))
                ->whereNull('registered_users.deleted_at')
                ->get();

        return view('fitflowVendor.module1.instructorSubscriptions.subscribers')
                        ->with('Members', $Members)
                        ->with('package_id', $package_id)
                        ->with('Count', $Count);
    }

    //Add Attendance
    public function addAttendance(Request $request) {

        $arrayData = $request->jsonData;
        $start_date = $request->start_date;

        $arrayData = array_filter($arrayData);

        $this->table = 'subscriber_attend_instructor';
        $this->instructorSubscriptionTable = 'instructor_subscribers_package_details';

        //Ajax request
        if (request()->ajax()) {
            $error = array();

            foreach ($arrayData as $val) {

                if (($val['ids'] == 0)) {
                    array_push($error, $val['ids']);
                } else {
                    if ($start_date == '' || $start_date == null) {
                        $attendance['date'] = Carbon::now();
                    } else {
                        $datetime = new DateTime();
                        $newDate = $datetime->createFromFormat('d/m/Y', $request->start_date);
                        $attendance['date'] = $newDate->format('Y-m-d');
                    }
                    $attendance['created_at'] = Carbon::now();
                    $attendance['updated_at'] = Carbon::now();
                    $attendance['subscribed_package_id'] = $val['ids'];
                    $attendance['status'] = 1;
                    $lastID = DB::table($this->table)->insertGetId($attendance);
                    DB::table($this->instructorSubscriptionTable)->whereId($attendance['subscribed_package_id'])->increment('num_booked');
                    LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Instructor Subscriber Attendance ', 'updated');
                }
            }
            if ($error) {
                return response()->json(['error' => 'ID# ' . json_encode($error) . ' error in update']);
            } else {
                return response()->json(['response' => config('global.updatedRecords')]);
            }
        }
    }
    
     //show Attendance
    public function showAttendance(Request $request) {
        
        $subscribed_package_id = $request->subscribed_package_id;
        
     $this->table = 'subscriber_attend_instructor';
        $this->instructorSubscriptionTable = 'instructor_subscribers_package_details';
        
        $attendances = DB::table($this->table)
                ->select('date', 'status')
                ->where('subscribed_package_id', $subscribed_package_id)->get();

        
        $returnHTML = view('fitflowVendor.module1.instructorSubscriptions.showAttendance')->with('attendances', $attendances)->render();
       
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

}
