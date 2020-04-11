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
use Illuminate\Support\Facades\Auth;
use App\Helpers\Permit;
use App\Helpers\TrainerDetail;

class TrainerReportController extends Controller {

    protected $guard = 'auth';
    protected $ViewAccess;
    protected $PrintAccess;

    public function __construct() {
        $this->middleware($this->guard);
        //$this->middleware('permission:reports');
    }

    /**
     * Display a listing of the Online Payments.
     */
    public function payment(Request $request) {

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('trainerpayments-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('trainerpayments-print');

        $this->table = 'registered_users';
        $this->packageTable = 'trainer_subscribers_package_details';

        //Get package payment details
        $paymentList = DB::table('payment_details As p')
                ->join($this->packageTable . ' As sp', 'sp.payment_id', '=', 'p.id')
                ->leftjoin($this->table . ' As m', 'sp.subscriber_id', '=', 'm.id')
                ->leftjoin('trainer_packages As tp', 'tp.id', '=', 'p.package_id')
                ->leftjoin('trainers As t', 't.id', '=', 'sp.trainer_id')
                ->select('t.name AS trainer', 'm.name', 'tp.name_en AS package_name', 'p.reference_id', 'p.amount', 'p.post_date', DB::raw('(CASE WHEN p.card_type = 1 THEN "KNET" ELSE "Credit Card" END) AS payment_method'));


        $KnetAmountList = DB::table('payment_details As p')
                ->join($this->packageTable . ' As sp', 'sp.payment_id', '=', 'p.id')
                ->join('knet_payments AS k', 'k.id', '=', 'p.payid')
                ->select(DB::raw('COALESCE(SUM(k.amount),0) as knet_amount'))
                ->where('p.card_type', 1);

        $CCAmountList = DB::table('payment_details As p')
                ->join($this->packageTable . ' As sp', 'sp.payment_id', '=', 'p.id')
                ->join('cc_payments AS c', 'c.id', '=', 'p.payid')
                ->select(DB::raw('COALESCE(SUM(c.amount),0) as cc_amount'))
                ->where('p.card_type', 2);

        $AmountList = DB::table('payment_details As p')
                ->join($this->packageTable . ' As sp', 'sp.payment_id', '=', 'p.id')
                ->select(DB::raw('COALESCE(SUM(sp.price),0) as fees'));

        $MemberList = DB::table($this->table . ' As m')
                ->join($this->packageTable . ' As sp', 'sp.subscriber_id', '=', 'm.id')
                ->select('m.name', 'm.id')
                ->where(array('m.status' => 1))
                ->groupby('sp.subscriber_id');

        $str = '';

        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $session_array['start_date'] = $start_date;
            $session_array['end_date'] = $end_date;
            Session::set('reportTrainerPayments', $session_array);
            Session::flash('reportTrainerPayments', Session::get('reportTrainerPayments'));
            $paymentList->whereBetween('p.post_date', [$start_date, $end_date]);
            $AmountList->whereBetween('p.post_date', [$start_date, $end_date]);
            $KnetAmountList->whereBetween('p.post_date', [$start_date, $end_date]);
            $CCAmountList->whereBetween('p.post_date', [$start_date, $end_date]);
        }

        // if Request having Member id
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            $session_array['id'] = $ID;
            Session::set('reportTrainerPayments', $session_array);
            Session::flash('reportTrainerPayments', Session::get('reportTrainerPayments'));
            $paymentList->where('sp.subscriber_id', $ID);
            $AmountList->where('sp.subscriber_id', $ID);
            $KnetAmountList->where('sp.subscriber_id', $ID);
            $CCAmountList->where('sp.subscriber_id', $ID);
        }
        // if Request having Trainer Name
        if ($request->has('trainer_id') && $request->get('trainer_id') != 0) {
            $ID = $request->get('trainer_id');
            $session_array['trainer_id'] = $ID;
            Session::set('reportTrainerPayments', $session_array);
            Session::flash('reportTrainerPayments', Session::get('reportTrainerPayments'));
            $paymentList->where('sp.trainer_id', $ID);
            $AmountList->where('sp.trainer_id', $ID);
            $KnetAmountList->where('sp.trainer_id', $ID);
            $CCAmountList->where('sp.trainer_id', $ID);
            $MemberList->where('sp.trainer_id', $ID);
        }

