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

class VendorReportController extends Controller {

    protected $guard = 'auth';
    protected $ViewAccess;
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
        $this->ViewAccess = Permit::AccessPermission('vendorfavourites-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('vendorfavourites-print');

        $favouriteList = DB::table('favourites AS f')
                ->join('vendors As v', 'v.id', '=', 'f.vendor_id')
                ->join('registered_users As ru', 'ru.id', '=', 'f.subscriber_id')
                ->select('ru.name As subscriber', 'v.name as vendor', 'f.created_at');

        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $session_array['start_date'] = $start_date;
            $session_array['end_date'] = $end_date;
            Session::set('reportVendorFavourites', $session_array);
            Session::flash('reportVendorFavourites', Session::get('reportVendorFavourites'));
            $favouriteList->whereBetween('f.created_at', [$start_date, $end_date]);
        }

        //if Request having Vendor ID
        if ($request->has('vendor_id') && $request->get('vendor_id') != 0) {
            $ID = $request->get('vendor_id');
            $session_array['vendor_id'] = $ID;
            Session::set('reportVendorFavourites', $session_array);
            Session::flash('reportVendorFavourites', Session::get('reportVendorFavourites'));
            $favouriteList->where('f.vendor_id', $ID);
        }

        //if Request having Subscriber ID
        if ($request->has('subscriber_id') && $request->get('subscriber_id') != 0) {
            $ID = $request->get('subscriber_id');
            $session_array['subscriber_id'] = $ID;
            Session::set('reportVendorFavourites', $session_array);
            Session::flash('reportVendorFavourites', Session::get('reportVendorFavourites'));
            $favouriteList->where('f.subscriber_id', $ID);
        }

