<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Input;
use Carbon\Carbon;
use DateTime;
use App\Models\Vendor\Member;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Permit;
use App\Helpers\LogActivity;
use App\Helpers\VendorDetail;
use App\Mail\InvoiceEmail;
use Mail;

class MemberController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:vendors');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $vendor_id = $request->vendor_id;

        $this->table = 'v' . $vendor_id . '_members';
        $this->packagetable = 'v' . $vendor_id . '_subscribers_package_details';

        //Get Sale Count Start Date
        $sale_setting = DB::table('vendors')
                ->select('sale_setting')
                ->where('id', $vendor_id)
                ->first();


        $Member = DB::table($this->table . ' As m')
                ->leftjoin('gender_types As g', 'g.id', '=', 'm.gender_id')
                ->select('m.id', 'm.name', 'm.email', 'm.mobile', 'g.name_en AS gender_name', 'm.package_name', 'm.start_date', 'm.end_date', 'm.subscribed_from')
                ->whereNull('m.deleted_at')
                ->whereDate('m.start_date', '>=', $sale_setting->sale_setting)
                ->groupby('m.id');

        //->havingRaw('MAX(spd.end_date) >= NOW()');
        // if Request having Pacakge name
        if ($request->has('name_en') && $request->get('name_en') != '') {
            $name_en = $request->get('name_en');
            $Member->where('m.package_name', 'like', "$name_en%");
        }

        // Subscription New:0, Renew:1
        if ($request->has('subscription') && $request->get('subscription') != '') {
            $subscription = $request->get('subscription');
            $Member->where('m.subscription', $subscription);
        }
        // if Member Status //1Week:0, 2Week:1, #Week:2
        if ($request->has('member_status') && $request->get('member_status') != '') {
            $member_status = $request->get('member_status');
            $current = Carbon::now();
            $expiry = $current->addWeek($member_status);
            $expiry = $expiry->format('Y-m-d');
            $currentDate = Carbon::now()->format('Y-m-d');

            $Member->whereBetween('m.end_date', [$currentDate, $expiry]);
        }
        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $Member->whereBetween('m.end_date', [$start_date, $end_date]);
        }

        $Members = $Member->get();

        //Get All Packages 
        $Packages = DB::table('vendor_packages')
                ->select('id', 'name_en')
                ->get();



        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Members)
                            ->editColumn('end_date', function ($Members) {
                                $newYear = new Carbon($Members->start_date);
                                $endYear = new Carbon($Members->end_date);
                                return $newYear->format('d/m/Y') . ' - ' . $endYear->format('d/m/Y');
                            })
                            ->editColumn('id', function ($Members) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $Members->id . '">';
                            })
                            ->editColumn('action', function ($Members) use($vendor_id) {
                                return ' <a href="' . url('admin/members') . '/' . $vendor_id . '/' . $Members->id . '/packageHistory" class="btn btn-orange tooltip-primary btn-small package_history" data-toggle="tooltip"  data-original-title="Package History" title="Package History"><i class="entypo-bag"></i></a>';
                            })
                            ->make();
        }

        return view('admin.members.index')
                        ->with('vendor_id', $vendor_id)
                        ->with('Packages', $Packages);
    }

    public function packageHistory(Request $request, $id) {

        $id = $request->id;
        $vendor_id = $request->vendor_id;

        $this->table = 'v' . $vendor_id . '_members';
        $this->packagetable = 'v' . $vendor_id . '_subscribers_package_details';

        //Get Subscriber name
        $username = DB::table($this->table)
                ->select('name')
                ->where('id', $id)
                ->first();

        //Sum Amount
        $Amount = DB::table($this->packagetable . ' As spd')
                ->where(array('spd.member_id' => $id))
                ->sum('spd.price');

        //Get all Archived Subscriber History
        $packageHistory = DB::table($this->packagetable . ' As spd')
                ->select('spd.name_en', 'spd.price', 'spd.start_date', 'spd.end_date', 'spd.num_days', 'spd.payment_id As payment_method', 'spd.cash', 'spd.knet', 'spd.payment_id')
                ->where(array('spd.member_id' => $id, 'spd.module_id' => 1))
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
                            ->editColumn('payment_method', function ($packageHistory) {
                                $str = '';
                                if ($packageHistory->payment_method == 0) {
                                    if (!is_null($packageHistory->cash) && $packageHistory->cash != 0) {
                                        $str .= 'Cash-' . $packageHistory->cash;
                                    }
                                    if (!is_null($packageHistory->knet) && $packageHistory->knet != 0) {
                                        $str .= ' KNET-' . $packageHistory->knet;
                                    }
                                    return $str;
                                } else {
                                    return ' ';
                                }
                            })
                            ->editColumn('action', function ($packageHistory) {
                                if ($packageHistory->payment_id != null)
                                    return '<a  class="btn btn-green tooltip-primary btn-small package_details" data-toggle="modal"  data-original-title="Package Details" title="Package Details"  href="#myModal" data-val="' . $packageHistory->payment_id . '"><i class="fa fa-money"></i></a>';
                            })
                            ->make();
        }

        return view('admin.members.packageHistory')
                        ->with('id', $id)
                        ->with('vendor_id', $vendor_id)
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

        $returnHTML = view('admin.members.packagePayment')->with('payment', $payment)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

}