        $payments = $paymentList->get();
        $Amount = $AmountList->first();
        $KnetAmount = $KnetAmountList->first();
        $CCAmount = $CCAmountList->first();
        $Members = $MemberList->get();

        //For memebers 
        $str .= '<option value="0">--All--</option>';
        foreach ($Members as $Member) {
            $str .= '<option value="' . $Member->id . '">' . $Member->name . '</option>';
        }



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
                            ->with('str', $str)
                            ->make();
        }



        $Trainers = DB::table('trainers')
                ->select('name', 'id')
                ->where(array('status' => 1))
                ->whereNull('deleted_at')
                ->get();

        return view('admin.trainerReports.payments')
                        ->with('Trainers', $Trainers)
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

        //TrainerDetail::setSubscriberPackageStatus();

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('trainerSubscriptionExpired-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('trainerSubscriptionExpired-print');

        $this->table = 'registered_users';
        $this->packageTable = 'trainer_subscribers_package_details';

        //Get All Packages 
        $Packages = DB::table('trainer_packages')
                        ->select('id', 'name_en')->get();

        $str = '';
        $memberStr = '';

        $SubscribersList = DB::table('trainer_subscribers_package_details As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->leftjoin('subscriber_attend_trainers As sat ', 'sat.subscribed_package_id', '=', 'spd.id')
                ->leftjoin('gender_types', 'gender_types.id', '=', 'registered_users.gender_id')
                ->leftjoin('trainers As t', 't.id', '=', 'spd.trainer_id')
                ->select('t.name AS trainer', 'registered_users.name', 'registered_users.email', 'registered_users.mobile', 'gender_types.name_en As gender_name', 'spd.name_en AS package_name'
                        , DB::raw('(CASE WHEN spd.num_points = 0 THEN "Unlimited" ELSE spd.num_points  END) AS num_points')
                        , DB::raw('COALESCE(SUM(sat.status),0) as count_class'), 'spd.end_date'
                        , DB::raw('CONCAT(DATE_FORMAT(spd.start_date,"%d/%m/%Y"), "- ", DATE_FORMAT(spd.end_date,"%d/%m/%Y")) AS period'), 'spd.start_date')
                ->whereNotIn('spd.subscriber_id', function($query) {
            $query->select(DB::raw('ts.subscriber_id'))
            ->from('trainer_subscribers_package_details As ts')
            ->where(function ($query) {
                $query->where('ts.active_status', '=', 1)
                ->orwhere('ts.active_status', '=', 0);
            })
            ->whereColumn('spd.subscriber_id', 'ts.subscriber_id');
        });

        $MemberList = DB::table($this->table . ' As m')
                ->join($this->packageTable . ' As sp', 'sp.subscriber_id', '=', 'm.id')
                ->select('m.name', 'm.id')
                ->where('module_id', 1)
                ->whereNotNull('trainer_id')
                ->WhereNull('vendor_id')
                ->where(array('m.status' => 1))
                ->groupby('sp.subscriber_id');


        // if Request having Pacakge name
        if ($request->has('name_en') && $request->get('name_en') != '') {
            $name_en = $request->get('name_en');
            $session_array['name_en'] = $name_en;
            Session::set('reportTrainerMemberExpired', $session_array);
            Session::flash('reportTrainerMemberExpired', Session::get('reportTrainerMemberExpired'));
            $SubscribersList->where('spd.name_en', 'like', "$name_en%");
        }

        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $session_array['start_date'] = $start_date;
            $session_array['end_date'] = $end_date;
            Session::set('reportTrainerMemberExpired', $session_array);
            Session::flash('reportTrainerMemberExpired', Session::get('reportTrainerMemberExpired'));
            $SubscribersList->whereBetween('spd.end_date', [$start_date, $end_date]);
        }

        // if Request having Member id
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            $session_array['id'] = $ID;
            Session::set('reportTrainerMemberExpired', $session_array);
            Session::flash('reportTrainerMemberExpired', Session::get('reportTrainerMemberExpired'));
            $SubscribersList->where('spd.subscriber_id', $ID);
        }
        // if Request having Trainer Name
        if ($request->has('trainer_id') && $request->get('trainer_id') != 0) {
            $ID = $request->get('trainer_id');
            $session_array['trainer_id'] = $ID;
            Session::set('reportTrainerMemberExpired', $session_array);
            Session::flash('reportTrainerMemberExpired', Session::get('reportTrainerMemberExpired'));
            $SubscribersList->where('spd.trainer_id', $ID);
            $MemberList->where('sp.trainer_id', $ID);

            //Get All Packages 
            $Packages = DB::table('trainer_packages')
                            ->select('id', 'name_en')->where('trainer_id',$ID)->get();

            $str .= '<option value="0">--All--</option>';
            foreach ($Packages as $Package) {
                $str .= '<option value="' . $Package->id . '">' . $Package->name_en . '</option>';
            }
        }

        // if Request having Gender id
        if ($request->has('gender_id') && $request->get('gender_id') != 0) {
            $GenderID = $request->get('gender_id');
            $session_array['gender_id'] = $GenderID;
            Session::set('reportTrainerMemberExpired', $session_array);
            Session::flash('reportTrainerMemberExpired', Session::get('reportTrainerMemberExpired'));
            $SubscribersList->where('registered_users.gender_id', 'like', "$GenderID%");
        }

        $SubscribersList->groupby('spd.subscriber_id');

