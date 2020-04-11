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
use App\Helpers\VendorDetail;

class ReportController extends Controller {

    protected $guard = 'vendor';
    protected $configName;
    protected $ViewAccess;
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
        $this->ViewAccess = Permit::AccessPermission('classBookings-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('classBookings-print');

        $this->table = VendorDetail::getPrefix() . 'subscribers_package_details';
        $this->bookingTable = VendorDetail::getPrefix() . 'bookings';

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
            Session::set('reportClassBookings', ['subscriber_id' => $ID]);
            Session::flash('reportClassBookings', Session::get('reportClassBookings'));
            $bookingList->where('b.subscriber_id', $ID);
        }

        // if Request having Class id
        if ($request->has('class_id') && $request->get('class_id') != 0) {
            $ID = $request->get('class_id');
            Session::set('reportClassBookings', ['class_id' => $ID]);
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
        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($bookingHistory)
                            ->editColumn('start', function ($bookingHistory) {
                                $newYear = new Carbon($bookingHistory->start);
                                return $newYear->format('h:i:A');
                            })
                            ->editColumn('end', function ($bookingHistory) {
                                $newYear = new Carbon($bookingHistory->end);
                                return $newYear->format('h:i:A');
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
                            ->make();
        }

        $Classes = DB::table($this->bookingTable . ' As b')
                ->join('classes', 'classes.id', '=', 'b.class_id')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->select('class_master.name_en', 'classes.class_master_id AS id')
                ->groupby('b.class_master_id')
                ->get();

        $Subscribers = DB::table($this->bookingTable . ' As b')
                ->join('registered_users', 'b.subscriber_id', '=', 'registered_users.id')
                ->select('registered_users.id', 'registered_users.name')
                ->groupby('registered_users.id')
                ->get();

        return view('fitflowVendor.module2.reports.bookings')
                        ->with('PrintAccess', $this->PrintAccess)
                        ->with('Classes', $Classes)
                        ->with('Count', $Count)
                        ->with('Subscribers', $Subscribers);
    }

    /**
     * Display a listing of the Online Payments.
     */
    public function onlinePayment(Request $request) {

        $this->table = 'registered_users';
        $this->packageTable = VendorDetail::getPrefix() . 'subscribers_package_details';


        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('module2OnlinePayments-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('module2OnlinePayments-print');

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
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            Session::set('reportModule2onlinePayments', ['start_date' => $start_date, 'end_date' => $end_date]);
            Session::flash('reportModule2onlinePayments', Session::get('reportModule2onlinePayments'));
            $paymentList->whereBetween('p.post_date', [$start_date, $end_date]);
            $AmountList->whereBetween('p.post_date', [$start_date, $end_date]);
            $KnetAmountList->whereBetween('p.post_date', [$start_date, $end_date]);
            $CCAmountList->whereBetween('p.post_date', [$start_date, $end_date]);
        }

        // if Request having Member id
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            Session::set('reportModule2onlinePayments', ['id' => $ID]);
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

        $Subscribers = DB::table($this->packageTable . ' As b')
                ->join('registered_users', 'b.subscriber_id', '=', 'registered_users.id')
                ->select('registered_users.id', 'registered_users.name')
                ->groupby('registered_users.id')
                ->get();

        return view('fitflowVendor.module2.reports.onlinePayments')
                        ->with('Subscribers', $Subscribers)
                        ->with('Amount', $Amount)
                        ->with('KnetAmount', $KnetAmount)
                        ->with('CCAmount', $CCAmount)
                        ->with('PrintAccess', $this->PrintAccess);
    }

    /**
     * Display a listing of the Subscription Expired.
     */
    public function subscriptionExpired(Request $request) {

        $this->packageTable = VendorDetail::getPrefix() . 'subscribers_package_details';

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('module2SubscriptionExpired-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('module2SubscriptionExpired-print');

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
            Session::set('reportModule2SubscriberExpired', ['name_en' => $name_en]);
            Session::flash('reportModule2SubscriberExpired', Session::get('reportModule2SubscriberExpired'));
            $SubscribersList->where('spd.name_en', 'like', "$name_en%");
        }

        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            Session::set('reportModule2SubscriberExpired', ['start_date' => $start_date, 'end_date' => $end_date]);
            Session::flash('reportModule2SubscriberExpired', Session::get('reportModule2SubscriberExpired'));
            $SubscribersList->whereBetween('spd.end_date', [$start_date, $end_date]);
        }

        // if Request having  Member id
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            Session::set('reportModule2SubscriberExpired', ['id' => $ID]);
            Session::flash('reportModule2SubscriberExpired', Session::get('reportModule2SubscriberExpired'));
            $SubscribersList->where('spd.subscriber_id', $ID);
        }

        // if Request having Gender id
        if ($request->has('gender_id') && $request->get('gender_id') != 0) {
            $GenderID = $request->get('gender_id');
            Session::set('reportModule2SubscriberExpired', ['gender_id' => $GenderID]);
            Session::flash('reportModule2SubscriberExpired', Session::get('reportModule2SubscriberExpired'));
            $SubscribersList->where('registered_users.gender_id', 'like', "$GenderID%");
        }

