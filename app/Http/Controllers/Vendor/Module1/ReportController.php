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

class ReportController extends Controller {

    protected $guard = 'vendor';
    protected $configName;
    protected $ViewAccess;
    protected $PrintAccess;

    public function __construct() {
        $this->middleware($this->guard);
        $this->configName = config('global.fitflowVendor');
        //$this->middleware('vendorPermission:reports');
    }

    /**
     * Display a listing of the Favourites.
     */
    public function favourite(Request $request) {

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('favourites-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('favourites-print');

        $favouriteList = DB::table('favourites')
                ->select('subscriber_id', 'created_at')
                ->where('vendor_id', VendorDetail::getID());

        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $session_array['start_date'] = $start_date;
            $session_array['end_date'] = $end_date;
            Session::set('reportFavourites', $session_array);
            Session::flash('reportFavourites', Session::get('reportFavourites'));
            $favouriteList->whereBetween('created_at', [$start_date, $end_date]);
        }
        $Favourites = $favouriteList->get();
        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Favourites)
                            ->editColumn('subscriber_id', function ($Favourites) {
                                //Get Subscriber name
                                $username = DB::table('registered_users')
                                        ->select('name')
                                        ->where('id', $Favourites->subscriber_id)
                                        ->first();
                                return $username->name;
                            })
                            ->editColumn('created_at', function ($Favourites) {
                                $newYear = new Carbon($Favourites->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->make();
        }

        return view('fitflowVendor.module1.reports.favourites')->with('PrintAccess', $this->PrintAccess);
    }

    /**
     * Display a listing of the Payments.
     */
    public function payment(Request $request) {

        $this->table = VendorDetail::getPrefix() . 'members';
        $this->invoiceTable = VendorDetail::getPrefix() . 'member_invoices';


        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('payments-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('payments-print');

        //Get Member & Invoice
        $invoiceList = DB::table($this->invoiceTable . ' As inv')
                ->join($this->table . ' As m', 'inv.member_id', '=', 'm.id')
                ->join('vendor_users As vu', 'vu.id', '=', 'inv.collected_by')
                ->select('inv.receipt_num', 'm.name', 'inv.package_name', 'inv.created_at', 'vu.name AS collected_by', 'inv.cash', 'inv.knet', 'inv.price', 'inv.start_date', 'inv.end_date');

        $invoiceAmountList = DB::table($this->invoiceTable . ' As inv')
                ->select(DB::raw('COALESCE(SUM(cash),0) as cash_amount')
                , DB::raw('COALESCE(SUM(knet),0) as knet_amount'), DB::raw('COALESCE(SUM(price),0) as fees'));


        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            Session::set('reportPayments', ['start_date' => $start_date, 'end_date' => $end_date]);
            Session::flash('reportPayments', Session::get('reportPayments'));
            $invoiceList->whereBetween('inv.created_at', [$start_date, $end_date]);
            $invoiceAmountList->whereBetween('inv.created_at', [$start_date, $end_date]);
        }

        // if Request having Member id
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            Session::set('reportPayments', ['id' => $ID]);
            Session::flash('reportPayments', Session::get('reportPayments'));
            $invoiceList->where('inv.member_id', $ID);
            $invoiceAmountList->where('inv.member_id', $ID);
        }
        $Invoices = $invoiceList->get();
        $invoiceAmount = $invoiceAmountList->first();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Invoices)
                            ->editColumn('created_at', function ($Invoices) {
                                $newYear = new Carbon($Invoices->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->with('invoiceAmount', $invoiceAmount)
                            ->make();
        }

        $Members = DB::table($this->table)
                ->select('name', 'id')
                ->where(array('status' => 1))
                ->whereNull('deleted_at')
                ->get();



        return view('fitflowVendor.module1.reports.payments')
                        ->with('Members', $Members)
                        ->with('invoiceAmount', $invoiceAmount)
                        ->with('PrintAccess', $this->PrintAccess);
    }

    /**
     * Display a listing of the Online Payments.
     */
    public function onlinePayment(Request $request) {

        $this->table = VendorDetail::getPrefix() . 'members';
        $this->packageTable = VendorDetail::getPrefix() . 'subscribers_package_details';


        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('onlinePayments-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('onlinePayments-print');

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
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            Session::set('reportonlinePayments', ['start_date' => $start_date, 'end_date' => $end_date]);
            Session::flash('reportonlinePayments', Session::get('reportonlinePayments'));
            $paymentList->whereBetween('p.post_date', [$start_date, $end_date]);
            $AmountList->whereBetween('p.post_date', [$start_date, $end_date]);
            $KnetAmountList->whereBetween('p.post_date', [$start_date, $end_date]);
            $CCAmountList->whereBetween('p.post_date', [$start_date, $end_date]);
        }

        // if Request having Member id
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            Session::set('reportonlinePayments', ['id' => $ID]);
            Session::flash('reportonlinePayments', Session::get('reportonlinePayments'));
            $paymentList->where('sp.member_id', $ID);
            $AmountList->where('sp.member_id', $ID);
            $KnetAmountList->where('sp.member_id', $ID);
            $CCAmountList->where('sp.member_id', $ID);
        }

        $payments = $paymentList->get();
        $Amount = $AmountList->first();
        $KnetAmount = $KnetAmountList->first();
        $CCAmount = $CCAmountList->first();


        //Ajax request
        if (request()->ajax()) {
            return Datatables::of($payments)
                            ->editColumn('post_date', function ($payments) {
                                $newYear = new Carbon($payments->post_date);
                                return $newYear->format('d/m/Y');
                            })
                            ->with('Amount', $Amount)
                            ->with('KnetAmount', $KnetAmount)
                            ->with('CCAmount', $CCAmount)
                            ->make();
        }

        $Members = DB::table($this->table)
                ->select('name', 'id')
                ->where(array('status' => 1))
                ->whereNull('deleted_at')
                ->get();

        return view('fitflowVendor.module1.reports.onlinePayments')
                        ->with('Members', $Members)
                        ->with('Amount', $Amount)
                        ->with('KnetAmount', $KnetAmount)
                        ->with('CCAmount', $CCAmount)
                        ->with('PrintAccess', $this->PrintAccess);
    }