//        $SubscribersList->groupby('sat.subscribed_package_id')
//                ->havingRaw('MAX(spd.end_date) < NOW() or count_class  = spd.num_points');


        $Subscribers = $SubscribersList->get();
        $Count = sizeof($SubscribersList->get()->toArray());
        $Members = $MemberList->get();

        //For memebers 
        $memberStr .= '<option value="0">--All--</option>';
        foreach ($Members as $Member) {
            $memberStr .= '<option value="' . $Member->id . '">' . $Member->name . '</option>';
        }


        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Subscribers)
                            ->with('str', $str)
                            ->with('memberStr', $memberStr)
                            ->with('count', $Count)
                            ->make();
        }

        $Trainers = DB::table('trainers')
                ->select('name', 'id')
                ->where(array('status' => 1))
                ->whereNull('deleted_at')
                ->get();

        //Get All Genders 
        $Genders = DB::table('gender_types')
                ->select('id', 'name_en')
                ->limit(2)
                ->get();

        return view('admin.trainerReports.subscriptionExpired')
                        ->with('Packages', $Packages)
                        ->with('Genders', $Genders)
                        ->with('Members', $Members)
                        ->with('Trainers', $Trainers)
                        ->with('Count', $Count)
                        ->with('PrintAccess', $this->PrintAccess);
    }

    /**
     * Display a listing of the Subscription Expired.
     */
    public function subscriptions(Request $request) {

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('trainerSubscriptions-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('trainerSubscriptions-print');

        $this->table = 'registered_users';
        $this->packageTable = 'trainer_subscribers_package_details';

        //Get All Packages 
        $Packages = DB::table('trainer_packages')
                ->select('id', 'name_en')
                ->get();

        $str = '';
        $memberStr = '';

        $MemberList = DB::table($this->table . ' As m')
                ->join($this->packageTable . ' As sp', 'sp.subscriber_id', '=', 'm.id')
                ->select('m.name', 'm.id')
                ->where(array('m.status' => 1))
                ->where('module_id', 1)
                ->whereNotNull('trainer_id')
                ->WhereNull('vendor_id')
                ->groupby('sp.subscriber_id');

        $SubscribersList = DB::table('trainer_subscribers_package_details As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->leftjoin('subscriber_attend_trainers As sat ', 'sat.subscribed_package_id', '=', 'spd.id')
                ->leftjoin('gender_types', 'gender_types.id', '=', 'registered_users.gender_id')
                ->leftjoin('trainers As t', 't.id', '=', 'spd.trainer_id')
                ->select('t.name As trainer', 'registered_users.name', 'registered_users.email', 'registered_users.mobile', 'gender_types.name_en As gender_name', 'spd.name_en AS package_name'
                , DB::raw('(CASE WHEN spd.num_points = 0 THEN "Unlimited" ELSE spd.num_points  END) AS num_points')
                , DB::raw('COALESCE(SUM(sat.status),0) as count_class'), 'spd.start_date', DB::raw('CONCAT(DATE_FORMAT(spd.start_date,"%d/%m/%Y"), "- ", DATE_FORMAT(spd.end_date,"%d/%m/%Y")) AS period'), 'spd.end_date');


        // if Request having Pacakge name
        if ($request->has('name_en') && $request->get('name_en') != '') {
            $name_en = $request->get('name_en');
            $session_array['name_en'] = $name_en;
            Session::set('reportTrainerMemberSubscribed', $session_array);
            Session::flash('reportTrainerMemberSubscribed', Session::get('reportTrainerMemberSubscribed'));
            $SubscribersList->where('spd.name_en', 'like', "$name_en%");
        }

        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $session_array['start_date'] = $start_date;
            $session_array['end_date'] = $end_date;
            Session::set('reportTrainerMemberSubscribed', $session_array);
            Session::flash('reportTrainerMemberSubscribed', Session::get('reportTrainerMemberSubscribed'));
            $SubscribersList->whereBetween('spd.start_date', [$start_date, $end_date]);
        }

        // if Request having Member id
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            $session_array['id'] = $ID;
            Session::set('reportTrainerMemberSubscribed', $session_array);
            Session::flash('reportTrainerMemberSubscribed', Session::get('reportTrainerMemberSubscribed'));
            $SubscribersList->where('spd.subscriber_id', $ID);
        }

        // if Request having Trainer Name
        if ($request->has('trainer_id') && $request->get('trainer_id') != 0) {
            $ID = $request->get('trainer_id');
            $session_array['trainer_id'] = $ID;
            Session::set('reportTrainerMemberSubscribed', $session_array);
            Session::flash('reportTrainerMemberSubscribed', Session::get('reportTrainerMemberSubscribed'));
            $SubscribersList->where('spd.trainer_id', $ID);
            $MemberList->where('sp.trainer_id', $ID);

            //Get All Packages 
            $Packages = DB::table('trainer_packages')
                            ->select('id', 'name_en')->where('trainer_id', $ID)->get();

            $str .= '<option value="0">--All--</option>';
            foreach ($Packages as $Package) {
                $str .= '<option value="' . $Package->id . '">' . $Package->name_en . '</option>';
            }
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
            Session::set('reportTrainerMemberSubscribed', $session_array);
            Session::flash('reportTrainerMemberSubscribed', Session::get('reportTrainerMemberSubscribed'));
            $SubscribersList->whereBetween('spd.end_date', [$currentDate, $expiry]);
        }
        // if Request having Gender id
        if ($request->has('gender_id') && $request->get('gender_id') != 0) {
            $GenderID = $request->get('gender_id');
            $session_array['gender_id'] = $GenderID;
            Session::set('reportTrainerMemberSubscribed', $session_array);
            Session::flash('reportTrainerMemberSubscribed', Session::get('reportTrainerMemberSubscribed'));
            $SubscribersList->where('registered_users.gender_id', 'like', "$GenderID%");
        }


        $SubscribersList->where('spd.active_status', 1)->groupby('spd.subscriber_id');
//        $SubscribersList->groupby('sat.subscribed_package_id')
//                ->havingRaw('MAX(spd.end_date) >= NOW()')
//                ->havingRaw('count_class  < spd.num_points');


        $Subscribers = $SubscribersList->get();
        $Count = sizeof($SubscribersList->get()->toArray());
        $Members = $MemberList->get();

        //For memebers 
        $memberStr .= '<option value="0">--All--</option>';
        foreach ($Members as $Member) {
            $memberStr .= '<option value="' . $Member->id . '">' . $Member->name . '</option>';
        }



        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Subscribers)
                            ->with('str', $str)
                            ->with('memberStr', $memberStr)
                            ->with('count', $Count)
                            ->make();
        }

        $Trainers = DB::table('trainers')
                ->select('name', 'id')
                ->where(array('status' => 1))
                ->whereNull('deleted_at')
                ->get();

        //Get All Genders 
        $Genders = DB::table('gender_types')
                ->select('id', 'name_en')
                ->limit(2)
                ->get();


        return view('admin.trainerReports.subscriptions')
                        ->with('Packages', $Packages)
                        ->with('Members', $Members)
                        ->with('Genders', $Genders)
                        ->with('Trainers', $Trainers)
                        ->with('Count', $Count)
                        ->with('PrintAccess', $this->PrintAccess);
    }

}
