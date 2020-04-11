<?php

namespace App\Http\Controllers\Vendor\Module2;

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
use App\Helpers\LogActivity;
use App\Helpers\VendorDetail;
use Illuminate\Support\Facades\Auth;

class SubscriberController extends Controller {

    protected $guard = 'vendor';
    protected $configName;
    protected $table;
    protected $bookingTable;

    public function __construct() {
        $this->middleware($this->guard);
        $this->middleware('vendorPermission:M2subscribers');
        $this->configName = config('global.fitflowVendor') . config('global.storeAddress') . '/' . config('global.M2');
        //Update Package Status
       // VendorDetail::setSubscriberPackageStatus();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        //VendorDetail::setSubscriberPackageStatus();

        $this->table = VendorDetail::getPrefix() . 'subscribers_package_details';


        $SubscribersList = DB::table($this->table . ' As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->leftjoin('areas', 'areas.id', '=', 'registered_users.area_id')
                ->leftjoin('gender_types', 'gender_types.id', '=', 'registered_users.gender_id')
                ->select('spd.id', 'spd.subscriber_id', 'registered_users.name', 'registered_users.email', 'registered_users.mobile', 'areas.name_en As area', 'gender_types.name_en As gender'
                        , 'spd.num_points', 'spd.num_booked')
                ->where('spd.active_status', 1)
                ->where('spd.module_id', 2);
        //->where('spd.end_date', '>=', 'NOW()')
        //->whereColumn('spd.num_booked', '<', 'spd.num_points');
        //if Request having ID
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            $SubscribersList->where('spd.subscriber_id', $ID);
        }

        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $SubscribersList->whereBetween('spd.end_date', [$start_date, $end_date]);
        }
        $SubscribersList->groupby('spd.subscriber_id');
        //dd($SubscribersList->toSql());
        $RegisteredUser = $SubscribersList->get();
        //dd($RegisteredUser);
        //Get All Subscribers 
        $Subscribers = DB::table($this->table . ' As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->select('spd.subscriber_id', 'registered_users.name', 'spd.num_points', 'spd.num_booked')
                ->groupby('spd.subscriber_id')
                ->where('spd.active_status', 1)
                ->where('spd.module_id', 2)
                ->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($RegisteredUser)
                            ->editColumn('action', function ($RegisteredUser) {
                                return '<a  class="btn btn-primary tooltip-primary btn-small current_package" data-toggle="modal"  data-original-title="Current Package" title="Current Package"  href="#myModal" data-val="' . $RegisteredUser->id . '"><i class="entypo-clock"></i></a>
                                         <a  class="btn btn-info tooltip-info btn-small current_bookings" data-toggle="modal"  data-original-title="Current Bookings" title="Current Bookings"  href="#myModal" data-val="' . $RegisteredUser->id . '"><i class="entypo-book"></i></a>
                                          <a  class="btn btn-orange tooltip-primary btn-small payment_details" data-toggle="modal"  data-original-title="Payment Details" title="Payment Details" href="#myModal" data-val="' . $RegisteredUser->id . '"><i class="entypo-clock"></i></a>'
                                        . ' <a href="' . url($this->configName) . '/subscribers/' . $RegisteredUser->subscriber_id . '/packageHistory" class="btn btn-info tooltip-primary btn-small package_history" data-toggle="tooltip"  data-original-title="Package History" title="Package History"><i class="entypo-bag"></i></a>'
                                        . ' <a href="' . url($this->configName) . '/subscribers/' . $RegisteredUser->subscriber_id . '/bookingHistory" class="btn btn-danger tooltip-primary btn-small booking_history" data-toggle="tooltip"  data-original-title="Booking History" title="Booking History"><i class="entypo-book"></i></a>';
                            })
                            ->make();
        }

