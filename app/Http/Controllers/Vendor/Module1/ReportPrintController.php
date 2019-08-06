<?php

namespace App\Http\Controllers\Vendor\Module1;

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
        $this->middleware('vendorPermission:reports');
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
                ->join('registered_users AS ru', 'ru.id', '=', 'f.subscriber_id')
                ->select('ru.name', 'f.created_at')
                ->where('vendor_id', VendorDetail::getID());

        //if Request having Date Range        
        if (Session::has('reportFavourites')) {
            $val = Session::get('reportFavourites');
            $favouriteList->whereBetween('f.created_at', [$val['start_date'], $val['end_date']]);
        }
        $Favourites = $favouriteList->get();

        return view('fitflowVendor.module1.reportPrint.favourites')->with('Favourites', $Favourites);
    }

    /**
     * Display a listing of the Payments.
     */
    public function payment(Request $request) {

        $this->table = VendorDetail::getPrefix() . 'members';
        $this->invoiceTable = VendorDetail::getPrefix() . 'member_invoices';


        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('payments-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        
        //Get Member & Invoice
        $invoiceList = DB::table($this->invoiceTable . ' As inv')
                ->join($this->table . ' As m', 'inv.member_id', '=', 'm.id')
                ->join('vendor_users As vu', 'vu.id', '=', 'inv.collected_by')
                ->select('inv.receipt_num', 'm.name', 'inv.package_name', 'inv.created_at', 'vu.name AS collected_by', 'inv.cash', 'inv.knet', 'inv.price', 'inv.start_date', 'inv.end_date');

        $invoiceAmountList = DB::table($this->invoiceTable . ' As inv')
                ->select(DB::raw('COALESCE(SUM(cash),0) as cash_amount')
                , DB::raw('COALESCE(SUM(knet),0) as knet_amount'), DB::raw('COALESCE(SUM(price),0) as fees'));


        //if Request having Date Range
        if (Session::has('reportPayments')) {
            $val = Session::get('reportPayments');
            if (Session::has('reportPayments.start_date')) {
                $invoiceList->whereBetween('inv.created_at', [$val['start_date'], $val['end_date']]);
                $invoiceAmountList->whereBetween('inv.created_at', [$val['start_date'], $val['end_date']]);
            }
            // if Request having Member id
            if (Session::has('reportPayments.id')) {
                $val = Session::get('reportPayments');
                $ID = $val['id'];
                $invoiceList->where('inv.member_id', $ID);
                $invoiceAmountList->where('inv.member_id', $ID);
            }
        }

        $Invoices = $invoiceList->get();
        $invoiceAmount = $invoiceAmountList->first();

        return view('fitflowVendor.module1.reportPrint.payments')
                        ->with('Invoices', $Invoices)
                        ->with('invoiceAmount', $invoiceAmount);
    }

    /**
     * Display a listing of the Online Payments.
     */
    public function onlinePayment(Request $request) {

        $this->table = VendorDetail::getPrefix() . 'members';
        $this->packageTable = VendorDetail::getPrefix() . 'subscribers_package_details';


        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('onlinePayments-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        //Get package payment details
        $paymentList = DB::table('payment_details As p')
                ->join($this->packageTable . ' As sp', 'sp.payment_id', '=', 'p.id')
                ->leftjoin($this->table . ' As m', 'sp.member_id', '=', 'm.id')
                ->select('m.name', 'm.package_name', 'p.reference_id', 'p.amount', 'p.post_date', DB::raw('(CASE WHEN p.card_type = 1 THEN "KNET" ELSE "Credit Card" END) AS payment_method'))
                ->where('sp.vendor_id', VendorDetail::getID())
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

        //if Request having Date Range
        if (Session::has('reportonlinePayments')) {
            $val = Session::get('reportonlinePayments');
            if (Session::has('reportonlinePayments.start_date')) {
                $paymentList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
                $AmountList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
                $KnetAmountList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
                $CCAmountList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
            }
            // if Request having Member id
            if (Session::has('reportonlinePayments.id')) {
                $val = Session::get('reportonlinePayments');
                $ID = $val['id'];
                $paymentList->where('sp.member_id', $ID);
                $AmountList->where('sp.member_id', $ID);
                $KnetAmountList->where('sp.member_id', $ID);
                $CCAmountList->where('sp.member_id', $ID);
            }
        }

        $payments = $paymentList->get();
        $Amount = $AmountList->first();
        $KnetAmount = $KnetAmountList->first();
        $CCAmount = $CCAmountList->first();


        return view('fitflowVendor.module1.reportPrint.onlinePayments')
                        ->with('payments', $payments)
                        ->with('Amount', $Amount)
                        ->with('KnetAmount', $KnetAmount)
                        ->with('CCAmount', $CCAmount);
    }

    /**
     * Display a listing of the Subscription Expired.
     */
    public function subscriptionExpired(Request $request) {

        $this->table = VendorDetail::getPrefix() . 'members';

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('subscriptionExpired-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');


        $MemberList = DB::table($this->table . ' As m')
                ->leftjoin('gender_types As g', 'g.id', '=', 'm.gender_id')
                ->select('m.name', 'm.email', 'm.mobile', 'g.name_en AS gender_name', 'm.package_name', 'm.start_date', 'm.end_date', 'm.subscribed_from')
                ->whereNull('m.deleted_at')
                ->whereDate('m.end_date', '<', Carbon::now())
                ->groupby('m.id');
        
        
        if (Session::has('reportMemberExpired')) {
            $val = Session::get('reportMemberExpired');
             //if Request having Date Range
            if (Session::has('reportMemberExpired.start_date')) {
                $MemberList->whereBetween('m.end_date', [$val['start_date'], $val['end_date']]);
            }
            // if Request having Member id
            if (Session::has('reportMemberExpired.id')) {
                $val = Session::get('reportMemberExpired');
                $ID = $val['id'];
                $MemberList->where('m.id', $ID);
            }
             // if Request having Gender id
            if (Session::has('reportMemberExpired.gender_id')) {
                $val = Session::get('reportMemberExpired');
                $GenderID = $val['gender_id'];
                $MemberList->where('m.gender_id', 'like', "$GenderID%");
            }
            // if Request having Package Name
            if (Session::has('reportMemberExpired.name_en')) {
                $val = Session::get('reportMemberExpired');
                $name_en = $val['name_en'];
                $MemberList->where('m.package_name', 'like', "$name_en%");
            }
            // if Request having Member Type
            if (Session::has('reportMemberExpired.member_type')) {
                $val = Session::get('reportMemberExpired');
                $member_type = $val['member_type'];
                $MemberList->where('m.subscribed_from', 'like', "$member_type%");
            }
        }

        $Members = $MemberList->get();

        return view('fitflowVendor.module1.reportPrint.subscriptionExpired')
                        ->with('Members', $Members);
    }

    /**
     * Display a listing of the Subscription Expired.
     */
    public function subscriptions(Request $request) {

        $this->table = VendorDetail::getPrefix() . 'members';

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('subscriptions-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');


       $MemberList = DB::table($this->table . ' As m')
                ->leftjoin('gender_types As g', 'g.id', '=', 'm.gender_id')
                ->select('m.name', 'm.email', 'm.mobile', 'g.name_en AS gender_name', 'm.package_name', 'm.start_date', 'm.end_date', 'm.subscribed_from')
                ->whereNull('m.deleted_at')
                ->whereDate('m.end_date', '>=', Carbon::now())
                ->groupby('m.id');
        
        
        if (Session::has('reportMemberSubscribed')) {
            $val = Session::get('reportMemberSubscribed');
             //if Request having Date Range
            if (Session::has('reportMemberSubscribed.start_date')) {
                $MemberList->whereBetween('m.start_date', [$val['start_date'], $val['end_date']]);
            }
            // if Request having Member id
            if (Session::has('reportMemberSubscribed.id')) {
                $val = Session::get('reportMemberSubscribed');
                $ID = $val['id'];
                $MemberList->where('m.id', $ID);
            }
            // if Request having Gender id
            if (Session::has('reportMemberSubscribed.gender_id')) {
                $val = Session::get('reportMemberSubscribed');
                $GenderID = $val['gender_id'];
                $MemberList->where('m.gender_id', 'like', "$GenderID%");
            }
            // if Request having Package Name
            if (Session::has('reportMemberSubscribed.name_en')) {
                $val = Session::get('reportMemberSubscribed');
                $name_en = $val['name_en'];
                $MemberList->where('m.package_name', 'like', "$name_en%");
            }
            // if Request having Member Type
            if (Session::has('reportMemberSubscribed.member_type')) {
                $val = Session::get('reportMemberSubscribed');
                $member_type = $val['member_type'];
                $MemberList->where('m.subscribed_from', 'like', "$member_type%");
            }
            // if Request having Member Type
           if (Session::has('reportMemberSubscribed.expiry')) {
                $MemberList->whereBetween('m.end_date', [$val['current_date'], $val['expiry']]);
            }
        }

        $Members = $MemberList->get();

        return view('fitflowVendor.module1.reportPrint.subscriptions')
                        ->with('Members', $Members);
}
   //Instruction Subscriptions
     public function instructorSubscriptions(Request $request) {

       $this->instructorSubscriptionTable = 'instructor_subscribers_package_details';


        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('reportInstructorSubscriptions-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        $InstructorSubscriptionList = DB::table($this->instructorSubscriptionTable . ' As ins')
                ->join('registered_users', 'ins.member_id', '=', 'registered_users.id')
                ->select('registered_users.name AS subscriber', 'registered_users.mobile', 'ins.name_en As package_name', 'ins.price', 'ins.num_points', 'ins.num_booked', 'ins.created_at')
                ->where('vendor_id', VendorDetail::getID());


        if (Session::has('reportInstructorSubscription')) {
            $val = Session::get('reportInstructorSubscription');
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
            
        }

        $InstructorSubscription = $InstructorSubscriptionList->get();

        return view('fitflowVendor.module1.reportPrint.instructorSubscriptions')
                        ->with('InstructorSubscription', $InstructorSubscription);
    }

}
