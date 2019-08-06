<?php

namespace App\Http\Controllers\Vendor\Module2;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\VendorDetail;

class ReportPrintController extends Controller {

    protected $guard = 'vendor';
    protected $configName;
    protected $PrintAccess;

    public function __construct() {
        $this->middleware($this->guard);
        $this->configName = config('global.fitflowVendor');
        //$this->middleware('vendorPermission:M2reports');
    }

    /**
     * Display a listing of the Favourites.
     */
    public function bookings(Request $request) {

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('classBookings-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        $this->table = VendorDetail::getPrefix() . 'subscribers_package_details';
        $this->bookingTable = VendorDetail::getPrefix() . 'bookings';

        $bookingList = DB::table($this->bookingTable . ' As b')
                ->join('classes', 'classes.id', '=', 'b.class_id')
                ->join('class_schedules', 'class_schedules.id', '=', 'b.schedule_id')
                ->join('registered_users', 'b.subscriber_id', '=', 'registered_users.id')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select('registered_users.name', DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS class_name'), 'class_schedules.start', 'class_schedules.end', 'class_schedules.schedule_date', 'b.created_at');


        if (Session::has('reportClassBookings')) {
            $val = Session::get('reportClassBookings');
            if (Session::has('reportClassBookings.start_date')) {
                $bookingList->whereBetween('class_schedules.schedule_date', [$val['start_date'], $val['end_date']]);
            }
            // if Request having Subscriber id
            if (Session::has('reportClassBookings.subscriber_id')) {
                $val = Session::get('reportClassBookings');
                $ID = $val['subscriber_id'];
                $bookingList->where('b.subscriber_id', $ID);
            }

            // if Request having Class id
            if (Session::has('reportClassBookings.class_id')) {
                $val = Session::get('reportClassBookings');
                $ID = $val['class_id'];
                $bookingList->where('b.class_master_id', $ID);
            }

            // if Request having Start Time and End Time
            if (Session::has('reportClassBookings.start_time')) {
                $val = Session::get('reportClassBookings');
                $bookingList->whereBetween('class_schedules.start', [$val['start_time'], $val['end_time']]);
            }
        }


        $bookingHistory = $bookingList->get();
        $Count = $bookingHistory->count();

        return view('fitflowVendor.module2.reportPrint.bookings')->with('Count', $Count)->with('bookingHistory', $bookingHistory);
    }

    /**
     * Display a listing of the Online Payments.
     */
    public function onlinePayment(Request $request) {

        $this->table = 'registered_users';
        $this->packageTable = VendorDetail::getPrefix() . 'subscribers_package_details';


        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('module2OnlinePayments-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        //Get package payment details
        $paymentList = DB::table('payment_details As p')
                ->join($this->packageTable . ' As sp', 'sp.payment_id', '=', 'p.id')
                ->leftjoin($this->table . ' As m', 'sp.subscriber_id', '=', 'm.id')
                ->select('m.name', 'sp.name_en', 'p.reference_id', 'p.amount', 'p.post_date', DB::raw('(CASE WHEN p.card_type = 1 THEN "KNET" ELSE "Credit Card" END) AS payment_method'))
                ->where('sp.vendor_id', VendorDetail::getID())
                ->where('sp.module_id', 2);

        $KnetAmountList = DB::table('payment_details As p')
                ->join($this->packageTable . ' As sp', 'sp.payment_id', '=', 'p.id')
                ->join('knet_payments AS k', 'k.id', '=', 'p.payid')
                ->select(DB::raw('COALESCE(SUM(k.amount),0) as knet_amount'))
                ->where('p.card_type', 1)
                ->where('sp.module_id', 2);

        $CCAmountList = DB::table('payment_details As p')
                ->join($this->packageTable . ' As sp', 'sp.payment_id', '=', 'p.id')
                ->join('cc_payments AS c', 'c.id', '=', 'p.payid')
                ->select(DB::raw('COALESCE(SUM(c.amount),0) as cc_amount'))
                ->where('p.card_type', 2)
                ->where('sp.module_id', 2);

        $AmountList = DB::table('payment_details As p')
                ->join($this->packageTable . ' As sp', 'sp.payment_id', '=', 'p.id')
                ->select(DB::raw('COALESCE(SUM(sp.price),0) as fees'))
                ->where('sp.module_id', 2);

        //if Request having Date Range
        if (Session::has('reportModule2onlinePayments')) {
            $val = Session::get('reportModule2onlinePayments');
            if (Session::has('reportModule2onlinePayments.start_date')) {
                $paymentList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
                $AmountList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
                $KnetAmountList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
                $CCAmountList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
            }
            // if Request having Member id
            if (Session::has('reportModule2onlinePayments.id')) {
                $val = Session::get('reportModule2onlinePayments');
                $ID = $val['id'];
                $paymentList->where('sp.subscriber_id', $ID);
                $AmountList->where('sp.subscriber_id', $ID);
                $KnetAmountList->where('sp.subscriber_id', $ID);
                $CCAmountList->where('sp.subscriber_id', $ID);
            }
        }

        $payments = $paymentList->get();
        $Amount = $AmountList->first();
        $KnetAmount = $KnetAmountList->first();
        $CCAmount = $CCAmountList->first();


        return view('fitflowVendor.module2.reportPrint.onlinePayments')
                        ->with('payments', $payments)
                        ->with('Amount', $Amount)
                        ->with('KnetAmount', $KnetAmount)
                        ->with('CCAmount', $CCAmount);
    }

    /**
     * Display a listing of the Subscription Expired.
     */
    public function subscriptionExpired(Request $request) {

        $this->packageTable = VendorDetail::getPrefix() . 'subscribers_package_details';

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('module2SubscriptionExpired-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        $SubscribersList = DB::table($this->packageTable . ' As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->leftjoin('areas', 'areas.id', '=', 'registered_users.area_id')
                ->leftjoin('gender_types', 'gender_types.id', '=', 'registered_users.gender_id')
                ->select('registered_users.name', 'registered_users.email', 'registered_users.mobile', 'gender_types.name_en As gender'
                        , 'spd.name_en', DB::raw('(CASE WHEN spd.num_points = 0 THEN "Unlimited" ELSE spd.num_points  END) AS num_points'), 'spd.num_booked'
                        , DB::raw('CONCAT(DATE_FORMAT(spd.start_date,"%d/%m/%Y"), "- ", DATE_FORMAT(spd.end_date,"%d/%m/%Y")) AS period'), 'spd.start_date', 'spd.end_date', 'spd.subscriber_id')
                ->where('spd.module_id', 2)
                ->whereNotIn('spd.subscriber_id', function($query) {
                    $query->select(DB::raw('ts.subscriber_id'))
                    ->from($this->packageTable . ' As ts')
                    ->where(function ($query) {
                        $query->where('ts.active_status', '=', 1)
                        ->orwhere('ts.active_status', '=', 0);
                    })
                    ->whereColumn('spd.subscriber_id', 'ts.subscriber_id');
                })
                ->groupby('spd.subscriber_id');


        if (Session::has('reportModule2SubscriberExpired')) {
            $val = Session::get('reportModule2SubscriberExpired');
            //if Request having Date Range
            if (Session::has('reportModule2SubscriberExpired.start_date')) {
                $SubscribersList->whereBetween('spd.end_date', [$val['start_date'], $val['end_date']]);
            }
            // if Request having Member id
            if (Session::has('reportModule2SubscriberExpired.id')) {
                $val = Session::get('reportModule2SubscriberExpired');
                $ID = $val['id'];
                $SubscribersList->where('spd.subscriber_id', $ID);
            }
            // if Request having Gender id
            if (Session::has('reportModule2SubscriberExpired.gender_id')) {
                $val = Session::get('reportModule2SubscriberExpired');
                $GenderID = $val['gender_id'];
                $SubscribersList->where('registered_users.gender_id', 'like', "$GenderID%");
            }
            // if Request having Package Name
            if (Session::has('reportModule2SubscriberExpired.name_en')) {
                $val = Session::get('reportModule2SubscriberExpired');
                $name_en = $val['name_en'];
                $SubscribersList->where('spd.name_en', 'like', "$name_en%");
            }
        }

        $Members = $SubscribersList->get();

        return view('fitflowVendor.module2.reportPrint.subscriptionExpired')
                        ->with('Members', $Members);
    }

    /**
     * Display a listing of the Subscription Expired.
     */
    public function subscriptions(Request $request) {

        $this->packageTable = VendorDetail::getPrefix() . 'subscribers_package_details';

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('module2Subscriptions-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        $SubscribersList = DB::table($this->packageTable . ' As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->leftjoin('areas', 'areas.id', '=', 'registered_users.area_id')
                ->leftjoin('gender_types', 'gender_types.id', '=', 'registered_users.gender_id')
                ->select('registered_users.name', 'registered_users.email', 'registered_users.mobile', 'gender_types.name_en As gender'
                        , 'spd.name_en', DB::raw('(CASE WHEN spd.num_points = 0 THEN "Unlimited" ELSE spd.num_points  END) AS num_points'), 'spd.num_booked'
                        , DB::raw('CONCAT(DATE_FORMAT(spd.start_date,"%d/%m/%Y"), "- ", DATE_FORMAT(spd.end_date,"%d/%m/%Y")) AS period'), 'spd.start_date', 'spd.end_date', 'spd.subscriber_id')
                ->where('spd.active_status', 1)
                ->where('spd.module_id', 2);


        if (Session::has('reportModule2Subscriber')) {
            $val = Session::get('reportModule2Subscriber');
            //if Request having Date Range
            if (Session::has('reportModule2Subscriber.start_date')) {
                $SubscribersList->whereBetween('spd.start_date', [$val['start_date'], $val['end_date']]);
            }
            // if Request having Member id
            if (Session::has('reportModule2Subscriber.id')) {
                $val = Session::get('reportModule2Subscriber');
                $ID = $val['id'];
                $SubscribersList->where('spd.subscriber_id', $ID);
            }
            // if Request having Gender id
            if (Session::has('reportModule2Subscriber.gender_id')) {
                $val = Session::get('reportModule2Subscriber');
                $GenderID = $val['gender_id'];
                $SubscribersList->where('registered_users.gender_id', 'like', "$GenderID%");
            }
            // if Request having Package Name
            if (Session::has('reportModule2Subscriber.name_en')) {
                $val = Session::get('reportModule2Subscriber');
                $name_en = $val['name_en'];
                $SubscribersList->where('spd.name_en', 'like', "$name_en%");
            }

            // if Member Status //1Week:0, 2Week:1, #Week:2
            if (Session::has('reportModule2Subscriber.expiry')) {
                $SubscribersList->whereBetween('spd.end_date', [$val['current_date'], $val['expiry']]);
            }
        }

        $Members = $SubscribersList->get();

        return view('fitflowVendor.module2.reportPrint.subscriptions')
                        ->with('Members', $Members);
    }

}
