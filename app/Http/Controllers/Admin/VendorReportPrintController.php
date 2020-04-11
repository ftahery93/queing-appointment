<?php

namespace App\Http\Controllers\Admin;

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

class VendorReportPrintController extends Controller {

    protected $guard = 'auth';
    protected $PrintAccess;

    public function __construct() {
        $this->middleware($this->guard);
        //$this->middleware('permission:reports');
    }

    /**
     * Display a listing of the Favourites.
     */
    public function favourite(Request $request) {

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('favourites-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        $favouriteList = DB::table('favourites AS f')
                ->join('vendors As v', 'v.id', '=', 'f.vendor_id')
                ->join('registered_users As ru', 'ru.id', '=', 'f.subscriber_id')
                ->select('ru.name As subscriber', 'v.name as vendor', 'f.created_at');

        if (Session::has('reportVendorFavourites')) {
            $val = Session::get('reportVendorFavourites');

            //if Request having Date Range
            if (Session::has('reportVendorFavourites.start_date')) {
                $favouriteList->whereBetween('f.created_at', [$val['start_date'], $val['end_date']]);
            }

            //if Request having Vendor ID
            if (Session::has('reportVendorFavourites.vendor_id')) {
                $val = Session::get('reportVendorFavourites');
                $ID = $val['vendor_id'];
                $favouriteList->where('f.vendor_id', $ID);
            }

            //if Request having Subscriber ID
            if (Session::has('reportVendorFavourites.subscriber_id')) {
                $val = Session::get('reportVendorFavourites');
                $ID = $val['subscriber_id'];
                $favouriteList->where('f.subscriber_id', $ID);
            }
        }
        $Favourites = $favouriteList->get();

        return view('admin.vendorPrintReports.favourites')->with('Favourites', $Favourites);
    }

    /**
     * Display a listing of the Payments.
     */
    public function payment(Request $request) {

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('vendorPayments-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        $tableArray = [];
        $Invoices = collect([]);
        $invoiceAmount = new \stdClass();
        $invoiceAmount->fees = '0.000';
        $invoiceAmount->cash_amount = '0.000';
        $invoiceAmount->knet_amount = '0.000';


        //if Request having Date Range
        if (Session::has('reportVendorPayments') && Session::get('reportVendorPayments.vendor_id') != 0) {
            $val = Session::get('reportVendorPayments');
            $vendor_id = $val['vendor_id'];
            $this->table = 'v' . $vendor_id . '_members';
            $this->invoiceTable = 'v' . $vendor_id . '_member_invoices';

            //Get Member & Invoice
            $invoiceList = DB::table($this->invoiceTable . ' As inv')
                    ->join($this->table . ' As m', 'inv.member_id', '=', 'm.id')
                    ->join('vendor_users As vu', 'vu.id', '=', 'inv.collected_by')
                    ->select('inv.receipt_num', 'm.name', 'inv.package_name', 'inv.created_at', 'vu.name AS collected_by', 'inv.cash', 'inv.knet', 'inv.price');

            $invoiceAmountList = DB::table($this->invoiceTable . ' As inv')
                    ->select(DB::raw('SUM(cash) as cash_amount')
                    , DB::raw('SUM(knet) as knet_amount'), DB::raw('SUM(price) as fees'));

            if (Session::has('reportVendorPayments.start_date')) {
                $invoiceList->whereBetween('inv.created_at', [$val['start_date'], $val['end_date']]);
                $invoiceAmountList->whereBetween('inv.created_at', [$val['start_date'], $val['end_date']]);
            }
            // if Request having Member id
            if (Session::has('reportVendorPayments.id')) {
                $val = Session::get('reportVendorPayments');
                $ID = $val['id'];
                $invoiceList->where('inv.member_id', $ID);
                $invoiceAmountList->where('inv.member_id', $ID);
            }
            $Invoices = $invoiceList->get()->toArray();
            $invoiceAmount = $invoiceAmountList->first();
        }


        return view('admin.vendorPrintReports.payments')
                        ->with('Invoices', $Invoices)
                        ->with('invoiceAmount', $invoiceAmount);
    }

    /**
     * Display a listing of the Online Payments.
     */
    public function onlinePayment(Request $request) {

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('vendorOnlinePayments-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');


        $payments = collect([]);
        $Amount = new \stdClass();
        $Amount->fees = '0.000';
        $KnetAmount = new \stdClass();
        $KnetAmount->knet_amount = '0.000';
        $CCAmount = new \stdClass();
        $CCAmount->cc_amount = '0.000';

        if (Session::has('reportVendorOnlinePayments') && Session::get('reportVendorOnlinePayments.vendor_id') != 0) {
            $val = Session::get('reportVendorOnlinePayments');
            $vendor_id = $val['vendor_id'];
            $this->table = 'v' . $vendor_id . '_members';
            $this->packageTable = 'v' . $vendor_id . '_subscribers_package_details';

            //Get package payment details
            $paymentList = DB::table('payment_details As p')
                    ->join($this->packageTable . ' As sp', 'sp.payment_id', '=', 'p.id')
                    ->leftjoin($this->table . ' As m', 'sp.member_id', '=', 'm.id')
                    ->select('m.name', 'm.package_name', 'p.reference_id', 'p.amount', 'p.post_date', DB::raw('(CASE WHEN p.card_type = 1 THEN "KNET" ELSE "Credit Card" END) AS payment_method'))
                    ->where('sp.vendor_id', $vendor_id)
                    ->where('sp.module_id', 1);

            $KnetAmountList = DB::table('payment_details As p')
                    ->join($this->packageTable . ' As sp', 'sp.payment_id', '=', 'p.id')
                    ->join('knet_payments AS k', 'k.id', '=', 'p.payid')
                    ->select(DB::raw('COALESCE(SUM(k.amount),0) as knet_amount'))
                    ->where('p.card_type', 1)
                    ->where('sp.module_id', 1);

            $CCAmountList = DB::table('payment_details As p')
                    ->join($this->packageTable . ' As sp', 'sp.payment_id', '=', 'p.id')
                    ->join('cc_payments AS c', 'c.id', '=', 'p.payid')
                    ->select(DB::raw('COALESCE(SUM(c.amount),0) as cc_amount'))
                    ->where('p.card_type', 2)
                    ->where('sp.module_id', 1);

            $AmountList = DB::table('payment_details As p')
                    ->join($this->packageTable . ' As sp', 'sp.payment_id', '=', 'p.id')
                    ->select(DB::raw('COALESCE(SUM(sp.price),0) as fees'))
                    ->where('sp.module_id', 1);

            if (Session::has('reportVendorOnlinePayments.start_date')) {
                $paymentList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
                $AmountList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
                $KnetAmountList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
                $CCAmountList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
            }
            // if Request having Member id
            if (Session::has('reportVendorOnlinePayments.id')) {
                $val = Session::get('reportVendorOnlinePayments');
                $ID = $val['id'];
                $paymentList->where('sp.member_id', $ID);
                $AmountList->where('sp.member_id', $ID);
                $KnetAmountList->where('sp.member_id', $ID);
                $CCAmountList->where('sp.member_id', $ID);
            }
            $payments = $paymentList->get();
            $Amount = $AmountList->first();
            $KnetAmount = $KnetAmountList->first();
            $CCAmount = $CCAmountList->first();
        }


        return view('admin.vendorPrintReports.onlinePayments')
                        ->with('payments', $payments)
                        ->with('Amount', $Amount)
                        ->with('KnetAmount', $KnetAmount)
                        ->with('CCAmount', $CCAmount);
    }

    /**
     * Display a listing of the Subscription Expired.
     */
    public function subscriptionExpired(Request $request) {

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('VendorSubscriptionExpired-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        $tableArray = [];
        $Members = collect([]);

        //if Request having Date Range
        if (Session::has('reportVendorSubscriptionExpired') && Session::get('reportVendorSubscriptionExpired.vendor_id') != 0) {
            $val = Session::get('reportVendorSubscriptionExpired');
            $vendor_id = $val['vendor_id'];
            $this->table = 'v' . $vendor_id . '_members';

            $MemberList = DB::table($this->table . ' As m')
                    ->leftjoin('gender_types As g', 'g.id', '=', 'm.gender_id')
                    ->select('m.name', 'm.email', 'm.mobile', 'g.name_en AS gender_name', 'm.package_name', 'm.start_date', 'm.end_date', 'm.subscribed_from')
                    ->whereNull('m.deleted_at')
                    ->whereDate('m.end_date', '<', Carbon::now())
                    ->groupby('m.id');

            //if Request having Date Range
            if (Session::has('reportVendorSubscriptionExpired.start_date')) {
                $MemberList->whereBetween('m.end_date', [$val['start_date'], $val['end_date']]);
            }
            // if Request having Member id
            if (Session::has('reportVendorSubscriptionExpired.id')) {
                $val = Session::get('reportVendorSubscriptionExpired');
                $ID = $val['id'];
                $MemberList->where('m.id', $ID);
            }
            // if Request having Package Name
            if (Session::has('reportVendorSubscriptionExpired.name_en')) {
                $val = Session::get('reportVendorSubscriptionExpired');
                $name_en = $val['name_en'];
                $MemberList->where('m.id', 'like', "$name_en%");
            }
            // if Request having Member Type
            if (Session::has('reportVendorSubscriptionExpired.member_type')) {
                $val = Session::get('reportVendorSubscriptionExpired');
                $member_type = $val['member_type'];
                $MemberList->where('m.subscribed_from', 'like', "$member_type%");
            }
            // if Request having Gender id
            if (Session::has('reportVendorSubscriptionExpired.gender_id')) {
                $val = Session::get('reportVendorSubscriptionExpired');
                $GenderID = $val['gender_id'];
                $MemberList->where('m.gender_id', 'like', "$GenderID%");
            }
            $Members = $MemberList->get();
        }

        return view('admin.vendorPrintReports.subscriptionExpired')
                        ->with('Members', $Members);
    }

    /**
     * Display a listing of the Subscription Expired.
     */
    public function subscriptions(Request $request) {

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('VendorSubscriptions-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        $tableArray = [];
        $Members = collect([]);

        //if Request having Date Range
        if (Session::has('reportVendorSubscriptions') && Session::get('reportVendorSubscriptions.vendor_id') != 0) {
            $val = Session::get('reportVendorSubscriptions');
            $vendor_id = $val['vendor_id'];
            $this->table = 'v' . $vendor_id . '_members';

            $MemberList = DB::table($this->table . ' As m')
                    ->leftjoin('gender_types As g', 'g.id', '=', 'm.gender_id')
                    ->select('m.name', 'm.email', 'm.mobile', 'g.name_en AS gender_name', 'm.package_name', 'm.start_date', 'm.end_date', 'm.subscribed_from')
                    ->whereNull('m.deleted_at')
                    ->whereDate('m.end_date', '>=', Carbon::now())
                    ->groupby('m.id');

            //if Request having Date Range
            if (Session::has('reportVendorSubscriptions.start_date')) {
                $MemberList->whereBetween('m.end_date', [$val['start_date'], $val['end_date']]);
            }
            // if Request having Member id
            if (Session::has('reportVendorSubscriptions.id')) {
                $val = Session::get('reportVendorSubscriptions');
                $ID = $val['id'];
                $MemberList->where('m.id', $ID);
            }
            // if Request having Package Name
            if (Session::has('reportVendorSubscriptions.name_en')) {
                $val = Session::get('reportVendorSubscriptions');
                $name_en = $val['name_en'];
                $MemberList->where('m.id', 'like', "$name_en%");
            }
            // if Request having Member Type
            if (Session::has('reportVendorSubscriptions.member_type')) {
                $val = Session::get('reportVendorSubscriptions');
                $member_type = $val['member_type'];
                $MemberList->where('m.subscribed_from', 'like', "$member_type%");
            }
            // if Request having Gender id
            if (Session::has('reportVendorSubscriptions.gender_id')) {
                $val = Session::get('reportVendorSubscriptions');
                $GenderID = $val['gender_id'];
                $MemberList->where('m.gender_id', 'like', "$GenderID%");
            }
            // if Request having Member Type
            if (Session::has('reportVendorSubscriptions.expiry')) {
                $MemberList->whereBetween('m.end_date', [$val['current_date'], $val['expiry']]);
            }

            $Members = $MemberList->get();
        }
        return view('admin.vendorPrintReports.subscriptions')
                        ->with('Members', $Members);
    }

    /**
     * Display a listing of the Class Subscription.
     */
    public function classSubscriptions(Request $request) {

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('classSubscriptions-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        $tableArray = [];
        $Members = collect([]);

        //if Request having Date Range        
        if (Session::has('reportModule2Subscriber') && Session::get('reportModule2Subscriber.vendor_id') != 0) {
            $val = Session::get('reportModule2Subscriber');
            $vendor_id = $val['vendor_id'];
            $this->packageTable = 'v' . $vendor_id . '_subscribers_package_details';

            $SubscribersList = DB::table($this->packageTable . ' As spd')
                    ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                    ->leftjoin('areas', 'areas.id', '=', 'registered_users.area_id')
                    ->leftjoin('gender_types', 'gender_types.id', '=', 'registered_users.gender_id')
                    ->select('registered_users.name', 'registered_users.email', 'registered_users.mobile', 'gender_types.name_en As gender'
                            , 'spd.name_en', DB::raw('(CASE WHEN spd.num_points = 0 THEN "Unlimited" ELSE spd.num_points  END) AS num_points'), 'spd.num_booked', 'spd.start_date'
                            , DB::raw('CONCAT(DATE_FORMAT(spd.start_date,"%d/%m/%Y"), "- ", DATE_FORMAT(spd.end_date,"%d/%m/%Y")) AS period'), 'spd.end_date', 'spd.subscriber_id')
                    ->where('spd.active_status', 1)
                    ->where('spd.module_id', 2);


            // if Request having Pacakge name
            if (Session::has('reportModule2Subscriber.name_en')) {
                $name_en = $val['name_en'];
                $SubscribersList->where('spd.name_en', 'like', "$name_en%");
            }

            //if Request having Date Range
            if (Session::has('reportModule2Subscriber.start_date')) {
                $SubscribersList->whereBetween('spd.start_date', [$val['start_date'], $val['end_date']]);
            }

            // if Request having Member id
            if (Session::has('reportModule2Subscriber.id')) {
                $ID = $val['id'];
                $SubscribersList->where('spd.subscriber_id', $ID);
            }
            // if Request having Gender id
            if (Session::has('reportModule2Subscriber.gender_id')) {
                $GenderID = $val['gender_id'];
                $SubscribersList->where('registered_users.gender_id', 'like', "$GenderID%");
            }

            // if Member Status //1Week:0, 2Week:1, #Week:2
            if (Session::has('reportModule2Subscriber.expiry')) {
                $SubscribersList->whereBetween('spd.end_date', [$val['current_date'], $val['expiry']]);
            }

            $Members = $SubscribersList->get();
        }

        return view('admin.vendorPrintReports.classSubscriptions')->with('Members', $Members);
    }

    /**
     * Display a listing of the Class Subscription Expired.
     */
    public function classSubscriptionExpired(Request $request) {

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('classSubscriptionExpired-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        $tableArray = [];
        $Members = collect([]);

        //if Request having Date Range
        if (Session::has('reportModule2SubscriberExpired') && Session::get('reportModule2SubscriberExpired.vendor_id') != 0) {
            $val = Session::get('reportModule2SubscriberExpired');
            $vendor_id = $val['vendor_id'];
            $this->packageTable = 'v' . $vendor_id . '_subscribers_package_details';

            $SubscribersList = DB::table($this->packageTable . ' As spd')
                    ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                    ->leftjoin('areas', 'areas.id', '=', 'registered_users.area_id')
                    ->leftjoin('gender_types', 'gender_types.id', '=', 'registered_users.gender_id')
                    ->select('registered_users.name', 'registered_users.email', 'registered_users.mobile', 'gender_types.name_en As gender'
                            , 'spd.name_en', DB::raw('(CASE WHEN spd.num_points = 0 THEN "Unlimited" ELSE spd.num_points  END) AS num_points'), 'spd.num_booked', 'spd.end_date'
                            , DB::raw('CONCAT(DATE_FORMAT(spd.start_date,"%d/%m/%Y"), "- ", DATE_FORMAT(spd.end_date,"%d/%m/%Y")) AS period'), 'spd.start_date', 'spd.subscriber_id')
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

            // if Request having Pacakge name
            if (Session::has('reportModule2SubscriberExpired.name_en')) {
                $name_en = $val['name_en'];
                $SubscribersList->where('spd.name_en', 'like', "$name_en%");
            }

            //if Request having Date Range
            if (Session::has('reportModule2SubscriberExpired.start_date')) {
                $SubscribersList->whereBetween('spd.start_date', [$val['start_date'], $val['end_date']]);
            }

            // if Request having Member id
            if (Session::has('reportModule2SubscriberExpired.id')) {
                $ID = $val['id'];
                $SubscribersList->where('spd.subscriber_id', $ID);
            }
            // if Request having Gender id
            if (Session::has('reportModule2SubscriberExpired.gender_id')) {
                $GenderID = $val['gender_id'];
                $SubscribersList->where('registered_users.gender_id', 'like', "$GenderID%");
            }

            $Members = $SubscribersList->get();
        }

        return view('admin.vendorPrintReports.classSubscriptionExpired')->with('Members', $Members);
    }

    /**
     * Display a listing of the Class Online Payments.
     */
    public function classOnlinePayment(Request $request) {

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('classOnlinePayments-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        $this->table = 'registered_users';

        $tableArray = [];
        $payments = collect([]);
        $Amount = new \stdClass();
        $Amount->fees = '0.000';
        $KnetAmount = new \stdClass();
        $KnetAmount->knet_amount = '0.000';
        $CCAmount = new \stdClass();
        $CCAmount->cc_amount = '0.000';

        //if Request having Date Range
        if (Session::has('reportModule2onlinePayments') && Session::get('reportModule2onlinePayments.vendor_id') != 0) {
            $val = Session::get('reportModule2onlinePayments');
            $vendor_id = $val['vendor_id'];
            $this->packageTable = 'v' . $vendor_id . '_subscribers_package_details';

            //Get package payment details
            $paymentList = DB::table('payment_details As p')
                    ->join($this->packageTable . ' As sp', 'sp.payment_id', '=', 'p.id')
                    ->leftjoin($this->table . ' As m', 'sp.subscriber_id', '=', 'm.id')
                    ->select('m.name', 'sp.name_en', 'p.reference_id', 'p.amount', 'p.post_date', DB::raw('(CASE WHEN p.card_type = 1 THEN "KNET" ELSE "Credit Card" END) AS payment_method'))
                    ->where('sp.vendor_id', $vendor_id)
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
                            ->select(DB::raw('COALESCE(SUM(sp.price),0) as fees'))->where('sp.module_id', 2);


            //if Request having Date Range
            if (Session::has('reportModule2onlinePayments.start_date')) {
                $paymentList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
                $AmountList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
                $KnetAmountList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
                $CCAmountList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
            }

            // if Request having Member id
            if (Session::has('reportModule2onlinePayments.id')) {
                $ID = $val['id'];
                $paymentList->where('sp.subscriber_id', $ID);
                $AmountList->where('sp.subscriber_id', $ID);
                $KnetAmountList->where('sp.subscriber_id', $ID);
                $CCAmountList->where('sp.subscriber_id', $ID);
            }


            $payments = $paymentList->get();
            $Amount = $AmountList->first();
            $KnetAmount = $KnetAmountList->first();
            $CCAmount = $CCAmountList->first();
        }


        return view('admin.vendorPrintReports.classOnlinePayments')
                        ->with('payments', $payments)
                        ->with('Amount', $Amount)
                        ->with('KnetAmount', $KnetAmount)
                        ->with('CCAmount', $CCAmount);
    }

    /**
     * Display a listing of the Class Booking.
     */
    public function classBookings(Request $request) {

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('classBookings-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');


        $tableArray = [];
        $bookingHistory = collect([]);
        $Count = 0;

        //if Request having Date Range
        if (Session::has('reportClassBookings') && Session::get('reportClassBookings.vendor_id') != 0) {
            $val = Session::get('reportClassBookings');
            $vendor_id = $val['vendor_id'];
            $this->table = 'v' . $vendor_id . '_subscribers_package_details';
            $this->bookingTable = 'v' . $vendor_id . '_bookings';

            $bookingList = DB::table($this->bookingTable . ' As b')
                    ->join('classes', 'classes.id', '=', 'b.class_id')
                    ->join('class_schedules', 'class_schedules.id', '=', 'b.schedule_id')
                    ->join('registered_users', 'b.subscriber_id', '=', 'registered_users.id')
                    ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                    ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                    ->select('registered_users.name', DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS class_name'), 'class_schedules.start', 'class_schedules.end', 'class_schedules.schedule_date', 'b.created_at');


            //if Request having Date Range
            if (Session::has('reportClassBookings.start_date')) {
                $bookingList->whereBetween('class_schedules.schedule_date', [$val['start_date'], $val['end_date']]);
            }

            // if Request having Member id
            if (Session::has('reportClassBookings.subscriber_id')) {
                $ID = $val['subscriber_id'];
                $bookingList->where('b.subscriber_id', $ID);
            }

            // if Request having Class id
            if (Session::has('reportClassBookings.class_id')) {
                $ID = $val['class_id'];
                $bookingList->where('b.class_master_id', $ID);
            }

            //if Request having Start Time And End Time        
            if (Session::has('reportClassBookings.start_time')) {
                $bookingList->whereBetween('class_schedules.start', [$val['start_time'], $val['end_time']]);
            }

            $bookingHistory = $bookingList->get();
            $Count = $bookingList->count();
        }


        return view('admin.vendorPrintReports.classBookings')
                        ->with('bookingHistory', $bookingHistory)
                        ->with('Count', $Count);
    }

    /**
     * Display a listing of the Fitflow Membership Subscription.
     */
    public function fitflowMembershipSubscriptions(Request $request) {

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('fitflowMembershipSubscriptions-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        $tableArray = [];

        $this->packageTable = 'subscribers_package_details';

        $SubscribersList = DB::table($this->packageTable . ' As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->leftjoin('areas', 'areas.id', '=', 'registered_users.area_id')
                ->leftjoin('gender_types', 'gender_types.id', '=', 'registered_users.gender_id')
                ->select('registered_users.name', 'registered_users.email', 'registered_users.mobile', 'gender_types.name_en As gender'
                        , 'spd.name_en', DB::raw('(CASE WHEN spd.num_points = 0 THEN "Unlimited" ELSE spd.num_points  END) AS num_points'), 'spd.num_booked', 'spd.start_date'
                        , DB::raw('CONCAT(DATE_FORMAT(spd.start_date,"%d/%m/%Y"), "- ", DATE_FORMAT(spd.end_date,"%d/%m/%Y")) AS period'), 'spd.end_date', 'spd.subscriber_id')
                ->where('spd.active_status', 1)
                ->where('spd.module_id', 3);

        if (Session::has('reportModule3Subscriber')) {
            $val = Session::get('reportModule3Subscriber');
            // if Request having Pacakge name
            if (Session::has('reportModule3Subscriber.name_en')) {
                $name_en = $val['name_en'];
                $SubscribersList->where('spd.name_en', 'like', "$name_en%");
            }

            //if Request having Date Range
            if (Session::has('reportModule3Subscriber.start_date')) {
                $SubscribersList->whereBetween('spd.start_date', [$val['start_date'], $val['end_date']]);
            }

            // if Request having Member id
            if (Session::has('reportModule3Subscriber.id')) {
                $ID = $val['id'];
                $SubscribersList->where('spd.subscriber_id', $ID);
            }
            // if Request having Gender id
            if (Session::has('reportModule3Subscriber.gender_id')) {
                $GenderID = $val['gender_id'];
                $SubscribersList->where('registered_users.gender_id', 'like', "$GenderID%");
            }

            // if Member Status //1Week:0, 2Week:1, #Week:2
            if (Session::has('reportModule3Subscriber.expiry')) {
                $SubscribersList->whereBetween('spd.end_date', [$val['current_date'], $val['expiry']]);
            }
        }

        $Members = $SubscribersList->get();

        return view('admin.vendorPrintReports.fitflowMembershipSubscriptions')->with('Members', $Members);
    }

    /**
     * Display a listing of the Fitflow Membership Expired.
     */
    public function fitflowMembershipSubscriptionExpired(Request $request) {

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('fitflowMembershipSubscriptionExpired-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        $tableArray = [];

        $this->packageTable = 'subscribers_package_details';

        $SubscribersList = DB::table($this->packageTable . ' As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->leftjoin('areas', 'areas.id', '=', 'registered_users.area_id')
                ->leftjoin('gender_types', 'gender_types.id', '=', 'registered_users.gender_id')
                ->select('registered_users.name', 'registered_users.email', 'registered_users.mobile', 'gender_types.name_en As gender'
                        , 'spd.name_en', DB::raw('(CASE WHEN spd.num_points = 0 THEN "Unlimited" ELSE spd.num_points  END) AS num_points'), 'spd.num_booked', 'spd.end_date'
                        , DB::raw('CONCAT(DATE_FORMAT(spd.start_date,"%d/%m/%Y"), "- ", DATE_FORMAT(spd.end_date,"%d/%m/%Y")) AS period'), 'spd.start_date', 'spd.subscriber_id')
                ->where('spd.module_id', 3)
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

        if (Session::has('reportModule3SubscriberExpired')) {
            $val = Session::get('reportModule3SubscriberExpired');
            // if Request having Pacakge name
            if (Session::has('reportModule3SubscriberExpired.name_en')) {
                $name_en = $val['name_en'];
                $SubscribersList->where('spd.name_en', 'like', "$name_en%");
            }

            //if Request having Date Range
            if (Session::has('reportModule3SubscriberExpired.start_date')) {
                $SubscribersList->whereBetween('spd.start_date', [$val['start_date'], $val['end_date']]);
            }

            // if Request having Member id
            if (Session::has('reportModule3SubscriberExpired.id')) {
                $ID = $val['id'];
                $SubscribersList->where('spd.subscriber_id', $ID);
            }
            // if Request having Gender id
            if (Session::has('reportModule3SubscriberExpired.gender_id')) {
                $GenderID = $val['gender_id'];
                $SubscribersList->where('registered_users.gender_id', 'like', "$GenderID%");
            }
        }

        $Members = $SubscribersList->get();

        return view('admin.vendorPrintReports.fitflowMembershipSubscriptionExpired')->with('Members', $Members);
    }

    /**
     * Display a listing of the Fitflow Online Payments.
     */
    public function fitflowMembershipOnlinePayment(Request $request) {

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('fitflowMembershipOnlinePayments-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        $tableArray = [];

        $this->table = 'registered_users';
        $this->packageTable = 'subscribers_package_details';

        //Get package payment details
        $paymentList = DB::table('payment_details As p')
                ->join($this->packageTable . ' As sp', 'sp.payment_id', '=', 'p.id')
                ->leftjoin($this->table . ' As m', 'sp.subscriber_id', '=', 'm.id')
                ->select('m.name', 'sp.name_en', 'p.reference_id', 'p.amount', 'p.post_date', DB::raw('(CASE WHEN p.card_type = 1 THEN "KNET" ELSE "Credit Card" END) AS payment_method'))
                ->where('sp.module_id', 3);

        $KnetAmountList = DB::table('payment_details As p')
                ->join($this->packageTable . ' As sp', 'sp.payment_id', '=', 'p.id')
                ->join('knet_payments AS k', 'k.id', '=', 'p.payid')
                ->select(DB::raw('COALESCE(SUM(k.amount),0) as knet_amount'))
                ->where('p.card_type', 1)
                ->where('sp.module_id', 3);

        $CCAmountList = DB::table('payment_details As p')
                ->join($this->packageTable . ' As sp', 'sp.payment_id', '=', 'p.id')
                ->join('cc_payments AS c', 'c.id', '=', 'p.payid')
                ->select(DB::raw('COALESCE(SUM(c.amount),0) as cc_amount'))
                ->where('p.card_type', 2)
                ->where('sp.module_id', 3);

        $AmountList = DB::table('payment_details As p')
                        ->join($this->packageTable . ' As sp', 'sp.payment_id', '=', 'p.id')
                        ->select(DB::raw('COALESCE(SUM(sp.price),0) as fees'))->where('sp.module_id', 3);

        if (Session::has('reportModule3onlinePayments')) {
            $val = Session::get('reportModule3onlinePayments');
            //if Request having Date Range
            if (Session::has('reportModule3onlinePayments.start_date')) {
                $paymentList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
                $AmountList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
                $KnetAmountList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
                $CCAmountList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
            }

            // if Request having Member id
            if (Session::has('reportModule3onlinePayments.id')) {
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

        return view('admin.vendorPrintReports.fitflowMembershipOnlinePayments')
                        ->with('payments', $payments)
                        ->with('Amount', $Amount)
                        ->with('KnetAmount', $KnetAmount)
                        ->with('CCAmount', $CCAmount);
    }

    /**
     * Display a listing of the Fitflow Membership Expired.
     */
    public function fitflowMembershipBookings(Request $request) {

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('fitflowMembershipBookings-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        $tableArray = [];

        $this->table = 'subscribers_package_details';
        $this->bookingTable = 'bookings';

        $bookingList = DB::table($this->bookingTable . ' As b')
                ->join('classes', 'classes.id', '=', 'b.class_id')
                ->join('class_schedules', 'class_schedules.id', '=', 'b.schedule_id')
                ->join('registered_users', 'b.subscriber_id', '=', 'registered_users.id')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->join('vendors', 'vendors.id', '=', 'b.vendor_id')
                ->select('registered_users.name', 'vendors.name AS vendor_name', DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS class_name'), 'class_schedules.start', 'class_schedules.end', 'class_schedules.schedule_date', 'b.created_at');

        if (Session::has('reportM3Bookings')) {
            $val = Session::get('reportM3Bookings');

            if (Session::has('reportM3Bookings.vendor_id')) {
                $ID = $val['vendor_id'];
                $bookingList->where('b.vendor_id', $ID);
            }
            if (Session::has('reportM3Bookings.start_date')) {
                $bookingList->whereBetween('class_schedules.schedule_date', [$val['start_date'], $val['end_date']]);
            }
            // if Request having Subscriber id
            if (Session::has('reportM3Bookings.subscriber_id')) {
                $ID = $val['subscriber_id'];
                $bookingList->where('b.subscriber_id', $ID);
            }

            // if Request having Class id
            if (Session::has('reportM3Bookings.class_id')) {
                $ID = $val['class_id'];
                $bookingList->where('b.class_master_id', $ID);
            }

            // if Request having Start Time and End Time
            if (Session::has('reportM3Bookings.start_time')) {
                $bookingList->whereBetween('class_schedules.start', [$val['start_time'], $val['end_time']]);
            }
        }

        $bookingHistory = $bookingList->get();
        $Count = $bookingList->count();

         return view('admin.vendorPrintReports.fitflowMembershipBookings')
                        ->with('bookingHistory', $bookingHistory)
                        ->with('Count', $Count);
    }
    
    public function instructorSubscriptions(Request $request) {

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('reportInstructorSubscriptions-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');
        
        $this->instructorSubscriptionTable = 'instructor_subscribers_package_details';

        $tableArray = [];
        $InstructorSubscription = collect([]);

        //if Request having Date Range
        if (Session::has('reportInstructorSubscription') && Session::get('reportInstructorSubscription.vendor_id') != 0) {
            $val = Session::get('reportInstructorSubscription');
            $vendor_id = $val['vendor_id'];
            
            $this->table = 'v' . $vendor_id . '_members';

            $InstructorSubscriptionList = DB::table($this->instructorSubscriptionTable . ' As ins')
                        ->join($this->table.' As registered_users', 'ins.member_id', '=', 'registered_users.id')
                        ->select('registered_users.name AS subscriber', 'registered_users.mobile', 'ins.name_en As package_name', 'ins.price', 'ins.num_points', 'ins.num_booked', 'ins.created_at')
                        ->where('ins.vendor_id', $vendor_id);

            //if Request having Date Range
            if (Session::has('reportInstructorSubscription.start_date')) {
                $InstructorSubscriptionList->whereBetween('ins.created_at', [$val['start_date'], $val['end_date']]);
            }
            // if Request having Member id
            if (Session::has('reportInstructorSubscription.id')) {
                $val = Session::get('reportInstructorSubscription');
                $ID = $val['id'];
                $InstructorSubscriptionList->where('ins.member_id', $ID);
            }
            // if Request having Package Name
            if (Session::has('reportInstructorSubscription.name_en')) {
                $val = Session::get('reportInstructorSubscription');
                $name_en = $val['name_en'];
                $InstructorSubscriptionList->where('ins.name_en', 'like', "$name_en%");
            }

            $InstructorSubscription = $InstructorSubscriptionList->get();
        }
        return view('admin.vendorPrintReports.instructorSubscriptions')
                        ->with('InstructorSubscription', $InstructorSubscription);
    }


}