        $Favourites = $favouriteList->get();
        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Favourites)
                            ->editColumn('created_at', function ($Favourites) {
                                $newYear = new Carbon($Favourites->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->make();
        }

        //Vendor List
        $Vendors = DB::table('vendors')
                ->select('name', 'id')
                ->whereNull('deleted_at')
                ->get();

        //Subscriber List
        $Subscribers = DB::table('registered_users')
                ->select('name', 'id')
                ->whereNull('deleted_at')
                ->get();


        return view('admin.vendorReports.favourites')
                        ->with('PrintAccess', $this->PrintAccess)
                        ->with('Vendors', $Vendors)
                        ->with('Subscribers', $Subscribers);
    }

    /**
     * Display a listing of the Payments.
     */
    public function payment(Request $request) {


        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('vendorPayments-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('vendorPayments-print');

        //Vendor List
        $Vendors = DB::table('vendors')
                ->select('name', 'id')
                ->whereNull('deleted_at')
                ->get();


        $Members = collect([]);
        $Invoices = collect([]);
        $invoiceAmount = new \stdClass();
        $invoiceAmount->fees = '0.000';
        $invoiceAmount->cash_amount = '0.000';
        $invoiceAmount->knet_amount = '0.000';
        $str = '';

        //Ajax request
        if (request()->ajax()) {

            if ($request->has('vendor_id') && $request->get('vendor_id') == 0) {
                $vendor_id = $request->get('vendor_id');
                $session_array['vendor_id'] = $vendor_id;
                Session::set('reportVendorPayments', $session_array);
                Session::flash('reportVendorPayments', Session::get('reportVendorPayments'));
                $Members = collect([]);
                $Invoices = collect([]);
                $invoiceAmount = collect([]);
            } else {
                $vendor_id = $request->get('vendor_id');
                $session_array['vendor_id'] = $vendor_id;
                Session::set('reportVendorPayments', $session_array);
                Session::flash('reportVendorPayments', Session::get('reportVendorPayments'));
                $this->table = 'v' . $vendor_id . '_members';
                $this->invoiceTable = 'v' . $vendor_id . '_member_invoices';

                $Members = DB::table($this->table)
                        ->select('name', 'id')
                        ->where(array('status' => 1))
                        ->whereNull('deleted_at')
                        ->get();
                $str .= '<option value="0">--All--</option>';
                foreach ($Members as $Member) {
                    $str .= '<option value="' . $Member->id . '">' . $Member->name . '</option>';
                }

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
                    $session_array['start_date'] = $start_date;
                    $session_array['end_date'] = $end_date;
                    Session::set('reportVendorPayments', $session_array);
                    Session::flash('reportVendorPayments', Session::get('reportVendorPayments'));
                    $invoiceList->whereBetween('inv.created_at', [$start_date, $end_date]);
                    $invoiceAmountList->whereBetween('inv.created_at', [$start_date, $end_date]);
                }

                // if Request having Member id
                if ($request->has('id') && $request->get('id') != 0) {
                    $ID = $request->get('id');
                    $session_array['id'] = $ID;
                    Session::set('reportVendorPayments', $session_array);
                    Session::flash('reportVendorPayments', Session::get('reportVendorPayments'));
                    $invoiceList->where('inv.member_id', $ID);
                    $invoiceAmountList->where('inv.member_id', $ID);
                }
                $Invoices = $invoiceList->get();
                $invoiceAmount = $invoiceAmountList->first();
            }


            return Datatables::of($Invoices)
                            ->editColumn('created_at', function ($Invoices) {
                                $newYear = new Carbon($Invoices->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->with('invoiceAmount', $invoiceAmount)
                            ->with('str', $str)
                            ->make();
        }


        return view('admin.vendorReports.payments')
                        ->with('Members', $Members)
                        ->with('Vendors', $Vendors)
                        ->with('invoiceAmount', $invoiceAmount)
                        ->with('PrintAccess', $this->PrintAccess);
    }

    /**
     * Display a listing of the Online Payments.
     */
    public function onlinePayment(Request $request) {

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('vendorOnlinePayments-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('vendorOnlinePayments-print');

        //Vendor List
        $Vendors = DB::table('vendors')
                ->select('name', 'id')
                ->whereNull('deleted_at')
                ->get();


        $Members = collect([]);
        $payments = collect([]);
        $Amount = new \stdClass();
        $Amount->fees = '0.000';
        $KnetAmount = new \stdClass();
        $KnetAmount->knet_amount = '0.000';
        $CCAmount = new \stdClass();
        $CCAmount->cc_amount = '0.000';
        $str = '';



        //Ajax request
        if (request()->ajax()) {

            if ($request->has('vendor_id') && $request->get('vendor_id') == 0) {
                $vendor_id = $request->get('vendor_id');
                $session_array['vendor_id'] = $vendor_id;
                Session::set('reportVendorOnlinePayments', $session_array);
                Session::flash('reportVendorOnlinePayments', Session::get('reportVendorOnlinePayments'));
                $Members = collect([]);
                $payments = collect([]);
                $Amount = new \stdClass();
                $Amount->fees = '0.000';
                $KnetAmount = new \stdClass();
                $KnetAmount->knet_amount = '0.000';
                $CCAmount = new \stdClass();
                $CCAmount->cc_amount = '0.000';
            } else {

                $vendor_id = $request->get('vendor_id');
                $session_array['vendor_id'] = $vendor_id;
                Session::set('reportVendorOnlinePayments', $session_array);
                Session::flash('reportVendorOnlinePayments', Session::get('reportVendorOnlinePayments'));
                $this->table = 'v' . $vendor_id . '_members';
                $this->packageTable = 'v' . $vendor_id . '_subscribers_package_details';

                $Members = DB::table($this->table)
                        ->select('name', 'id')
                        ->where(array('status' => 1))
                        ->whereNull('deleted_at')
                        ->get();
                $str .= '<option value="0">--All--</option>';
                foreach ($Members as $Member) {
                    $str .= '<option value="' . $Member->id . '">' . $Member->name . '</option>';
                }

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
                                ->where('p.card_type', 1)->where('sp.module_id', 1);

                $CCAmountList = DB::table('payment_details As p')
                                ->join($this->packageTable . ' As sp', 'sp.payment_id', '=', 'p.id')
                                ->join('cc_payments AS c', 'c.id', '=', 'p.payid')
                                ->select(DB::raw('COALESCE(SUM(c.amount),0) as cc_amount'))
                                ->where('p.card_type', 2)->where('sp.module_id', 1);

                $AmountList = DB::table('payment_details As p')
                                ->join($this->packageTable . ' As sp', 'sp.payment_id', '=', 'p.id')
                                ->select(DB::raw('COALESCE(SUM(sp.price),0) as fees'))->where('sp.module_id', 1);

                //if Request having Date Range
                if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
                    $start_date = $request->get('start_date');
                    $end_date = $request->get('end_date');
                    $session_array['start_date'] = $start_date;
                    $session_array['end_date'] = $end_date;
                    Session::set('reportVendorOnlinePayments', $session_array);
                    Session::flash('reportVendorOnlinePayments', Session::get('reportVendorOnlinePayments'));
                    $paymentList->whereBetween('p.post_date', [$start_date, $end_date]);
                    $AmountList->whereBetween('p.post_date', [$start_date, $end_date]);
                    $KnetAmountList->whereBetween('p.post_date', [$start_date, $end_date]);
                    $CCAmountList->whereBetween('p.post_date', [$start_date, $end_date]);
                }

                // if Request having Member id
                if ($request->has('id') && $request->get('id') != 0) {
                    $ID = $request->get('id');
                    $session_array['id'] = $ID;
                    Session::set('reportVendorOnlinePayments', $session_array);
                    Session::flash('reportVendorOnlinePayments', Session::get('reportVendorOnlinePayments'));
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

            return Datatables::of($payments)
                            ->editColumn('post_date', function ($payments) {
                                $newYear = new Carbon($payments->post_date);
                                return $newYear->format('d/m/Y');
                            })
                            ->with('Amount', $Amount)
                            ->with('KnetAmount', $KnetAmount)
                            ->with('CCAmount', $CCAmount)
                            ->with('str', $str)
                            ->make();
        }


        return view('admin.vendorReports.onlinePayments')
                        ->with('Members', $Members)
                        ->with('Vendors', $Vendors)
                        ->with('Amount', $Amount)
                        ->with('KnetAmount', $KnetAmount)
                        ->with('CCAmount', $CCAmount)
                        ->with('PrintAccess', $this->PrintAccess);
    }

    /**
     * Display a listing of the Subscription Expired.
     */
    public function subscriptionExpired(Request $request) {

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('VendorSubscriptionExpired-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('VendorSubscriptionExpired-print');

        //Vendor List
        $Vendors = DB::table('vendors')
                ->select('name', 'id')
                ->whereNull('deleted_at')
                ->get();


        $Members = collect([]);
        $Member = collect([]);
        $Packages = collect([]);
        $str = '';
        $packageStr = '';
        $Count = 0;

        //Ajax request
        if (request()->ajax()) {

            if ($request->has('vendor_id') && $request->get('vendor_id') == 0) {
                $vendor_id = $request->get('vendor_id');
                $session_array['vendor_id'] = $vendor_id;
                Session::set('reportVendorSubscriptionExpired', $session_array);
                Session::flash('reportVendorSubscriptionExpired', Session::get('reportVendorSubscriptionExpired'));
                $Members = collect([]);
            } else {
                $vendor_id = $request->get('vendor_id');
                $session_array['vendor_id'] = $vendor_id;
                Session::set('reportVendorSubscriptionExpired', $session_array);
                Session::flash('reportVendorSubscriptionExpired', Session::get('reportVendorSubscriptionExpired'));
                $this->table = 'v' . $vendor_id . '_members';

                $Members = DB::table($this->table)
                        ->select('name', 'id')
                        ->where(array('status' => 1))
                        ->whereNull('deleted_at')
                        ->whereDate('end_date', '<', Carbon::now())
                        ->get();

                $str .= '<option value="0">--All--</option>';
                foreach ($Members as $Member) {
                    $str .= '<option value="' . $Member->id . '">' . $Member->name . '</option>';
                }

                $MemberList = DB::table($this->table . ' As m')
                        ->leftjoin('gender_types As g', 'g.id', '=', 'm.gender_id')
                        ->select('m.name', 'm.email', 'm.mobile', 'g.name_en AS gender_name', 'm.package_name', 'm.end_date'
                                , DB::raw('CONCAT(DATE_FORMAT(m.start_date,"%d/%m/%Y"), "- ", DATE_FORMAT(m.end_date,"%d/%m/%Y")) AS period')
                                , 'm.start_date', 'm.subscribed_from')
                        ->whereNull('m.deleted_at')
                        ->whereDate('m.end_date', '<', Carbon::now())
                        ->groupby('m.id');

                //Get All Packages 
                $Packages = DB::table('vendor_packages')
                        ->where(array('vendor_id' => $vendor_id))
                        ->select('id', 'name_en')
                        ->get();

                $packageStr .= '<option value=" ">--All--</option>';
                foreach ($Packages as $Package) {
                    $packageStr .= '<option value="' . $Package->id . '">' . $Package->name_en . '</option>';
                }


                // if Request having Pacakge name
                if ($request->has('name_en') && $request->get('name_en') != '') {
                    $name_en = $request->get('name_en');
                    $session_array['name_en'] = $name_en;
                    Session::set('reportVendorSubscriptionExpired', $session_array);
                    Session::flash('reportVendorSubscriptionExpired', Session::get('reportVendorSubscriptionExpired'));
                    $MemberList->where('m.package_name', 'like', "$name_en%");
                }
                // if Member Type //GYM:0, Fitflow:1
                if ($request->has('member_type') && $request->get('member_type') != '') {
                    $member_type = $request->get('member_type');
                    $session_array['member_type'] = $member_type;
                    Session::set('reportVendorSubscriptionExpired', $session_array);
                    Session::flash('reportVendorSubscriptionExpired', Session::get('reportVendorSubscriptionExpired'));
                    $MemberList->where('m.subscribed_from', $member_type);
                }

                //if Request having Date Range
                if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
                    $start_date = $request->get('start_date');
                    $end_date = $request->get('end_date');
                    $session_array['start_date'] = $start_date;
                    $session_array['end_date'] = $end_date;
                    Session::set('reportVendorSubscriptionExpired', $session_array);
                    Session::flash('reportVendorSubscriptionExpired', Session::get('reportVendorSubscriptionExpired'));
                    $MemberList->whereBetween('m.end_date', [$start_date, $end_date]);
                }

                // if Request having Member id
                if ($request->has('id') && $request->get('id') != 0) {
                    $ID = $request->get('id');
                    $session_array['id'] = $ID;
                    Session::set('reportVendorSubscriptionExpired', $session_array);
                    Session::flash('reportVendorSubscriptionExpired', Session::get('reportVendorSubscriptionExpired'));
                    $MemberList->where('m.id', $ID);
                }

                // if Request having Gender id
                if ($request->has('gender_id') && $request->get('gender_id') != 0) {
                    $GenderID = $request->get('gender_id');
                    $session_array['gender_id'] = $GenderID;
                    Session::set('reportVendorSubscriptionExpired', $session_array);
                    Session::flash('reportVendorSubscriptionExpired', Session::get('reportVendorSubscriptionExpired'));
                    $MemberList->where('m.gender_id', 'like', "$GenderID%");
                }

                $Member = $MemberList->get();
                $Count = $Member->count();
            }


            return Datatables::of($Member)
                            ->editColumn('end_date', function ($Member) {
                                $newYear = new Carbon($Member->start_date);
                                $endYear = new Carbon($Member->end_date);
                                return $newYear->format('d/m/Y') . ' - ' . $endYear->format('d/m/Y');
                            })
                            ->with('str', $str)
                            ->with('packageStr', $packageStr)
                            ->with('count', $Count)
                            ->make();
        }

        //Get All Genders 
        $Genders = DB::table('gender_types')
                ->select('id', 'name_en')
                ->limit(2)
                ->get();


        return view('admin.vendorReports.subscriptionExpired')
                        ->with('Packages', $Packages)
                        ->with('Genders', $Genders)
                        ->with('Vendors', $Vendors)
                        ->with('Members', $Members)
                        ->with('Count', $Count)
                        ->with('PrintAccess', $this->PrintAccess);
    }

    /**
     * Display a listing of the Subscription Expired.
     */
    public function subscriptions(Request $request) {

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('VendorSubscriptions-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('VendorSubscriptions-print');

        //Vendor List
        $Vendors = DB::table('vendors')
                ->select('name', 'id')
                ->whereNull('deleted_at')
                ->get();



        $Members = collect([]);
        $Packages = collect([]);
        $str = '';
        $packageStr = '';
        $Count = 0;
        $m = 0;

        //Ajax request
        if (request()->ajax()) {

            if ($request->has('vendor_id') && $request->get('vendor_id') == 0) {
                $vendor_id = $request->get('vendor_id');
                $session_array['vendor_id'] = $vendor_id;
                Session::set('reportVendorSubscriptions', $session_array);
                Session::flash('reportVendorSubscriptions', Session::get('reportVendorSubscriptions'));
                $Members = collect([]);
                $Packages = collect([]);
            } else {
                $m = 1;
                $vendor_id = $request->get('vendor_id');
                $session_array['vendor_id'] = $vendor_id;
                Session::set('reportVendorSubscriptions', $session_array);
                Session::flash('reportVendorSubscriptions', Session::get('reportVendorSubscriptions'));
                $this->table = 'v' . $vendor_id . '_members';

                $Members = DB::table($this->table)
                        ->select('name', 'id')
                        ->where(array('status' => 1))
                        ->whereNull('deleted_at')
                        ->whereDate('end_date', '>=', Carbon::now())
                        ->get();

                //Get All Packages 
                $Packages = DB::table('vendor_packages')
                        ->where(array('vendor_id' => $vendor_id))
                        ->select('id', 'name_en')
                        ->get();

                $str .= '<option value="0">--All--</option>';
                foreach ($Members as $Member) {
                    $str .= '<option value="' . $Member->id . '">' . $Member->name . '</option>';
                }

                $packageStr .= '<option value=" ">--All--</option>';
                foreach ($Packages as $Package) {
                    $packageStr .= '<option value="' . $Package->id . '">' . $Package->name_en . '</option>';
                }

                $MemberList = DB::table($this->table . ' As m')
                        ->leftjoin('gender_types As g', 'g.id', '=', 'm.gender_id')
                        ->select('m.name', 'm.email', 'm.mobile', 'g.name_en AS gender_name', 'm.package_name', 'm.start_date'
                                , DB::raw('CONCAT(DATE_FORMAT(m.start_date,"%d/%m/%Y"), "- ", DATE_FORMAT(m.end_date,"%d/%m/%Y")) AS period')
                                , 'm.end_date', 'm.subscribed_from')
                        ->whereNull('m.deleted_at')
                        ->whereDate('m.end_date', '>=', Carbon::now())
                        ->groupby('m.id');


                // if Request having Pacakge name
                if ($request->has('name_en') && $request->get('name_en') != '') {
                    $name_en = $request->get('name_en');
                    $session_array['name_en'] = $name_en;
                    Session::set('reportVendorSubscriptions', $session_array);
                    Session::flash('reportVendorSubscriptions', Session::get('reportVendorSubscriptions'));
                    $MemberList->where('m.package_name', 'like', "$name_en%");
                }
                // if Member Type //GYM:0, Fitflow:1
                if ($request->has('member_type') && $request->get('member_type') != '') {
                    $member_type = $request->get('member_type');
                    $session_array['member_type'] = $member_type;
                    Session::set('reportVendorSubscriptions', $session_array);
                    Session::flash('reportVendorSubscriptions', Session::get('reportVendorSubscriptions'));
                    $MemberList->where('m.subscribed_from', $member_type);
                }

                //if Request having Date Range
                if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
                    $start_date = $request->get('start_date');
                    $end_date = $request->get('end_date');
                    $session_array['start_date'] = $start_date;
                    $session_array['end_date'] = $end_date;
                    Session::set('reportVendorSubscriptions', $session_array);
                    Session::flash('reportVendorSubscriptions', Session::get('reportVendorSubscriptions'));
                    $MemberList->whereBetween('m.end_date', [$start_date, $end_date]);
                }

                // if Request having Member id
                if ($request->has('id') && $request->get('id') != 0) {
                    $ID = $request->get('id');
                    $session_array['id'] = $ID;
                    Session::set('reportVendorSubscriptions', $session_array);
                    Session::flash('reportVendorSubscriptions', Session::get('reportVendorSubscriptions'));
                    $MemberList->where('m.id', $ID);
                }
                // if Request having Gender id
                if ($request->has('gender_id') && $request->get('gender_id') != 0) {
                    $GenderID = $request->get('gender_id');
                    $session_array['gender_id'] = $GenderID;
                    Session::set('reportVendorSubscriptions', $session_array);
                    Session::flash('reportVendorSubscriptions', Session::get('reportVendorSubscriptions'));
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
                    Session::set('reportVendorSubscriptions', $session_array);
                    Session::flash('reportVendorSubscriptions', Session::get('reportVendorSubscriptions'));
                    $MemberList->whereBetween('m.end_date', [$currentDate, $expiry]);
                }
            }

            if ($m != 0) {
                $Member = $MemberList->get();
                $Count = $Member->count();
            } else {
                $Member = collect([]);
                $Count = 0;
            }

            return Datatables::of($Member)
                            ->with('str', $str)
                            ->with('packageStr', $packageStr)
                            ->with('count', $Count)
                            ->make();
        }

        //Get All Genders 
        $Genders = DB::table('gender_types')
                ->select('id', 'name_en')
                ->limit(2)
                ->get();

        return view('admin.vendorReports.subscriptions')
                        ->with('Packages', $Packages)
                        ->with('Genders', $Genders)
                        ->with('Vendors', $Vendors)
                        ->with('Members', $Members)
                        ->with('Count', $Count)
                        ->with('PrintAccess', $this->PrintAccess);
    }

    /**
     * Display a listing of the Class Subscription.
     */
    public function classSubscriptions(Request $request) {

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('classSubscriptions-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('classSubscriptions-print');

        //Vendor List
        $Vendors = DB::table('vendors')
                ->select('name', 'id')
                ->whereNull('deleted_at')
                ->get();

        $Subscribers = collect([]);
        $Packages = collect([]);
        $Member = collect([]);
        $str = '';
        $packageStr = '';
        $Count = 0;

        //Ajax request
        if (request()->ajax()) {

            if ($request->has('vendor_id') && $request->get('vendor_id') == 0) {
                $vendor_id = $request->get('vendor_id');
                $session_array['vendor_id'] = $vendor_id;
                Session::set('reportModule2Subscriber', $session_array);
                Session::flash('reportModule2Subscriber', Session::get('reportModule2Subscriber'));
                $Subscribers = collect([]);
                $Packages = collect([]);
            } else {
                $vendor_id = $request->get('vendor_id');
                $session_array['vendor_id'] = $vendor_id;
                Session::set('reportModule2Subscriber', $session_array);
                Session::flash('reportModule2Subscriber', Session::get('reportModule2Subscriber'));
                $this->packageTable = 'v' . $vendor_id . '_subscribers_package_details';

                //Get All Subscribers 
                $Subscribers = DB::table($this->packageTable . ' As spd')
                        ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                        ->select('spd.subscriber_id', 'registered_users.name')
                        ->groupby('spd.subscriber_id')
                        ->where('spd.active_status', 1)
                        ->where('spd.module_id', 2)
                        ->get();

                //Get All Packages 
                $Packages = DB::table('class_packages')
                        ->where(array('vendor_id' => $vendor_id))
                        ->select('id', 'name_en')
                        ->get();

                $str .= '<option value="0">--All--</option>';
                foreach ($Subscribers as $Subscriber) {
                    $str .= '<option value="' . $Subscriber->subscriber_id . '">' . $Subscriber->name . '</option>';
                }

                $packageStr .= '<option value=" ">--All--</option>';
                foreach ($Packages as $Package) {
                    $packageStr .= '<option value="' . $Package->id . '">' . $Package->name_en . '</option>';
                }

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
                if ($request->has('name_en') && $request->get('name_en') != '') {
                    $name_en = $request->get('name_en');
                    $session_array['name_en'] = $name_en;
                    Session::set('reportModule2Subscriber', $session_array);
                    Session::flash('reportModule2Subscriber', Session::get('reportModule2Subscriber'));
                    $SubscribersList->where('spd.name_en', 'like', "$name_en%");
                }

                //if Request having Date Range
                if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
                    $start_date = $request->get('start_date');
                    $end_date = $request->get('end_date');
                    $session_array['start_date'] = $start_date;
                    $session_array['end_date'] = $end_date;
                    Session::set('reportModule2Subscriber', $session_array);
                    Session::flash('reportModule2Subscriber', Session::get('reportModule2Subscriber'));
                    $SubscribersList->whereBetween('spd.start_date', [$start_date, $end_date]);
                }

                // if Request having Member id
                if ($request->has('id') && $request->get('id') != 0) {
                    $ID = $request->get('id');
                    $session_array['id'] = $ID;
                    Session::set('reportModule2Subscriber', $session_array);
                    Session::flash('reportModule2Subscriber', Session::get('reportModule2Subscriber'));
                    $SubscribersList->where('spd.subscriber_id', $ID);
                }
                // if Request having Gender id
                if ($request->has('gender_id') && $request->get('gender_id') != 0) {
                    $GenderID = $request->get('gender_id');
                    $session_array['gender_id'] = $GenderID;
                    Session::set('reportModule2Subscriber', $session_array);
                    Session::flash('reportModule2Subscriber', Session::get('reportModule2Subscriber'));
                    $SubscribersList->where('registered_users.gender_id', 'like', "$GenderID%");
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
                    Session::set('reportModule2Subscriber', $session_array);
                    Session::flash('reportModule2Subscriber', Session::get('reportModule2Subscriber'));
                    $SubscribersList->whereBetween('spd.end_date', [$currentDate, $expiry]);
                }


                $Member = $SubscribersList->get();
                $Count = $Member->count();
            }

            return Datatables::of($Member)
                            ->with('str', $str)
                            ->with('packageStr', $packageStr)
                            ->with('count', $Count)
                            ->make();
        }

        //Get All Genders 
        $Genders = DB::table('gender_types')
                ->select('id', 'name_en')
                ->limit(2)
                ->get();

        return view('admin.vendorReports.classSubscriptions')
                        ->with('Packages', $Packages)
                        ->with('Genders', $Genders)
                        ->with('Vendors', $Vendors)
                        ->with('Subscribers', $Subscribers)
                        ->with('Count', $Count)
                        ->with('PrintAccess', $this->PrintAccess);
    }

    /**
     * Display a listing of the Class Subscription Expired.
     */
    public function classSubscriptionExpired(Request $request) {

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('classSubscriptionExpired-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('classSubscriptionExpired-print');

        //Vendor List
        $Vendors = DB::table('vendors')
                ->select('name', 'id')
                ->whereNull('deleted_at')
                ->get();

        $Subscribers = collect([]);
        $Packages = collect([]);
        $Member = collect([]);
        $str = '';
        $packageStr = '';
        $Count = 0;

        //Ajax request
        if (request()->ajax()) {

            if ($request->has('vendor_id') && $request->get('vendor_id') == 0) {
                $vendor_id = $request->get('vendor_id');
                $session_array['vendor_id'] = $vendor_id;
                Session::set('reportModule2SubscriberExpired', $session_array);
                Session::flash('reportModule2SubscriberExpired', Session::get('reportModule2SubscriberExpired'));
                $Subscribers = collect([]);
                $Packages = collect([]);
            } else {
                $vendor_id = $request->get('vendor_id');
                $session_array['vendor_id'] = $vendor_id;
                Session::set('reportModule2SubscriberExpired', $session_array);
                Session::flash('reportModule2SubscriberExpired', Session::get('reportModule2SubscriberExpired'));
                $this->packageTable = 'v' . $vendor_id . '_subscribers_package_details';

                //Get All Subscribers 
                $Subscribers = DB::table($this->packageTable . ' As spd')
                        ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                        ->select('spd.subscriber_id', 'registered_users.name')
                        ->groupby('spd.subscriber_id')
                        ->where('spd.active_status', 1)
                        ->where('spd.module_id', 2)
                        ->get();

                //Get All Packages 
                $Packages = DB::table('class_packages')
                        ->where(array('vendor_id' => $vendor_id))
                        ->select('id', 'name_en')
                        ->get();

                $str .= '<option value="0">--All--</option>';
                foreach ($Subscribers as $Subscriber) {
                    $str .= '<option value="' . $Subscriber->subscriber_id . '">' . $Subscriber->name . '</option>';
                }

                $packageStr .= '<option value=" ">--All--</option>';
                foreach ($Packages as $Package) {
                    $packageStr .= '<option value="' . $Package->id . '">' . $Package->name_en . '</option>';
                }

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
                if ($request->has('name_en') && $request->get('name_en') != '') {
                    $name_en = $request->get('name_en');
                    $session_array['name_en'] = $name_en;
                    Session::set('reportModule2SubscriberExpired', $session_array);
                    Session::flash('reportModule2SubscriberExpired', Session::get('reportModule2SubscriberExpired'));
                    $SubscribersList->where('spd.name_en', 'like', "$name_en%");
                }

                //if Request having Date Range
                if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
                    $start_date = $request->get('start_date');
                    $end_date = $request->get('end_date');
                    $session_array['start_date'] = $start_date;
                    $session_array['end_date'] = $end_date;
                    Session::set('reportModule2SubscriberExpired', $session_array);
                    Session::flash('reportModule2SubscriberExpired', Session::get('reportModule2SubscriberExpired'));
                    $SubscribersList->whereBetween('spd.start_date', [$start_date, $end_date]);
                }

                // if Request having Member id
                if ($request->has('id') && $request->get('id') != 0) {
                    $ID = $request->get('id');
                    $session_array['id'] = $ID;
                    Session::set('reportModule2SubscriberExpired', $session_array);
                    Session::flash('reportModule2SubscriberExpired', Session::get('reportModule2SubscriberExpired'));
                    $SubscribersList->where('spd.subscriber_id', $ID);
                }
                // if Request having Gender id
                if ($request->has('gender_id') && $request->get('gender_id') != 0) {
                    $GenderID = $request->get('gender_id');
                    $session_array['gender_id'] = $GenderID;
                    Session::set('reportModule2SubscriberExpired', $session_array);
                    Session::flash('reportModule2SubscriberExpired', Session::get('reportModule2SubscriberExpired'));
                    $SubscribersList->where('registered_users.gender_id', 'like', "$GenderID%");
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
                    Session::set('reportModule2SubscriberExpired', $session_array);
                    Session::flash('reportModule2SubscriberExpired', Session::get('reportModule2SubscriberExpired'));
                    $SubscribersList->whereBetween('spd.end_date', [$currentDate, $expiry]);
                }


                $Member = $SubscribersList->get();
                $Count = $Member->count();
            }

            return Datatables::of($Member)
                            ->with('str', $str)
                            ->with('packageStr', $packageStr)
                            ->with('count', $Count)
                            ->make();
        }

        //Get All Genders 
        $Genders = DB::table('gender_types')
                ->select('id', 'name_en')
                ->limit(2)
                ->get();

        return view('admin.vendorReports.classSubscriptionExpired')
                        ->with('Packages', $Packages)
                        ->with('Genders', $Genders)
                        ->with('Vendors', $Vendors)
                        ->with('Subscribers', $Subscribers)
                        ->with('Count', $Count)
                        ->with('PrintAccess', $this->PrintAccess);
    }

    /**
     * Display a listing of the Class Online Payments.
     */
    public function classOnlinePayment(Request $request) {

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('classOnlinePayments-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('classOnlinePayments-print');

        $this->table = 'registered_users';

        //Vendor List
        $Vendors = DB::table('vendors')
                ->select('name', 'id')
                ->whereNull('deleted_at')
                ->get();


        $Members = collect([]);
        $payments = collect([]);
        $Amount = new \stdClass();
        $Amount->fees = '0.000';
        $KnetAmount = new \stdClass();
        $KnetAmount->knet_amount = '0.000';
        $CCAmount = new \stdClass();
        $CCAmount->cc_amount = '0.000';
        $str = '';



        //Ajax request
        if (request()->ajax()) {

            if ($request->has('vendor_id') && $request->get('vendor_id') == 0) {
                $vendor_id = $request->get('vendor_id');
                $session_array['vendor_id'] = $vendor_id;
                Session::set('reportModule2onlinePayments', $session_array);
                Session::flash('reportModule2onlinePayments', Session::get('reportModule2onlinePayments'));
                $Members = collect([]);
                $payments = collect([]);
                $Amount = new \stdClass();
                $Amount->fees = '0.000';
                $KnetAmount = new \stdClass();
                $KnetAmount->knet_amount = '0.000';
                $CCAmount = new \stdClass();
                $CCAmount->cc_amount = '0.000';
            } else {

                $vendor_id = $request->get('vendor_id');
                $session_array['vendor_id'] = $vendor_id;
                Session::set('reportModule2onlinePayments', $session_array);
                Session::flash('reportModule2onlinePayments', Session::get('reportModule2onlinePayments'));
                $this->packageTable = 'v' . $vendor_id . '_subscribers_package_details';

                $Members = DB::table($this->table)
                        ->select('name', 'id')
                        ->where(array('status' => 1))
                        ->whereNull('deleted_at')
                        ->get();
                $str .= '<option value="0">--All--</option>';
                foreach ($Members as $Member) {
                    $str .= '<option value="' . $Member->id . '">' . $Member->name . '</option>';
                }

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
                if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
                    $start_date = $request->get('start_date');
                    $end_date = $request->get('end_date');
                    $session_array['start_date'] = $start_date;
                    $session_array['end_date'] = $end_date;
                    Session::set('reportModule2onlinePayments', $session_array);
                    Session::flash('reportModule2onlinePayments', Session::get('reportModule2onlinePayments'));
                    $paymentList->whereBetween('p.post_date', [$start_date, $end_date]);
                    $AmountList->whereBetween('p.post_date', [$start_date, $end_date]);
                    $KnetAmountList->whereBetween('p.post_date', [$start_date, $end_date]);
                    $CCAmountList->whereBetween('p.post_date', [$start_date, $end_date]);
                }

                // if Request having Member id
                if ($request->has('id') && $request->get('id') != 0) {
                    $ID = $request->get('id');
                    $session_array['id'] = $ID;
                    Session::set('reportModule2onlinePayments', $session_array);
                    Session::flash('reportModule2onlinePayments', Session::get('reportModule2onlinePayments'));
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

            return Datatables::of($payments)
                            ->editColumn('post_date', function ($payments) {
                                $newYear = new Carbon($payments->post_date);
                                return $newYear->format('d/m/Y');
                            })
                            ->with('Amount', $Amount)
                            ->with('KnetAmount', $KnetAmount)
                            ->with('CCAmount', $CCAmount)
                            ->with('str', $str)
                            ->make();
        }


        return view('admin.vendorReports.classOnlinePayments')
                        ->with('Members', $Members)
                        ->with('Vendors', $Vendors)
                        ->with('Amount', $Amount)
                        ->with('KnetAmount', $KnetAmount)
                        ->with('CCAmount', $CCAmount)
                        ->with('PrintAccess', $this->PrintAccess);
    }

    /**
     * Display a listing of the Class Booking.
     */
    public function classBookings(Request $request) {

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('classBookings-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('classBookings-print');

        //Vendor List
        $Vendors = DB::table('vendors')
                ->select('name', 'id')
                ->whereNull('deleted_at')
                ->get();

        $Subscribers = collect([]);
        $bookingHistory = collect([]);
        $Classes = collect([]);
        $Count = 0;
        $str = '';
        $classStr = '';

        //Ajax request
        if (request()->ajax()) {

            if ($request->has('vendor_id') && $request->get('vendor_id') == 0) {
                $vendor_id = $request->get('vendor_id');
                $session_array['vendor_id'] = $vendor_id;
                Session::set('reportClassBookings', $session_array);
                Session::flash('reportClassBookings', Session::get('reportClassBookings'));
                $Subscribers = collect([]);
                $Packages = collect([]);
            } else {
                $vendor_id = $request->get('vendor_id');
                $session_array['vendor_id'] = $vendor_id;
                Session::set('reportClassBookings', $session_array);
                Session::flash('reportClassBookings', Session::get('reportClassBookings'));
                $this->table = 'v' . $vendor_id . '_subscribers_package_details';
                $this->bookingTable = 'v' . $vendor_id . '_bookings';

                //Get All Subscribers 
                $Subscribers = DB::table($this->bookingTable . ' As b')
                        ->join('registered_users', 'b.subscriber_id', '=', 'registered_users.id')
                        ->select('registered_users.id', 'registered_users.name')
                        ->groupby('registered_users.id')
                        ->get();

                $str .= '<option value="">--All--</option>';
                foreach ($Subscribers as $Subscriber) {
                    $str .= '<option value="' . $Subscriber->id . '">' . $Subscriber->name . '</option>';
                }

                $Classes = DB::table($this->bookingTable . ' As b')
                        ->join('classes', 'classes.id', '=', 'b.class_id')
                        ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                        ->select('class_master.name_en', 'classes.class_master_id AS id')
                        ->groupby('b.class_master_id')
                        ->get();

                $classStr .= '<option value="0">--All--</option>';
                foreach ($Classes as $Class) {
                    $classStr .= '<option value="' . $Class->id . '">' . $Class->name_en . '</option>';
                }

                $bookingList = DB::table($this->bookingTable . ' As b')
                        ->join('classes', 'classes.id', '=', 'b.class_id')
                        ->join('class_schedules', 'class_schedules.id', '=', 'b.schedule_id')
                        ->join('registered_users', 'b.subscriber_id', '=', 'registered_users.id')
                        ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                        ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                        ->select('registered_users.name', DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS class_name'), 'class_schedules.start', 'class_schedules.end', 'class_schedules.schedule_date', 'b.created_at');


                //if Request having Date Range
                if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
                    $start_date = $request->get('start_date');
                    $end_date = $request->get('end_date');
                    $session_array['start_date'] = $start_date;
                    $session_array['end_date'] = $end_date;
                    Session::set('reportClassBookings', $session_array);
                    Session::flash('reportClassBookings', Session::get('reportClassBookings'));
                    $bookingList->whereBetween('class_schedules.schedule_date', [$start_date, $end_date]);
                }
                // if Request having Subscriber id
                if ($request->has('subscriber_id') && $request->get('subscriber_id') != 0) {
                    $ID = $request->get('subscriber_id');
                    $session_array['subscriber_id'] = $request->get('subscriber_id');
                    Session::set('reportClassBookings', $session_array);
                    Session::flash('reportClassBookings', Session::get('reportClassBookings'));
                    $bookingList->where('b.subscriber_id', $ID);
                }

                // if Request having Class id
                if ($request->has('class_id') && $request->get('class_id') != 0) {
                    $ID = $request->get('class_id');
                    $session_array['class_id'] = $ID;
                    Session::set('reportClassBookings', $session_array);
                    Session::flash('reportClassBookings', Session::get('reportClassBookings'));
                    $bookingList->where('b.class_master_id', $ID);
                }

                //if Request having Start Time And End Time        
                if ($request->has('start_time') && $request->get('start_time') != '' && $request->has('end_time') && $request->get('end_time') != '') {
                    $start_time = $request->get('start_time');
                    $end_time = $request->get('end_time');

                    $datetime = new DateTime();

                    $newDate = $datetime->createFromFormat('h:m:A', $start_time);
                    $start_time = $newDate->format('H:i:s');

                    $eDate = $datetime->createFromFormat('h:m:A', $end_time);
                    $end_time = $eDate->format('H:i:s');

                    //dd($start_time);

                    $session_array['start_time'] = $start_time;
                    $session_array['end_time'] = $end_time;
                    Session::set('reportClassBookings', $session_array);
                    Session::flash('reportClassBookings', Session::get('reportClassBookings'));
                    $bookingList->whereBetween('class_schedules.start', [$start_time, $end_time]);
                }


                $bookingHistory = $bookingList->get();
                $Count = $bookingHistory->count();
            }

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
                            ->with('Count', $Count)
                            ->with('str', $str)
                            ->with('classStr', $classStr)
                            ->make();
        }



        return view('admin.vendorReports.classBookings')
                        ->with('PrintAccess', $this->PrintAccess)
                        ->with('Classes', $Classes)
                        ->with('Count', $Count)
                        ->with('Vendors', $Vendors)
                        ->with('Subscribers', $Subscribers);
    }

    /**
     * Display a listing of the Fitflow Membership Subscription.
     */
    public function fitflowMembershipSubscriptions(Request $request) {

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('fitflowMembershipSubscriptions-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('fitflowMembershipSubscriptions-print');

        $Count = 0;

        $this->packageTable = 'subscribers_package_details';

        //Get All Subscribers 
        $Subscribers = DB::table($this->packageTable . ' As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->select('spd.subscriber_id', 'registered_users.name')
                ->groupby('spd.subscriber_id')
                ->where('spd.active_status', 1)
                ->where('spd.module_id', 3)
                ->get();

        //Get All Packages 
        $Packages = DB::table('packages')
                ->select('id', 'name_en')
                ->get();

        //Get All Genders 
        $Genders = DB::table('gender_types')
                ->select('id', 'name_en')
                ->limit(2)
                ->get();

        $SubscribersList = DB::table($this->packageTable . ' As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->leftjoin('areas', 'areas.id', '=', 'registered_users.area_id')
                ->leftjoin('gender_types', 'gender_types.id', '=', 'registered_users.gender_id')
                ->select('registered_users.name', 'registered_users.email', 'registered_users.mobile', 'gender_types.name_en As gender'
                        , 'spd.name_en', DB::raw('(CASE WHEN spd.num_points = 0 THEN "Unlimited" ELSE spd.num_points  END) AS num_points'), 'spd.num_booked', 'spd.start_date'
                        , DB::raw('CONCAT(DATE_FORMAT(spd.start_date,"%d/%m/%Y"), "- ", DATE_FORMAT(spd.end_date,"%d/%m/%Y")) AS period'), 'spd.end_date', 'spd.subscriber_id')
                ->where('spd.active_status', 1)
                ->where('spd.module_id', 3);


        // if Request having Pacakge name
        if ($request->has('name_en') && $request->get('name_en') != '') {
            $name_en = $request->get('name_en');
            $session_array['name_en'] = $name_en;
            Session::set('reportModule3Subscriber', $session_array);
            Session::flash('reportModule3Subscriber', Session::get('reportModule3Subscriber'));
            $SubscribersList->where('spd.name_en', 'like', "$name_en%");
        }

        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $session_array['start_date'] = $start_date;
            $session_array['end_date'] = $end_date;
            Session::set('reportModule3Subscriber', $session_array);
            Session::flash('reportModule3Subscriber', Session::get('reportModule3Subscriber'));
            $SubscribersList->whereBetween('spd.start_date', [$start_date, $end_date]);
        }

        // if Request having Member id
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            $session_array['id'] = $ID;
            Session::set('reportModule3Subscriber', $session_array);
            Session::flash('reportModule3Subscriber', Session::get('reportModule3Subscriber'));
            $SubscribersList->where('spd.subscriber_id', $ID);
        }
        // if Request having Gender id
        if ($request->has('gender_id') && $request->get('gender_id') != 0) {
            $GenderID = $request->get('gender_id');
            $session_array['gender_id'] = $GenderID;
            Session::set('reportModule3Subscriber', $session_array);
            Session::flash('reportModule3Subscriber', Session::get('reportModule3Subscriber'));
            $SubscribersList->where('registered_users.gender_id', 'like', "$GenderID%");
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
            Session::set('reportModule3Subscriber', $session_array);
            Session::flash('reportModule3Subscriber', Session::get('reportModule2Subscriber'));
            $SubscribersList->whereBetween('spd.end_date', [$currentDate, $expiry]);
        }


        $Member = $SubscribersList->get();
        $Count = $Member->count();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Member)
                            ->with('count', $Count)
                            ->make();
        }


        return view('admin.vendorReports.fitflowMembershipSubscriptions')
                        ->with('Packages', $Packages)
                        ->with('Genders', $Genders)
                        ->with('Subscribers', $Subscribers)
                        ->with('Count', $Count)
                        ->with('PrintAccess', $this->PrintAccess);
    }

    /**
     * Display a listing of the Fitflow Subscription Expired.
     */
    public function fitflowMembershipSubscriptionExpired(Request $request) {

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('fitflowMembershipSubscriptionExpired-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('fitflowMembershipSubscriptionExpired-print');

        $Count = 0;

        $this->packageTable = 'subscribers_package_details';

        //Get All Subscribers 
        $Subscribers = DB::table($this->packageTable . ' As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->select('spd.subscriber_id', 'registered_users.name')
                ->groupby('spd.subscriber_id')
                ->where('spd.active_status', 1)
                ->where('spd.module_id', 3)
                ->get();

        //Get All Packages 
        $Packages = DB::table('packages')
                ->select('id', 'name_en')
                ->get();

        //Get All Genders 
        $Genders = DB::table('gender_types')
                ->select('id', 'name_en')
                ->limit(2)
                ->get();


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


        // if Request having Pacakge name
        if ($request->has('name_en') && $request->get('name_en') != '') {
            $name_en = $request->get('name_en');
            $session_array['name_en'] = $name_en;
            Session::set('reportModule3SubscriberExpired', $session_array);
            Session::flash('reportModule3SubscriberExpired', Session::get('reportModule3SubscriberExpired'));
            $SubscribersList->where('spd.name_en', 'like', "$name_en%");
        }

        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $session_array['start_date'] = $start_date;
            $session_array['end_date'] = $end_date;
            Session::set('reportModule3SubscriberExpired', $session_array);
            Session::flash('reportModule3SubscriberExpired', Session::get('reportModule3SubscriberExpired'));
            $SubscribersList->whereBetween('spd.start_date', [$start_date, $end_date]);
        }

        // if Request having Member id
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            $session_array['id'] = $ID;
            Session::set('reportModule3SubscriberExpired', $session_array);
            Session::flash('reportModule3SubscriberExpired', Session::get('reportModule3SubscriberExpired'));
            $SubscribersList->where('spd.subscriber_id', $ID);
        }
        // if Request having Gender id
        if ($request->has('gender_id') && $request->get('gender_id') != 0) {
            $GenderID = $request->get('gender_id');
            $session_array['gender_id'] = $GenderID;
            Session::set('reportModule3SubscriberExpired', $session_array);
            Session::flash('reportModule3SubscriberExpired', Session::get('reportModule3SubscriberExpired'));
            $SubscribersList->where('registered_users.gender_id', 'like', "$GenderID%");
        }


        $Member = $SubscribersList->get();
        $Count = $Member->count();

        //Ajax request
        if (request()->ajax()) {
            return Datatables::of($Member)
                            ->with('count', $Count)
                            ->make();
        }


        return view('admin.vendorReports.fitflowMembershipSubscriptionExpired')
                        ->with('Packages', $Packages)
                        ->with('Genders', $Genders)
                        ->with('Subscribers', $Subscribers)
                        ->with('Count', $Count)
                        ->with('PrintAccess', $this->PrintAccess);
    }

    /**
     * Display a listing of the fitflowMembership Online Payments.
     */
    public function fitflowMembershipOnlinePayment(Request $request) {

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('fitflowMembershipOnlinePayments-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('fitflowMembershipOnlinePayments-print');

        $this->table = 'registered_users';
        $this->packageTable = 'subscribers_package_details';


        $Members = collect([]);
        $payments = collect([]);
        $Amount = new \stdClass();
        $Amount->fees = '0.000';
        $KnetAmount = new \stdClass();
        $KnetAmount->knet_amount = '0.000';
        $CCAmount = new \stdClass();
        $CCAmount->cc_amount = '0.000';


        $MemberList = DB::table($this->table)
                ->select('name', 'id')
                ->where(array('status' => 1))
                ->whereNull('deleted_at')
                ->get();


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

        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $session_array['start_date'] = $start_date;
            $session_array['end_date'] = $end_date;
            Session::set('reportModule3onlinePayments', $session_array);
            Session::flash('reportModule3onlinePayments', Session::get('reportModule3onlinePayments'));
            $paymentList->whereBetween('p.post_date', [$start_date, $end_date]);
            $AmountList->whereBetween('p.post_date', [$start_date, $end_date]);
            $KnetAmountList->whereBetween('p.post_date', [$start_date, $end_date]);
            $CCAmountList->whereBetween('p.post_date', [$start_date, $end_date]);
        }

        // if Request having Member id
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            $session_array['id'] = $ID;
            Session::set('reportModule3onlinePayments', $session_array);
            Session::flash('reportModule3onlinePayments', Session::get('reportModule3onlinePayments'));
            $paymentList->where('sp.subscriber_id', $ID);
            $AmountList->where('sp.subscriber_id', $ID);
            $KnetAmountList->where('sp.subscriber_id', $ID);
            $CCAmountList->where('sp.subscriber_id', $ID);
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


        return view('admin.vendorReports.fitflowMembershipOnlinePayments')
                        ->with('Members', $Members)
                        ->with('Amount', $Amount)
                        ->with('KnetAmount', $KnetAmount)
                        ->with('CCAmount', $CCAmount)
                        ->with('PrintAccess', $this->PrintAccess);
    }

    /**
     * Display a listing of the Class Subscription Expired.
     */
    public function fitflowMembershipBookings(Request $request) {

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('fitflowMembershipBookings-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('fitflowMembershipBookings-print');

        $this->table = 'subscribers_package_details';
        $this->bookingTable = 'bookings';

        $Count = 0;

        //Get All Subscribers 
        $SubscriberList = DB::table($this->bookingTable . ' As b')
                ->join('registered_users', 'b.subscriber_id', '=', 'registered_users.id')
                ->select('registered_users.id', 'registered_users.name')
                ->groupby('registered_users.id');


        $ClassList = DB::table($this->bookingTable . ' As b')
                ->join('classes', 'classes.id', '=', 'b.class_id')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->select('class_master.name_en', 'classes.class_master_id AS id')
                ->groupby('b.class_master_id');

        //Vendor List
        $Vendors = DB::table('vendors')
                ->select('name', 'id')
                ->whereNull('deleted_at')
                ->get();

        $str = '';
        $classStr = '';

        $bookingList = DB::table($this->bookingTable . ' As b')
                ->join('classes', 'classes.id', '=', 'b.class_id')
                ->join('class_schedules', 'class_schedules.id', '=', 'b.schedule_id')
                ->join('registered_users', 'b.subscriber_id', '=', 'registered_users.id')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->join('vendors', 'vendors.id', '=', 'b.vendor_id')
                ->select('registered_users.name', 'vendors.name AS vendor_name', DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS class_name'), 'class_schedules.start', 'class_schedules.end', 'class_schedules.schedule_date', 'b.created_at');




        if ($request->has('vendor_id') && $request->get('vendor_id') != 0) {
            $vendor_id = $request->get('vendor_id');
            $session_array['vendor_id'] = $vendor_id;
            Session::set('reportM3Bookings', $session_array);
            Session::flash('reportM3Bookings', Session::get('reportM3Bookings'));
            $bookingList->where('b.vendor_id', $vendor_id);
            $SubscriberList->where('b.vendor_id', $vendor_id);
            $ClassList->where('b.vendor_id', $vendor_id);
        }


        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $session_array['start_date'] = $start_date;
            $session_array['end_date'] = $end_date;
            Session::set('reportM3Bookings', $session_array);
            Session::flash('reportM3Bookings', Session::get('reportM3Bookings'));
            $bookingList->whereBetween('class_schedules.schedule_date', [$start_date, $end_date]);
        }
        // if Request having Subscriber id
        if ($request->has('subscriber_id') && $request->get('subscriber_id') != 0) {
            $ID = $request->get('subscriber_id');
            $session_array['subscriber_id'] = $request->get('subscriber_id');
            Session::set('reportM3Bookings', $session_array);
            Session::flash('reportM3Bookings', Session::get('reportM3Bookings'));
            $bookingList->where('b.subscriber_id', $ID);
        }

        // if Request having Class id
        if ($request->has('class_id') && $request->get('class_id') != 0) {
            $ID = $request->get('class_id');
            $session_array['class_id'] = $ID;
            Session::set('reportM3Bookings', $session_array);
            Session::flash('reportM3Bookings', Session::get('reportM3Bookings'));
            $bookingList->where('b.class_master_id', $ID);
        }

        //if Request having Start Time And End Time        
        if ($request->has('start_time') && $request->get('start_time') != '' && $request->has('end_time') && $request->get('end_time') != '') {
            $start_time = $request->get('start_time');
            $end_time = $request->get('end_time');

            $datetime = new DateTime();

            $newDate = $datetime->createFromFormat('h:m:A', $start_time);
            $start_time = $newDate->format('H:i:s');

            $eDate = $datetime->createFromFormat('h:m:A', $end_time);
            $end_time = $eDate->format('H:i:s');

            //dd($start_time);

            $session_array['start_time'] = $start_time;
            $session_array['end_time'] = $end_time;
            Session::set('reportM3Bookings', $session_array);
            Session::flash('reportM3Bookings', Session::get('reportM3Bookings'));
            $bookingList->whereBetween('class_schedules.start', [$start_time, $end_time]);
        }


        $bookingHistory = $bookingList->get();
        $Subscribers = $SubscriberList->get();
        $Classes = $ClassList->get();
        $Count = $bookingHistory->count();

        //Subscriber List
        $str .= '<option value="">--All--</option>';
        foreach ($Subscribers as $Subscriber) {
            $str .= '<option value="' . $Subscriber->id . '">' . $Subscriber->name . '</option>';
        }

        //Classes List
        $classStr .= '<option value="0">--All--</option>';
        foreach ($Classes as $Class) {
            $classStr .= '<option value="' . $Class->id . '">' . $Class->name_en . '</option>';
        }

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
                            ->with('Count', $Count)
                            ->with('str', $str)
                            ->with('classStr', $classStr)
                            ->make();
        }



        return view('admin.vendorReports.fitflowMembershipBookings')
                        ->with('PrintAccess', $this->PrintAccess)
                        ->with('Classes', $Classes)
                        ->with('Vendors', $Vendors)
                        ->with('Count', $Count)
                        ->with('Subscribers', $Subscribers);
    }

    //Instructor Subscription
    public function instructorSubscriptions(Request $request) {

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('reportInstructorSubscriptions-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('reportInstructorSubscriptions-print');

        $this->instructorSubscriptionTable = 'instructor_subscribers_package_details';

        //Vendor List
        $Vendors = DB::table('vendors')
                ->select('name', 'id')
                ->whereNull('deleted_at')
                ->get();



        $Members = collect([]);
        $Packages = collect([]);
        $InstructorSubscription = collect([]);
        $str = '';
        $packageStr = '';
        
       

        //Ajax request
        if (request()->ajax()) {

            if ($request->has('vendor_id') && $request->get('vendor_id') == 0) {
                $vendor_id = $request->get('vendor_id');
                $session_array['vendor_id'] = $vendor_id;
                Session::set('reportInstructorSubscription', $session_array);
                Session::flash('reportInstructorSubscription', Session::get('reportInstructorSubscription'));
                $Members = collect([]);
                $Packages = collect([]);
                $InstructorSubscription = collect([]);
            } else {
                $vendor_id = $request->get('vendor_id');
                $session_array['vendor_id'] = $vendor_id;
                Session::set('reportInstructorSubscription', $session_array);
                Session::flash('reportInstructorSubscription', Session::get('reportInstructorSubscription'));
                
                $this->table = 'v' . $vendor_id . '_members';

                $Members = DB::table($this->instructorSubscriptionTable . ' As ins')
                         ->join($this->table.' As registered_users', 'ins.member_id', '=', 'registered_users.id')
                        ->select('registered_users.name', 'registered_users.id')
                        ->where('ins.vendor_id', $vendor_id)
                        ->where(array('registered_users.status' => 1))
                        ->whereNull('registered_users.deleted_at')
                        ->groupby('ins.member_id')
                        ->get();

                //Get All Packages 
                $Packages = DB::table('instructor_packages')
                        ->select('id', 'name_en')
                        ->where('vendor_id', $vendor_id)
                        ->get();

                $str .= '<option value="0">--All--</option>';
                foreach ($Members as $Member) {
                    $str .= '<option value="' . $Member->id . '">' . $Member->name . '</option>';
                }

                $packageStr .= '<option value=" ">--All--</option>';
                foreach ($Packages as $Package) {
                    $packageStr .= '<option value="' . $Package->name_en . '">' . $Package->name_en . '</option>';
                }

                $InstructorSubscriptionList = DB::table($this->instructorSubscriptionTable . ' As ins')
                         ->join($this->table.' As registered_users', 'ins.member_id', '=', 'registered_users.id')
                        ->select('ins.id AS subscription_package_id', 'registered_users.name AS subscriber', 'registered_users.mobile', 'ins.name_en As package_name', 'ins.price', 'ins.num_points', 'ins.num_booked', 'ins.created_at')
                        ->where('ins.vendor_id', $vendor_id);

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
            }
            
           

            return Datatables::of($InstructorSubscription)
                            ->editColumn('created_at', function ($InstructorSubscription) {
                                $newYear = new Carbon($InstructorSubscription->created_at);
                                return $newYear->format('d/m/Y');
                            })   
                            ->with('packageStr', $packageStr)
                            ->with('str', $str)
                            ->make();
        }

     

        return view('admin.vendorReports.instructorSubscriptions')
                        ->with('Packages', $Packages)
                        ->with('Vendors', $Vendors)
                        ->with('Members', $Members)
                        ->with('PrintAccess', $this->PrintAccess);
    }

}
