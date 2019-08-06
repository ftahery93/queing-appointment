<?php

namespace App\Http\Controllers\Vendor\Module3;

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
        $this->middleware('vendorPermission:M3subscribers');
        $this->configName = config('global.fitflowVendor') . config('global.storeAddress') . '/' . config('global.M3');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        //VendorDetail::setSubscriberPackageStatus();

        $this->table = 'subscribers_package_details';
        $this->bookingTable = 'bookings';


        $SubscribersList = DB::table($this->bookingTable . ' As b')
                ->join($this->table.' AS spd', 'spd.id', '=', 'b.subscribed_package_id')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->leftjoin('areas', 'areas.id', '=', 'registered_users.area_id')
                ->leftjoin('gender_types', 'gender_types.id', '=', 'registered_users.gender_id')
                ->select('spd.id', 'spd.subscriber_id', 'registered_users.name', 'registered_users.email', 'registered_users.mobile', 'areas.name_en As area', 'gender_types.name_en As gender'
                        , 'spd.num_points', 'spd.num_booked')
                ->where(array('spd.module_id'=>3,'b.vendor_id'=>VendorDetail::getID()));
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
        $Subscribers = DB::table($this->bookingTable . ' As b')
                ->join($this->table.' AS spd', 'spd.id', '=', 'b.subscribed_package_id')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->select('spd.subscriber_id', 'registered_users.name', 'spd.num_points', 'spd.num_booked')
                ->groupby('spd.subscriber_id')
                ->where(array('spd.module_id'=>3,'b.vendor_id'=>VendorDetail::getID()))
                ->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($RegisteredUser)
                            ->editColumn('action', function ($RegisteredUser) {
                                return  ' <a href="' . url($this->configName) . '/m3/subscribers/' . $RegisteredUser->subscriber_id . '/bookingHistory" class="btn btn-danger tooltip-primary btn-small booking_history" data-toggle="tooltip"  data-original-title="Booking History" title="Booking History"><i class="entypo-book"></i></a>';
                            })
                            ->make();
        }

        return view('fitflowVendor.module3.subscribers.index')
                        ->with('Subscribers', $Subscribers);
    }

    
    public function bookingHistory(Request $request, $id) {

        $id = $request->id;

        $this->table = 'subscribers_package_details';
        $this->bookingTable = 'bookings';

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
                ->where(array('b.subscriber_id' => $id,'b.vendor_id'=>VendorDetail::getID()))
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

        return view('fitflowVendor.module3.subscribers.bookingHistory')
                        ->with('id', $id)
                        ->with('username', $username);
    }

   
}