        $Member = $SubscribersList->get();
        $Count = $Member->count();

        //Get All Packages 
        $Packages = DB::table('class_packages')
                ->select('id', 'name_en')
                ->where(array('vendor_id' => VendorDetail::getID()))
                ->get();

        //Get All Genders 
        $Genders = DB::table('gender_types')
                ->select('id', 'name_en')
                ->limit(2)
                ->get();


        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Member)
                            ->editColumn('action', function ($Member) {
                                return ' <a href="' . url($this->configName) . '/subscribers/' . $Member->subscriber_id . '/packageHistory" class="btn btn-info tooltip-primary btn-small package_history" data-toggle="tooltip"  data-original-title="Package History" title="Package History"><i class="entypo-bag"></i></a>'
                                        . ' <a href="' . url($this->configName) . '/subscribers/' . $Member->subscriber_id . '/bookingHistory" class="btn btn-danger tooltip-primary btn-small booking_history" data-toggle="tooltip"  data-original-title="Booking History" title="Booking History"><i class="entypo-book"></i></a>';
                            })
                            ->with('count', $Count)
                            ->make();
        }


        //Get All Subscribers 
        $Subscribers = DB::table($this->packageTable . ' As spd')
                        ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                        ->select('spd.subscriber_id', 'registered_users.name', DB::raw('(CASE WHEN spd.num_points = 0 THEN "Unlimited" ELSE spd.num_points  END) AS num_points'), 'spd.num_booked')
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
                        ->groupby('spd.subscriber_id')->get();

        return view('fitflowVendor.module2.reports.subscriptionExpired')
                        ->with('Packages', $Packages)
                        ->with('Genders', $Genders)
                        ->with('Subscribers', $Subscribers)
                        ->with('Count', $Count)
                        ->with('PrintAccess', $this->PrintAccess);
    }

    /**
     * Display a listing of the Subscription Expired.
     */
    public function subscriptions(Request $request) {

        $this->packageTable = VendorDetail::getPrefix() . 'subscribers_package_details';

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('module2Subscriptions-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('module2Subscriptions-print');

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
            Session::set('reportModule2Subscriber', ['name_en' => $name_en]);
            Session::flash('reportModule2Subscriber', Session::get('reportModule2Subscriber'));
            $SubscribersList->where('spd.name_en', 'like', "$name_en%");
        }

        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            Session::set('reportModule2Subscriber', ['start_date' => $start_date, 'end_date' => $end_date]);
            Session::flash('reportModule2Subscriber', Session::get('reportModule2Subscriber'));
            $SubscribersList->whereBetween('spd.start_date', [$start_date, $end_date]);
        }

        // if Request having Member id
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            Session::set('reportModule2Subscriber', ['id' => $ID]);
            Session::flash('reportModule2Subscriber', Session::get('reportModule2Subscriber'));
            $SubscribersList->where('spd.subscriber_id', $ID);
        }

        // if Request having Gender id
        if ($request->has('gender_id') && $request->get('gender_id') != 0) {
            $GenderID = $request->get('gender_id');
            Session::set('reportModule2Subscriber', ['gender_id' => $GenderID]);
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


        //Get All Packages 
        $Packages = DB::table('class_packages')
                ->select('id', 'name_en')
                ->where(array('vendor_id' => VendorDetail::getID()))
                ->get();

        //Get All Genders 
        $Genders = DB::table('gender_types')
                ->select('id', 'name_en')
                ->limit(2)
                ->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Member)
                            ->editColumn('action', function ($Member) {
                                return ' <a href="' . url($this->configName) . '/subscribers/' . $Member->subscriber_id . '/packageHistory" class="btn btn-info tooltip-primary btn-small package_history" data-toggle="tooltip"  data-original-title="Package History" title="Package History"><i class="entypo-bag"></i></a>'
                                        . ' <a href="' . url($this->configName) . '/subscribers/' . $Member->subscriber_id . '/bookingHistory" class="btn btn-danger tooltip-primary btn-small booking_history" data-toggle="tooltip"  data-original-title="Booking History" title="Booking History"><i class="entypo-book"></i></a>';
                            })
                            ->with('count', $Count)
                            ->make();
        }


        //Get All Subscribers 
        $Subscribers = DB::table($this->packageTable . ' As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->select('spd.subscriber_id', 'registered_users.name', DB::raw('(CASE WHEN spd.num_points = 0 THEN "Unlimited" ELSE spd.num_points  END) AS num_points'), 'spd.num_booked')
                ->groupby('spd.subscriber_id')
                ->where('spd.active_status', 1)
                ->where('spd.module_id', 2)
                ->get();

        return view('fitflowVendor.module2.reports.subscriptions')
                        ->with('Packages', $Packages)
                        ->with('Genders', $Genders)
                        ->with('Subscribers', $Subscribers)
                        ->with('Count', $Count)
                        ->with('PrintAccess', $this->PrintAccess);
    }

}