        return view('fitflowVendor.module2.subscribers.index')
                        ->with('Subscribers', $Subscribers);
    }

    public function currentPackage(Request $request, $id) {
        $id = $request->id;

        $this->table = VendorDetail::getPrefix() . 'subscribers_package_details';

        //Get current package
        $currentPackage = DB::table($this->table)
                ->select('name_en', 'price', 'num_days', 'start_date', 'end_date', DB::raw('(CASE WHEN num_points = 0 THEN "Unlimited" ELSE num_points END) AS num_points'))
                ->where(array('id' => $id))
                ->where('module_id', 2)
                ->first();

        //Change Start Date Format
        $newdate = new Carbon($currentPackage->start_date);
        $currentPackage->start_date = $newdate->format('d/m/Y');

        //Change End Date Format
        $enddate = new Carbon($currentPackage->end_date);
        $currentPackage->end_date = $enddate->format('d/m/Y');

        $currentPackage->detail_type = 0; //Current Package:0

        $returnHTML = view('fitflowVendor.module2.subscribers.currentPackage')->with('currentPackage', $currentPackage)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

    public function currentBooking(Request $request, $id) {
        $id = $request->id;

        $this->table = VendorDetail::getPrefix() . 'subscribers_package_details';
        $this->bookingTable = VendorDetail::getPrefix() . 'bookings';

        //Get current package
        $currentPackage = DB::table($this->table)
                ->select('id', DB::raw('(CASE WHEN num_points = 0 THEN "Unlimited" ELSE num_points END) AS num_points'), 'num_booked')
                ->where(array('id' => $id))
                ->where('module_id', 2)
                ->first();


        //Get current package
        $currentBookings = DB::table($this->bookingTable . ' As b')
                ->join('classes', 'classes.id', '=', 'b.class_id')
                ->join('class_schedules', 'class_schedules.id', '=', 'b.schedule_id')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select(DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS class_name'), 'class_schedules.start', 'class_schedules.end', 'b.created_at', 'class_schedules.schedule_date')
                ->where(array('b.subscribed_package_id' => $id))
                ->get();


        $returnHTML = view('fitflowVendor.module2.subscribers.currentBooking')
                ->with('currentPackage', $currentPackage)
                ->with('currentBookings', $currentBookings)
                ->render();

        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

    public function paymentDetails(Request $request, $id) {

        $id = $request->id;

        $this->table = VendorDetail::getPrefix() . 'subscribers_package_details';

        //Get current payment details
        $currentPackage = DB::table($this->table . ' As spd')
                ->join('payment_details', 'spd.payment_id', '=', 'payment_details.id')
                ->select('payment_details.reference_id', 'payment_details.amount', 'payment_details.post_date', 'payment_details.result', DB::raw('(CASE WHEN payment_details.card_type = 1 THEN "KNET" ELSE "Credit Card" END) AS card_type'))
                ->where(array('spd.id' => $id))
                ->where('spd.module_id', 2)
                ->first();


        //Change Start Date Format
        $newdate = new Carbon($currentPackage->post_date);
        $currentPackage->post_date = $newdate->format('d/m/Y');

        $currentPackage->detail_type = 1; //Payment Details:0

        $returnHTML = view('fitflowVendor.module2.subscribers.currentPackage')->with('currentPackage', $currentPackage)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

    public function packageHistory(Request $request, $id) {

        $id = $request->id;

        $this->table = VendorDetail::getPrefix() . 'subscribers_package_details';

        //Get Subscriber name
        $username = DB::table('registered_users')
                ->select('name')
                ->where('id', $id)
                ->first();

        //Sum Amount
        $Amount = DB::table($this->table . ' As spd')
                ->where(array('spd.subscriber_id' => $id))
                ->sum('spd.price');

        //Get all Archived Subscriber History
        $packageHistory = DB::table($this->table . ' As spd')
                ->select('spd.id', 'spd.name_en', 'spd.price', 'spd.start_date', 'spd.end_date', DB::raw('(CASE WHEN spd.num_points = 0 THEN "Unlimited" ELSE spd.num_points  END) AS num_points'), 'spd.num_days', 'spd.payment_id', 'spd.num_booked')
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
                            ->editColumn('name_en', function ($packageHistory) {
                                $str = '';
                                $str .= $packageHistory->name_en;
                                if ($packageHistory->end_date >= new Carbon() && $packageHistory->num_booked < $packageHistory->num_points)
                                    $str .= ' <div class="label label-success">Active</div>';

                                return $str;
                            })
                            ->editColumn('action', function ($packageHistory) {
                                return '<a  class="btn btn-gold tooltip-primary btn-small package_details" data-toggle="modal"  data-original-title="Package Details" title="Package Details"  href="#myModal" data-val="' . $packageHistory->payment_id . '"><i class="fa fa-money"></i></a>'
                                        . ' <a  class="btn btn-info tooltip-info btn-small bookings" data-toggle="modal"  data-original-title="Bookings" title="Bookings"  href="#myModal2" data-val="' . $packageHistory->id . '"><i class="entypo-book"></i></a>';
                            })
                            ->make();
        }

        return view('fitflowVendor.module2.subscribers.packageHistory')
                        ->with('id', $id)
                        ->with('username', $username)
                        ->with('Amount', $Amount);
    }

    public function packagePayment(Request $request, $id) {

        $payment_id = $request->id;


        //Get package payment details
        $payment = DB::table('payment_details')
                ->select('reference_id', 'amount', 'post_date', 'result', DB::raw('(CASE WHEN card_type = 1 THEN "KNET" ELSE "Credit Card" END) AS card_type'))
                ->where(array('id' => $payment_id))
                ->where('module_id', 2)
                ->first();



        //Change Start Date Format
        $newdate = new Carbon($payment->post_date);
        $payment->post_date = $newdate->format('d/m/Y');

        $returnHTML = view('fitflowVendor.module2.subscribers.packagePayment')->with('payment', $payment)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

    public function bookingHistory(Request $request, $id) {

        $id = $request->id;

        $this->table = VendorDetail::getPrefix() . 'subscribers_package_details';
        $this->bookingTable = VendorDetail::getPrefix() . 'bookings';

        //Get Subscriber name
        $username = DB::table('registered_users')
                ->select('name')
                ->where('id', $id)
                ->first();


        //Get current bookings
        $bookingHistory = DB::table($this->bookingTable . ' As b')
                ->join('classes', 'classes.id', '=', 'b.class_id')
                ->join('class_schedules', 'class_schedules.id', '=', 'b.schedule_id')
                 ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select(DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS class_name'), 'class_schedules.start', 'class_schedules.end', 'class_schedules.schedule_date', 'b.created_at')
                ->where(array('b.subscriber_id' => $id))
                ->get();


        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($bookingHistory)
                            ->editColumn('start', function ($bookingHistory) {
                                $newYear = new Carbon($bookingHistory->start);
                                return $newYear->format('h:m:A');
                            })
                            ->editColumn('end', function ($bookingHistory) {
                                $newYear = new Carbon($bookingHistory->end);
                                return $newYear->format('h:m:A');
                            })
                            ->editColumn('schedule_date', function ($bookingHistory) {
                                $newYear = new Carbon($bookingHistory->schedule_date);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('created_at', function ($bookingHistory) {
                                $newYear = new Carbon($bookingHistory->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->make();
        }

        return view('fitflowVendor.module2.subscribers.bookingHistory')
                        ->with('id', $id)
                        ->with('username', $username);
    }

    public function archivedSubscribers(Request $request) {

        //VendorDetail::setSubscriberPackageStatus();

        $this->table = VendorDetail::getPrefix() . 'subscribers_package_details';


        $SubscribersList = DB::table($this->table . ' As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->leftjoin('areas', 'areas.id', '=', 'registered_users.area_id')
                ->leftjoin('gender_types', 'gender_types.id', '=', 'registered_users.gender_id')
                ->select('spd.id', 'spd.subscriber_id', 'registered_users.name', 'registered_users.email', 'registered_users.mobile', 'areas.name_en As area', 'gender_types.name_en As gender'
                        , 'spd.num_points', 'spd.num_booked')
                ->where('spd.module_id', 2)
//                ->whereNotIn('spd.subscriber_id', function($query) {
//                    $query->select(DB::raw('COALESCE(subscriber_id,0) AS id'))
//                    ->from($this->table)
//                    ->where('active_status', '=', 1)
//                    ->orwhere('active_status', '=', 0)
//                    ->groupby('subscriber_id');
//                })
                ->whereNotIn('spd.subscriber_id', function($query) {
                    $query->select(DB::raw('ts.subscriber_id'))
                    ->from($this->table . ' As ts')
                    ->where(function ($query) {
                        $query->where('ts.active_status', '=', 1)
                        ->orwhere('ts.active_status', '=', 0);
                    })
                    ->whereColumn('spd.subscriber_id', 'ts.subscriber_id');
                })
                ->groupby('spd.subscriber_id');


        //if Request having ID
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            $SubscribersList->where('spd.subscriber_id', $ID);
        }

        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $SubscribersList->whereBetween('spd.end_date', [$start_date, $end_date]);
        }

        // dd($SubscribersList->toSql());
        $RegisteredUser = $SubscribersList->get();
        //dd($RegisteredUser);
        //Get All Subscribers 
        $Subscribers = DB::table($this->table . ' As spd')
                        ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                        ->select('spd.subscriber_id', 'registered_users.name', 'spd.num_points', 'spd.num_booked')
                        ->where('spd.module_id', 2)
//                        ->whereNotIn('spd.subscriber_id', function($query) {
//                            $query->select(DB::raw('COALESCE(subscriber_id,0) AS id'))
//                            ->from($this->table)
//                            ->where('active_status', '=', 1)
//                            ->orwhere('active_status', '=', 0)
//                            ->groupby('subscriber_id');
//                        })
                        ->whereNotIn('spd.subscriber_id', function($query) {
                            $query->select(DB::raw('ts.subscriber_id'))
                            ->from($this->table . ' As ts')
                            ->where(function ($query) {
                                $query->where('ts.active_status', '=', 1)
                                ->orwhere('ts.active_status', '=', 0);
                            })
                            ->whereColumn('spd.subscriber_id', 'ts.subscriber_id');
                        })
                        ->groupby('spd.subscriber_id')->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($RegisteredUser)
                            ->editColumn('action', function ($RegisteredUser) {
                                return '<a href="' . url($this->configName) . '/subscribers/' . $RegisteredUser->subscriber_id . '/packageHistory" class="btn btn-info tooltip-primary btn-small package_history" data-toggle="tooltip"  data-original-title="Package History" title="Package History"><i class="entypo-bag"></i></a>'
                                        . ' <a href="' . url($this->configName) . '/subscribers/' . $RegisteredUser->subscriber_id . '/bookingHistory" class="btn btn-danger tooltip-primary btn-small booking_history" data-toggle="tooltip"  data-original-title="Booking History" title="Booking History"><i class="entypo-book"></i></a>';
                            })
                            ->make();
        }

        return view('fitflowVendor.module2.subscribers.archivedSubscribers')
                        ->with('Subscribers', $Subscribers);
    }

}
