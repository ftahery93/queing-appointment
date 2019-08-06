<?php

namespace App\Http\Controllers\Trainer;

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
use Illuminate\Support\Facades\Auth;
use App\Helpers\TrainerDetail;

class ReportController extends Controller {

    protected $guard = 'trainer';

    public function __construct() {
        $this->middleware($this->guard);
    }

    /**
     * Display a listing of the Online Payments.
     */
    public function payment(Request $request) {

        $this->table = 'registered_users';
        $this->packageTable = 'trainer_subscribers_package_details';

        //Get package payment details
        $paymentList = DB::table('payment_details As p')
                ->join($this->packageTable . ' As sp', 'sp.payment_id', '=', 'p.id')
                ->leftjoin($this->table . ' As m', 'sp.subscriber_id', '=', 'm.id')
                ->leftjoin('trainer_packages As tp', 'tp.id', '=', 'p.package_id')
                ->select('m.name', 'tp.name_en AS package_name', 'p.reference_id', 'p.amount', 'p.post_date', DB::raw('(CASE WHEN p.card_type = 1 THEN "KNET" ELSE "Credit Card" END) AS payment_method'))
                ->where('sp.trainer_id', Auth::guard('trainer')->user()->id);

        $KnetAmountList = DB::table('payment_details As p')
                ->join($this->packageTable . ' As sp', 'sp.payment_id', '=', 'p.id')
                ->join('knet_payments AS k', 'k.id', '=', 'p.payid')
                ->select(DB::raw('COALESCE(SUM(k.amount),0) as knet_amount'))
                ->where('p.card_type', 1)->where('sp.trainer_id', Auth::guard('trainer')->user()->id);

        $CCAmountList = DB::table('payment_details As p')
                ->join($this->packageTable . ' As sp', 'sp.payment_id', '=', 'p.id')
                ->join('cc_payments AS c', 'c.id', '=', 'p.payid')
                ->select(DB::raw('COALESCE(SUM(c.amount),0) as cc_amount'))
                ->where('p.card_type', 2)->where('sp.trainer_id', Auth::guard('trainer')->user()->id);

        $AmountList = DB::table('payment_details As p')
                ->join($this->packageTable . ' As sp', 'sp.payment_id', '=', 'p.id')
                ->select(DB::raw('COALESCE(SUM(sp.price),0) as fees'))->where('sp.trainer_id', Auth::guard('trainer')->user()->id);

        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $session_array['start_date'] = $start_date;
            $session_array['end_date'] = $end_date;
            Session::set('reportPayments', $session_array);
            Session::flash('reportPayments', Session::get('reportPayments'));
            $paymentList->whereBetween('p.post_date', [$start_date, $end_date]);
            $AmountList->whereBetween('p.post_date', [$start_date, $end_date]);
            $KnetAmountList->whereBetween('p.post_date', [$start_date, $end_date]);
            $CCAmountList->whereBetween('p.post_date', [$start_date, $end_date]);
        }

        // if Request having Member id
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            $session_array['id'] = $ID;
            Session::set('reportPayments', $session_array);
            Session::flash('reportPayments', Session::get('reportPayments'));
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

        $Members = DB::table($this->table . ' As m')
                ->join($this->packageTable . ' As sp', 'sp.subscriber_id', '=', 'm.id')
                ->select('m.name', 'm.id')
                ->where(array('m.status' => 1, 'sp.trainer_id' => Auth::guard('trainer')->user()->id))
                ->groupby('sp.subscriber_id')
                ->get();