    /**
     * Display a listing of the Subscription Expired.
     */
    public function subscriptionExpired(Request $request) {

        $this->table = VendorDetail::getPrefix() . 'members';

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('subscriptionExpired-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('subscriptionExpired-print');

        $MemberList = DB::table($this->table . ' As m')
                ->leftjoin('gender_types As g', 'g.id', '=', 'm.gender_id')
                ->select('m.name', 'm.email', 'm.mobile', 'g.name_en AS gender_name', 'm.package_name', 'm.end_date', DB::raw('CONCAT(DATE_FORMAT(m.start_date,"%d/%m/%Y"), "- ", DATE_FORMAT(m.end_date,"%d/%m/%Y")) AS period'), 'm.start_date', 'm.subscribed_from')
                ->whereNull('m.deleted_at')
                ->whereDate('m.end_date', '<', Carbon::now())
                ->groupby('m.id');


        // if Request having Pacakge name
        if ($request->has('name_en') && $request->get('name_en') != '') {
            $name_en = $request->get('name_en');
            Session::set('reportMemberExpired', ['name_en' => $name_en]);
            Session::flash('reportMemberExpired', Session::get('reportMemberExpired'));
            $MemberList->where('m.package_name', 'like', "$name_en%");
        }
        // if Member Type //GYM:0, Fitflow:1
        if ($request->has('member_type') && $request->get('member_type') != '') {
            $member_type = $request->get('member_type');
            Session::set('reportMemberExpired', ['member_type' => $member_type]);
            Session::flash('reportMemberExpired', Session::get('reportMemberExpired'));
            $MemberList->where('m.subscribed_from', $member_type);
        }

        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            Session::set('reportMemberExpired', ['start_date' => $start_date, 'end_date' => $end_date]);
            Session::flash('reportMemberExpired', Session::get('reportMemberExpired'));
            $MemberList->whereBetween('m.end_date', [$start_date, $end_date]);
        }

        // if Request having Member id
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            Session::set('reportMemberExpired', ['id' => $ID]);
            Session::flash('reportMemberExpired', Session::get('reportMemberExpired'));
            $MemberList->where('m.id', $ID);
        }
        // if Request having Gender id
        if ($request->has('gender_id') && $request->get('gender_id') != 0) {
            $GenderID = $request->get('gender_id');
            Session::set('reportMemberExpired', ['gender_id' => $GenderID]);
            Session::flash('reportMemberExpired', Session::get('reportMemberExpired'));
            $MemberList->where('m.gender_id', 'like', "$GenderID%");
        }

        $Member = $MemberList->get();
        $Count = $Member->count();

