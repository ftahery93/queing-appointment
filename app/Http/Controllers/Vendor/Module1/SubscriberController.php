<?php

namespace App\Http\Controllers\Vendor\Module1;

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

    public function __construct() {
        $this->middleware($this->guard);
        $this->middleware('vendorPermission:subscribers');
        $this->configName = config('global.fitflowVendor') . config('global.storeAddress') . '/' . config('global.M1');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $this->table = VendorDetail::getPrefix() . 'subscribers_package_details';


        $SubscribersList = DB::table($this->table . ' As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->leftjoin('areas', 'areas.id', '=', 'registered_users.area_id')
                ->leftjoin('gender_types', 'gender_types.id', '=', 'registered_users.gender_id')
                ->select('spd.id', 'spd.subscriber_id', 'registered_users.name', 'registered_users.email', 'registered_users.mobile', 'areas.name_en As area', 'gender_types.name_en As gender')
                ->groupby('spd.subscriber_id')
                ->havingRaw('MAX(spd.end_date) >= NOW()');

        //if Request having ID
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            $SubscribersList->where('spd.subscriber_id', $ID);
        }
        
        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $SubscribersList->whereBetween('spd.end_date',  [$start_date, $end_date]);
        }
         //dd($SubscribersList->toSql());
        $RegisteredUser = $SubscribersList->get();
        
        //Get All Subscribers 
        $Subscribers = DB::table($this->table . ' As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->select('spd.subscriber_id', 'registered_users.name')
                ->groupby('spd.subscriber_id')
                ->havingRaw('MAX(spd.end_date) >= NOW()')
                ->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($RegisteredUser)
                            ->editColumn('action', function ($RegisteredUser) {
                                return '<a  class="btn btn-primary tooltip-primary btn-small current_package" data-toggle="modal"  data-original-title="Current Package" title="Current Package"  href="#myModal" data-val="' . $RegisteredUser->id . '"><i class="entypo-clock"></i></a>
                                          <a  class="btn btn-orange tooltip-primary btn-small payment_details" data-toggle="modal"  data-original-title="Payment Details" title="Payment Details" href="#myModal" data-val="' . $RegisteredUser->id . '"><i class="entypo-clock"></i></a>'
                                        . ' <a href="' . url($this->configName) . '/' . $RegisteredUser->subscriber_id . '/packageHistory" class="btn btn-info tooltip-primary btn-small package_history" data-toggle="tooltip"  data-original-title="Package History" title="Package History"><i class="entypo-bag"></i></a>';
                            })
                            ->make();
        }

        return view('fitflowVendor.module1.subscribers.index')
                        ->with('Subscribers', $Subscribers);
    }

    public function currentPackage(Request $request, $id) {
        $id = $request->id;

        $this->table = VendorDetail::getPrefix() . 'subscribers_package_details';

        //Get current package
        $currentPackage = DB::table($this->table)
                ->select('name_en', 'price', 'num_days', 'start_date', 'end_date')
                ->where(array('id' => $id))
                ->first();

        //Change Start Date Format
        $newdate = new Carbon($currentPackage->start_date);
        $currentPackage->start_date = $newdate->format('d/m/Y');

        //Change End Date Format
        $enddate = new Carbon($currentPackage->end_date);
        $currentPackage->end_date = $enddate->format('d/m/Y');

        $currentPackage->detail_type = 0; //Current Package:0

        $returnHTML = view('fitflowVendor.module1.subscribers.currentPackage')->with('currentPackage', $currentPackage)->render();
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
                ->first();


        //Change Start Date Format
        $newdate = new Carbon($currentPackage->post_date);
        $currentPackage->post_date = $newdate->format('d/m/Y');

        $currentPackage->detail_type = 1; //Payment Details:0

        $returnHTML = view('fitflowVendor.module1.subscribers.currentPackage')->with('currentPackage', $currentPackage)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

    //Archived Classes
    public function archivedSubscribers(Request $request) {

        $this->table = VendorDetail::getPrefix() . 'subscribers_package_details';

        $SubscribersList = DB::table($this->table . ' As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->leftjoin('areas', 'areas.id', '=', 'registered_users.area_id')
                ->leftjoin('gender_types', 'gender_types.id', '=', 'registered_users.gender_id')
                ->select('spd.id', 'spd.subscriber_id', 'registered_users.name', 'registered_users.email', 'registered_users.mobile', 'areas.name_en As area', 'gender_types.name_en As gender')
                ->groupby('spd.subscriber_id')
                ->havingRaw('MAX(spd.end_date) < NOW()');

        //if Request having ID
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            $SubscribersList->where('spd.subscriber_id', $ID);
        }
        
        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $SubscribersList->whereBetween('spd.end_date',  [$start_date, $end_date]);
        }
        
        $RegisteredUser = $SubscribersList->get();

        //Get All Subscribers 
        $Subscribers = DB::table($this->table . ' As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->select('spd.subscriber_id', 'registered_users.name')
                ->groupby('spd.subscriber_id')
                ->havingRaw('MAX(spd.end_date) < NOW()')
                ->get();


        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($RegisteredUser)
                            ->editColumn('action', function ($RegisteredUser) {
                                return '<a href="' . url($this->configName) . '/' . $RegisteredUser->subscriber_id . '/packageHistory" class="btn btn-primary tooltip-primary btn-small package_history" data-toggle="tooltip"  data-original-title="Package History" title="Package History"><i class="entypo-bag"></i></a>';
                            })
                            ->make();
        }

        return view('fitflowVendor.module1.subscribers.archivedSubscribers')
                        ->with('Subscribers', $Subscribers);
    }

  
    public function packageHistory(Request $request, $id) {
        
        $id = $request->id;

        $this->table = VendorDetail::getPrefix() . 'subscribers_package_details';

        //Get Subscriber name
        $username = DB::table($this->table . ' As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->select('registered_users.name')
                ->where('spd.id', $id)
                ->first();

        //Sum Amount
        $Amount = DB::table($this->table . ' As spd')
                ->where(array('spd.subscriber_id' => $id))
                ->sum('spd.price');

        //Get all Archived Subscriber History
        $packageHistory = DB::table($this->table . ' As spd')
                ->select('spd.name_en', 'spd.price', 'spd.start_date', 'spd.end_date', 'spd.num_days', 'spd.payment_id')
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
                            ->editColumn('action', function ($packageHistory) {
                                return '<a  class="btn btn-green tooltip-primary btn-small package_details" data-toggle="modal"  data-original-title="Package Details" title="Package Details"  href="#myModal" data-val="' . $packageHistory->payment_id . '"><i class="fa fa-money"></i></a>';
                            })
                            ->make();
        }

        return view('fitflowVendor.module1.subscribers.packageHistory')
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
                ->first();



        //Change Start Date Format
        $newdate = new Carbon($payment->post_date);
        $payment->post_date = $newdate->format('d/m/Y');

        $returnHTML = view('fitflowVendor.module1.subscribers.packagePayment')->with('payment', $payment)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

}