        return view('trainer.reports.payments')
                        ->with('Members', $Members)
                        ->with('Amount', $Amount)
                        ->with('KnetAmount', $KnetAmount)
                        ->with('CCAmount', $CCAmount);
    }

    /**
     * Display a listing of the Subscription Expired.
     */
    public function subscriptionExpired(Request $request) {
        //  TrainerDetail::setSubscriberPackageStatus();
        $this->table = 'registered_users';
        $this->packageTable = 'trainer_subscribers_package_details';

        $SubscribersList = DB::table('trainer_subscribers_package_details As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->leftjoin('subscriber_attend_trainers As sat ', 'sat.subscribed_package_id', '=', 'spd.id')
                ->leftjoin('gender_types', 'gender_types.id', '=', 'registered_users.gender_id')
                ->select('registered_users.name', 'registered_users.email', 'registered_users.mobile', 'gender_types.name_en As gender_name', 'spd.name_en AS package_name'
                        , DB::raw('(CASE WHEN spd.num_points = 0 THEN "Unlimited" ELSE spd.num_points  END) AS num_points')
                        , DB::raw('COALESCE(SUM(sat.status),0) as count_class'), 'spd.end_date'
                        , DB::raw('CONCAT(DATE_FORMAT(spd.start_date,"%d/%m/%Y"), "- ", DATE_FORMAT(spd.end_date,"%d/%m/%Y")) AS period'), 'spd.start_date')
                ->where('spd.trainer_id', Auth::guard('trainer')->user()->id)
                ->whereNotIn('spd.subscriber_id', function($query) {
                            $query->select(DB::raw('ts.subscriber_id'))
                            ->from('trainer_subscribers_package_details As ts')
                            ->where(function ($query) {
                                $query->where('ts.active_status', '=', 1)
                                ->orwhere('ts.active_status', '=', 0);
                            })
                            ->whereColumn('spd.subscriber_id', 'ts.subscriber_id');
                        });


        // if Request having Pacakge name
        if ($request->has('name_en') && $request->get('name_en') != '') {
            $name_en = $request->get('name_en');
            $session_array['name_en'] = $name_en;
            Session::set('reportMemberExpired', $session_array);
            Session::flash('reportMemberExpired', Session::get('reportMemberExpired'));
            $SubscribersList->where('spd.name_en', 'like', "$name_en%");
        }

        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $session_array['start_date'] = $start_date;
            $session_array['end_date'] = $end_date;
            Session::set('reportMemberExpired', $session_array);
            Session::flash('reportMemberExpired', Session::get('reportMemberExpired'));
            $SubscribersList->whereBetween('spd.end_date', [$start_date, $end_date]);
        }

        // if Request having Member id
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            $session_array['id'] = $ID;
            Session::set('reportMemberExpired', $session_array);
            Session::flash('reportMemberExpired', Session::get('reportMemberExpired'));
            $SubscribersList->where('spd.subscriber_id', $ID);
        }
        
        // if Request having Gender id
        if ($request->has('gender_id') && $request->get('gender_id') != 0) {
            $GenderID = $request->get('gender_id');
            $session_array['gender_id'] = $GenderID;
            Session::set('reportMemberExpired', $session_array);
            Session::flash('reportMemberExpired', Session::get('reportMemberExpired'));
            $SubscribersList->where('registered_users.gender_id', 'like', "$GenderID%");
        }

        $SubscribersList->groupby('spd.subscriber_id');
        $Subscribers = $SubscribersList->get();
        $Count = sizeof($SubscribersList->get()->toArray());

        //Get All Packages 
        $Packages = DB::table('trainer_packages')
                ->select('id', 'name_en')
                ->where('trainer_id', Auth::guard('trainer')->user()->id)
                ->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Subscribers)
                            ->editColumn('end_date', function ($Subscribers) {
                                $newYear = new Carbon($Subscribers->start_date);
                                $endYear = new Carbon($Subscribers->end_date);
                                return $newYear->format('d/m/Y') . ' - ' . $endYear->format('d/m/Y');
                            })
                            ->with('count', $Count)
                            ->make();
        }

        $Members = DB::table($this->table . ' As m')
                ->join($this->packageTable . ' As sp', 'sp.subscriber_id', '=', 'm.id')
                ->select('m.name', 'm.id')
                ->where(array('m.status' => 1, 'sp.trainer_id' => Auth::guard('trainer')->user()->id))
                ->groupby('sp.subscriber_id')
                ->get();

         //Get All Genders 
        $Genders = DB::table('gender_types')
                ->select('id', 'name_en')
                ->limit(2)
                ->get();

        return view('trainer.reports.subscriptionExpired')
                        ->with('Packages', $Packages)
                ->with('Genders', $Genders)
                        ->with('Members', $Members)
                        ->with('Count', $Count);
    }

    /**
     * Display a listing of the Subscription Expired.
     */
    public function subscriptions(Request $request) {
        //TrainerDetail::setSubscriberPackageStatus();
        $this->table = 'registered_users';
        $this->packageTable = 'trainer_subscribers_package_details';

        $SubscribersList = DB::table('trainer_subscribers_package_details As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->leftjoin('subscriber_attend_trainers As sat ', 'sat.subscribed_package_id', '=', 'spd.id')
                ->leftjoin('gender_types', 'gender_types.id', '=', 'registered_users.gender_id')
                ->select('registered_users.name', 'registered_users.email', 'registered_users.mobile', 'gender_types.name_en As gender_name', 'spd.name_en AS package_name'
                        , DB::raw('(CASE WHEN spd.num_points = 0 THEN "Unlimited" ELSE spd.num_points  END) AS num_points')
                        , DB::raw('COALESCE(SUM(sat.status),0) as count_class'), 'spd.start_date',
                        DB::raw('CONCAT(DATE_FORMAT(spd.start_date,"%d/%m/%Y"), "- ", DATE_FORMAT(spd.end_date,"%d/%m/%Y")) AS period'), 'spd.end_date')
                ->where('spd.trainer_id', Auth::guard('trainer')->user()->id)
                ->where('spd.active_status', 1);


        // if Request having Pacakge name
        if ($request->has('name_en') && $request->get('name_en') != '') {
            $name_en = $request->get('name_en');
            $session_array['name_en'] = $name_en;
            Session::set('reportMemberSubscribed', $session_array);
            Session::flash('reportMemberSubscribed', Session::get('reportMemberSubscribed'));
            $SubscribersList->where('spd.name_en', 'like', "$name_en%");
        }

        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $session_array['start_date'] = $start_date;
            $session_array['end_date'] = $end_date;
            Session::set('reportMemberSubscribed', $session_array);
            Session::flash('reportMemberSubscribed', Session::get('reportMemberSubscribed'));
            $SubscribersList->whereBetween('spd.start_date', [$start_date, $end_date]);
        }

        // if Request having Member id
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            $session_array['id'] = $ID;
            Session::set('reportTrainerMemberSubscribed', $session_array);
            Session::flash('reportMemberSubscribed', Session::get('reportMemberSubscribed'));
            $SubscribersList->where('spd.subscriber_id', $ID);
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
            $SubscribersList->whereBetween('spd.end_date', [$currentDate, $expiry]);
        }
        
         // if Request having Gender id
        if ($request->has('gender_id') && $request->get('gender_id') != 0) {
            $GenderID = $request->get('gender_id');
            $session_array['gender_id'] = $GenderID;
            Session::set('reportMemberSubscribed', $session_array);
            Session::flash('reportMemberSubscribed', Session::get('reportMemberSubscribed'));
            $SubscribersList->where('registered_users.gender_id', 'like', "$GenderID%");
        }

        

        $SubscribersList->groupby('spd.subscriber_id');


        $Subscribers = $SubscribersList->get();
        $Count = sizeof($SubscribersList->get()->toArray());


        //Get All Packages 
        $Packages = DB::table('trainer_packages')
                ->select('id', 'name_en')
                ->where('trainer_id', Auth::guard('trainer')->user()->id)
                ->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Subscribers)
                            ->editColumn('end_date', function ($Subscribers) {
                                $newYear = new Carbon($Subscribers->start_date);
                                $endYear = new Carbon($Subscribers->end_date);
                                return $newYear->format('d/m/Y') . ' - ' . $endYear->format('d/m/Y');
                            })
                            ->with('count', $Count)
                            ->make();
        }

        $Members = DB::table($this->table . ' As m')
                ->join($this->packageTable . ' As sp', 'sp.subscriber_id', '=', 'm.id')
                ->select('m.name', 'm.id')
                ->where(array('m.status' => 1, 'sp.trainer_id' => Auth::guard('trainer')->user()->id))
                ->groupby('sp.subscriber_id')
                ->get();

        //Get All Genders 
        $Genders = DB::table('gender_types')
                ->select('id', 'name_en')
                ->limit(2)
                ->get();

        return view('trainer.reports.subscriptions')
                        ->with('Packages', $Packages)
                        ->with('Members', $Members)
                ->with('Genders', $Genders)
                        ->with('Count', $Count);
    }

    /**
     * Display a listing of the Subscriptions.
     */
    public function attendance(Request $request) {

        $this->table = 'registered_users';
        $this->packageTable = 'trainer_subscribers_package_details';

        $SubscribersList = DB::table('subscriber_attend_trainers As sat')
                ->leftjoin('trainer_subscribers_package_details As spd', 'sat.subscribed_package_id', '=', 'spd.id')
                ->leftjoin('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->select('registered_users.name', 'spd.name_en AS package_name', 'sat.date')
                ->where('spd.trainer_id', Auth::guard('trainer')->user()->id);


        // if Request having Pacakge name
        if ($request->has('name_en') && $request->get('name_en') != '') {
            $name_en = $request->get('name_en');
            $session_array['name_en'] = $name_en;
            Session::set('reportAttendance', $session_array);
            Session::flash('reportAttendance', Session::get('reportAttendance'));
            $SubscribersList->where('spd.name_en', 'like', "$name_en%");
        }

        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $session_array['start_date'] = $start_date;
            $session_array['end_date'] = $end_date;
            Session::set('reportAttendance', $session_array);
            Session::flash('reportAttendance', Session::get('reportAttendance'));
            $SubscribersList->whereBetween('sat.date', [$start_date, $end_date]);
        }

        // if Request having Member id
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            $session_array['id'] = $ID;
            Session::set('reportAttendance', $session_array);
            Session::flash('reportAttendance', Session::get('reportAttendance'));
            $SubscribersList->where('spd.subscriber_id', $ID);
        }



        $Subscribers = $SubscribersList->get();
        $Count = sizeof($SubscribersList->get()->toArray());


        //Get All Packages 
        $Packages = DB::table('trainer_packages')
                ->select('id', 'name_en')
                ->where('trainer_id', Auth::guard('trainer')->user()->id)
                ->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Subscribers)
                            ->editColumn('date', function ($Subscribers) {
                                $newYear = new Carbon($Subscribers->date);
                                return $newYear->format('d/m/Y g:i:A');
                            })
                            ->with('count', $Count)
                            ->make();
        }

        $Members = DB::table($this->table . ' As m')
                ->join($this->packageTable . ' As sp', 'sp.subscriber_id', '=', 'm.id')
                ->select('m.name', 'm.id')
                ->where(array('m.status' => 1, 'sp.trainer_id' => Auth::guard('trainer')->user()->id))
                ->groupby('sp.subscriber_id')
                ->get();

        return view('trainer.reports.attendance')
                        ->with('Packages', $Packages)
                        ->with('Members', $Members)
                        ->with('Count', $Count);
    }

}