        //Get All Packages 
        $Packages = DB::table('vendor_packages')
                ->select('id', 'name_en')
                ->where('vendor_id', VendorDetail::getID())
                ->get();

        //Get All Genders 
        $Genders = DB::table('gender_types')
                ->select('id', 'name_en')
                ->limit(2)
                ->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Member)
                            ->editColumn('end_date', function ($Member) {
                                $newYear = new Carbon($Member->start_date);
                                $endYear = new Carbon($Member->end_date);
                                return $newYear->format('d/m/Y') . ' - ' . $endYear->format('d/m/Y');
                            })
                            ->make();
        }


        $Members = DB::table($this->table)
                ->select('name', 'id')
                ->where(array('status' => 1))
                ->whereNull('deleted_at')
                ->whereDate('end_date', '<', Carbon::now())
                ->get();

        return view('fitflowVendor.module1.reports.subscriptionExpired')
                        ->with('Packages', $Packages)
                        ->with('Genders', $Genders)
                        ->with('Members', $Members)
                        ->with('Count', $Count)
                        ->with('PrintAccess', $this->PrintAccess);
    }

    /**
     * Display a listing of the Subscription Expired.
     */
    public function subscriptions(Request $request) {

        $this->table = VendorDetail::getPrefix() . 'members';

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('subscriptions-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('subscriptions-print');

        $MemberList = DB::table($this->table . ' As m')
                ->leftjoin('gender_types As g', 'g.id', '=', 'm.gender_id')
                ->select('m.name', 'm.email', 'm.mobile', 'g.name_en AS gender_name', 'm.package_name', 'm.start_date', DB::raw('CONCAT(DATE_FORMAT(m.start_date,"%d/%m/%Y"), "- ", DATE_FORMAT(m.end_date,"%d/%m/%Y")) AS period'), 'm.end_date', 'm.subscribed_from')
                ->whereNull('m.deleted_at')
                ->whereDate('m.end_date', '>=', Carbon::now())
                ->groupby('m.id');


        // if Request having Pacakge name
        if ($request->has('name_en') && $request->get('name_en') != '') {
            $name_en = $request->get('name_en');
            Session::set('reportMemberSubscribed', ['name_en' => $name_en]);
            Session::flash('reportMemberSubscribed', Session::get('reportMemberSubscribed'));
            $MemberList->where('m.package_name', 'like', "$name_en%");
        }
        // if Member Type //GYM:0, Fitflow:1
        if ($request->has('member_type') && $request->get('member_type') != '') {
            $member_type = $request->get('member_type');
            Session::set('reportMemberSubscribed', ['member_type' => $member_type]);
            Session::flash('reportMemberSubscribed', Session::get('reportMemberSubscribed'));
            $MemberList->where('m.subscribed_from', $member_type);
        }

        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            Session::set('reportMemberSubscribed', ['start_date' => $start_date, 'end_date' => $end_date]);
            Session::flash('reportMemberSubscribed', Session::get('reportMemberSubscribed'));
            $MemberList->whereBetween('m.start_date', [$start_date, $end_date]);
        }

        // if Request having Member id
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            Session::set('reportMemberSubscribed', ['id' => $ID]);
            Session::flash('reportMemberSubscribed', Session::get('reportMemberSubscribed'));
            $MemberList->where('m.id', $ID);
        }

        // if Request having Gender id
        if ($request->has('gender_id') && $request->get('gender_id') != 0) {
            $GenderID = $request->get('gender_id');
            Session::set('reportMemberSubscribed', ['gender_id' => $GenderID]);
            Session::flash('reportMemberSubscribed', Session::get('reportMemberSubscribed'));
            $MemberList->where('m.gender_id', 'like', "$GenderID%");
        }

        // if Member Status //1Week:0, 2Week:1, #Week:2
        if ($request->has('member_status') && $request->get('member_status') != '') {
            $member_status = $request->get('member_status');
            $current = Carbon::now();
            $expiry = $current->addWeek($member_status);
            $expiry = $expiry->format('Y-m-d');
            $currentDate = Carbon::now()->format('Y-m-d');
            $session_array['current_date'] = $currentDate;
            $session_array['expiry'] = $expiry;
            Session::set('reportMemberSubscribed', $session_array);
            Session::flash('reportMemberSubscribed', Session::get('reportMemberSubscribed'));
            $MemberList->whereBetween('m.end_date', [$currentDate, $expiry]);
        }

        $Member = $MemberList->get();
        $Count = $Member->count();

        //Get All Packages 
        $Packages = DB::table('vendor_packages')
                ->select('id', 'name_en')
                ->where('vendor_id', VendorDetail::getID())
                ->get();

        //Get All Genders 
        $Genders = DB::table('gender_types')
                ->select('id', 'name_en')
                ->limit(2)
                ->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Member)
                            ->editColumn('end_date', function ($Member) {
                                $newYear = new Carbon($Member->start_date);
                                $endYear = new Carbon($Member->end_date);
                                return $newYear->format('d/m/Y') . ' - ' . $endYear->format('d/m/Y');
                            })
                            ->with('count', $Count)
                            ->make();
        }


        $Members = DB::table($this->table)
                ->select('name', 'id')
                ->where(array('status' => 1))
                ->whereNull('deleted_at')
                ->whereDate('end_date', '>', Carbon::now())
                ->get();

        return view('fitflowVendor.module1.reports.subscriptions')
                        ->with('Packages', $Packages)
                        ->with('Genders', $Genders)
                        ->with('Members', $Members)
                        ->with('Count', $Count)
                        ->with('PrintAccess', $this->PrintAccess);
    }

    //Instructor Subscription
    public function instructorSubscriptions(Request $request) {

        $this->instructorSubscriptionTable = 'instructor_subscribers_package_details';

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('reportInstructorSubscriptions-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('reportInstructorSubscriptions-print');

        $InstructorSubscriptionList = DB::table($this->instructorSubscriptionTable . ' As ins')
                ->join('registered_users', 'ins.member_id', '=', 'registered_users.id')
                ->select('ins.id AS subscription_package_id', 'registered_users.name AS subscriber', 'registered_users.mobile', 'ins.name_en As package_name', 'ins.price', 'ins.num_points', 'ins.num_booked', 'ins.created_at')
                ->where('vendor_id', VendorDetail::getID());



        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            Session::set('reportInstructorSubscription', ['start_date' => $start_date, 'end_date' => $end_date]);
            Session::flash('reportInstructorSubscription', Session::get('reportInstructorSubscription'));
            $InstructorSubscriptionList->whereBetween('ins.created_at', [$start_date, $end_date]);
        }

        // if Request having Member id
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            Session::set('reportInstructorSubscription', ['id' => $ID]);
            Session::flash('reportInstructorSubscription', Session::get('reportInstructorSubscription'));
            $InstructorSubscriptionList->where('ins.member_id', $ID);
        }

        // if Request having Pacakge name
        if ($request->has('name_en') && $request->get('name_en') != '') {
            $name_en = $request->get('name_en');
            Session::set('reportInstructorSubscription', ['name_en' => $name_en]);
            Session::flash('reportInstructorSubscription', Session::get('reportInstructorSubscription'));
            $InstructorSubscriptionList->where('ins.name_en', 'like', "$name_en%");
        }

        $InstructorSubscription = $InstructorSubscriptionList->get();
        

        //Get All Packages 
        $Packages = DB::table('instructor_packages')
                ->select('id', 'name_en')
                ->where('vendor_id', VendorDetail::getID())
                ->get();


        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($InstructorSubscription)
                            ->editColumn('created_at', function ($InstructorSubscription) {
                                $newYear = new Carbon($InstructorSubscription->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('action', function ($InstructorSubscription) {
                                return '<a data-val="' . $InstructorSubscription->subscription_package_id . '"  href="#Attendance" class="btn btn-danger tooltip-success btn-small subscriber_attendance" '
                                        . 'data-toggle="modal"  data-original-title="Attendance" title="Attendance"><i class="entypo-doc-text"></i></a>';
                            })                            
                            ->make();
        }


         $Members = DB::table($this->instructorSubscriptionTable . ' As ins')
                ->join('registered_users', 'ins.member_id', '=', 'registered_users.id')
                ->select('registered_users.name', 'registered_users.id')
                ->where('vendor_id', VendorDetail::getID())
                ->where(array('registered_users.status' => 1))
                ->whereNull('registered_users.deleted_at')
                 ->groupby('ins.member_id')
                ->get();

        return view('fitflowVendor.module1.reports.instructorSubscriptions')
                        ->with('Packages', $Packages)
                        ->with('Members', $Members)
                        ->with('PrintAccess', $this->PrintAccess);
    }

}
