<?php

namespace App\Http\Controllers\Trainer;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Excel;
use Carbon\Carbon;
use PHPExcel_Cell;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Helpers\TrainerDetail;

class ReportExcelController extends Controller {

    protected $guard = 'trainer';

    public function __construct() {
        $this->middleware($this->guard);
    }

    /**
     * Display a listing of the Payments.
     */
    public function payment(Request $request) {

        $this->table = 'registered_users';
        $this->packageTable = 'trainer_subscribers_package_details';

        $tableArray = [];

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
        if (Session::has('reportPayments')) {
            $val = Session::get('reportPayments');
            if (Session::has('reportPayments.start_date')) {
                $paymentList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
                $AmountList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
                $KnetAmountList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
                $CCAmountList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
            }
            // if Request having Member id
            if (Session::has('reportPayments.id')) {
                $val = Session::get('reportPayments');
                $ID = $val['id'];
                $paymentList->where('sp.subscriber_id', $ID);
                $AmountList->where('sp.subscriber_id', $ID);
                $KnetAmountList->where('sp.subscriber_id', $ID);
                $CCAmountList->where('sp.subscriber_id', $ID);
            }
        }

        $payments = $paymentList->get()->toArray();
        $Amount = $AmountList->first();
        $KnetAmount = $KnetAmountList->first();
        $CCAmount = $CCAmountList->first();

        // Define the Excel spreadsheet headers
        $tableArray[] = ['Name', 'Package Name', 'Reference ID', 'Amount (' . config('global.amountCurrency') . ')', 'Collected On', 'Payment Method'];

        // Convert each member of the returned collection into an array,
        // and append it to the array.
        foreach ($payments as $table_data) {

            if (!is_null($table_data->post_date)) {
                $sdate = new Carbon($table_data->post_date);
                $table_data->post_date = $sdate->format('d/m/Y');
            }

            $tableArray[] = (array) $table_data;
        }
        $count = count($tableArray) + 1;

        $tableArray[$count]['Total Amount'] = 'Total Amount (' . config('global.amountCurrency') . ') ' . $Amount->fees;
        $tableArray[$count]['Total Credit Card'] = 'Total Credit Card  (' . config('global.amountCurrency') . ') ' . $CCAmount->cc_amount;
        $tableArray[$count]['Total KNET'] = 'Total KNET  (' . config('global.amountCurrency') . ') ' . $KnetAmount->knet_amount;


        // Generate and return the spreadsheet
        Excel::create('Payments', function($excel) use($tableArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Payments');
            $excel->setCreator('Creativity')->setCompany(config('global.appTitle'));
            $excel->setDescription('Payments');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($tableArray) {
                $sheet->fromArray($tableArray, null, 'A1', false, false);
            });
        })->download('xlsx');

        return redirect('trainer/payments');
    }

    /**
     * Display a listing of the Subscription Expired.
     */
    public function subscriptionExpired(Request $request) {
        //TrainerDetail::setSubscriberPackageStatus();
        $this->table = 'registered_users';
        $this->packageTable = 'trainer_subscribers_package_details';

        $SubscribersList = DB::table('trainer_subscribers_package_details As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->leftjoin('subscriber_attend_trainers As sat ', 'sat.subscribed_package_id', '=', 'spd.id')
                ->leftjoin('gender_types', 'gender_types.id', '=', 'registered_users.gender_id')
                ->select('registered_users.name', 'registered_users.email', 'registered_users.mobile', 'gender_types.name_en As gender_name', 'spd.name_en AS package_name'
                        , DB::raw('(CASE WHEN spd.num_points = 0 THEN "Unlimited" ELSE spd.num_points  END) AS num_points')
                        , DB::raw('COALESCE(SUM(sat.status),0) as count_class'), 'spd.start_date', 'spd.end_date')
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


        if (Session::has('reportMemberExpired')) {
            $val = Session::get('reportMemberExpired');

            //if Request having Date Range
            if (Session::has('reportMemberExpired.start_date')) {
                $SubscribersList->whereBetween('spd.end_date', [$val['start_date'], $val['end_date']]);
            }
            // if Request having Subscriber id
            if (Session::has('reportMemberExpired.id')) {
                $val = Session::get('reportMemberExpired');
                $ID = $val['id'];
                $SubscribersList->where('spd.subscriber_id', $ID);
            }
            // if Request having Package Name
            if (Session::has('reportMemberExpired.name_en')) {
                $val = Session::get('reportMemberExpired');
                $name_en = $val['name_en'];
                $SubscribersList->where('spd.name_en', 'like', "$name_en%");
            }
             // if Request having Gender id
            if (Session::has('reportMemberExpired.gender_id')) {
                $val = Session::get('reportMemberExpired');
                $GenderID = $val['gender_id'];
                $SubscribersList->where('registered_users.gender_id', 'like', "$GenderID%");
            }
        }


        $SubscribersList->groupby('spd.subscriber_id');
        $Subscribers = $SubscribersList->get()->toArray();

        // Define the Excel spreadsheet headers
        $tableArray[] = ['Name', 'Eamil', 'Mobile', 'Gender', 'Package Name', 'No. of Classes', 'Attendance', 'Period'];

        // Convert each member of the returned collection into an array,
        // and append it to the array.
        foreach ($Subscribers as $table_data) {

            if (!is_null($table_data->start_date) && !is_null($table_data->end_date)) {
                $newYear = new Carbon($table_data->start_date);
                $endYear = new Carbon($table_data->end_date);
                $table_data->period = $newYear->format('d/m/Y') . ' - ' . $endYear->format('d/m/Y');
            }
            unset($table_data->start_date);
            unset($table_data->end_date);
            $tableArray[] = (array) $table_data;
        }

        // Generate and return the spreadsheet
        Excel::create('Subscription Expired', function($excel) use($tableArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Subscription Expired');
            $excel->setCreator('Creativity')->setCompany(config('global.appTitle'));
            $excel->setDescription('Subscription Expired');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($tableArray) {
                $sheet->fromArray($tableArray, null, 'A1', false, false);
            });
        })->download('xlsx');
    }

    /**
     * Display a listing of the Subscriptions.
     */
    public function subscriptions(Request $request) {
       // TrainerDetail::setSubscriberPackageStatus();
        $this->table = 'registered_users';
        $this->packageTable = 'trainer_subscribers_package_details';

        $SubscribersList = DB::table('trainer_subscribers_package_details As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->leftjoin('subscriber_attend_trainers As sat ', 'sat.subscribed_package_id', '=', 'spd.id')
                ->leftjoin('gender_types', 'gender_types.id', '=', 'registered_users.gender_id')
                ->select('registered_users.name', 'registered_users.email', 'registered_users.mobile', 'gender_types.name_en As gender_name', 'spd.name_en AS package_name'
                        , DB::raw('(CASE WHEN spd.num_points = 0 THEN "Unlimited" ELSE spd.num_points  END) AS num_points')
                        , DB::raw('COALESCE(SUM(sat.status),0) as count_class'), 'spd.start_date', 'spd.end_date')
                ->where('spd.trainer_id', Auth::guard('trainer')->user()->id)
                ->where('spd.active_status', 1);



        if (Session::has('reportMemberSubscribed')) {
            $val = Session::get('reportMemberSubscribed');

            //if Request having Date Range
            if (Session::has('reportMemberSubscribed.start_date')) {
                $SubscribersList->whereBetween('spd.start_date', [$val['start_date'], $val['end_date']]);
            }
            // if Request having Subscriber id
            if (Session::has('reportMemberSubscribed.id')) {
                $val = Session::get('reportMemberSubscribed');
                $ID = $val['id'];
                $SubscribersList->where('spd.subscriber_id', $ID);
            }
            // if Request having Package Name
            if (Session::has('reportMemberSubscribed.name_en')) {
                $val = Session::get('reportMemberSubscribed');
                $name_en = $val['name_en'];
                $SubscribersList->where('spd.name_en', 'like', "$name_en%");
            }

            // if Request having Member Type
            if (Session::has('reportMemberSubscribed.expiry')) {
                $SubscribersList->whereBetween('spd.end_date', [$val['current_date'], $val['expiry']]);
            }
            // if Request having Gender id
            if (Session::has('reportMemberSubscribed.gender_id')) {
                $val = Session::get('reportMemberSubscribed');
                $GenderID = $val['gender_id'];
                $SubscribersList->where('registered_users.gender_id', 'like', "$GenderID%");
            }
        }


        $SubscribersList->groupby('spd.subscriber_id');

        $Subscribers = $SubscribersList->get()->toArray();

        // Define the Excel spreadsheet headers
        $tableArray[] = ['Name', 'Eamil', 'Mobile', 'Gender', 'Package Name', 'No. of Classes', 'Attendance', 'Period'];

        // Convert each member of the returned collection into an array,
        // and append it to the array.
        foreach ($Subscribers as $table_data) {

            if (!is_null($table_data->start_date) && !is_null($table_data->end_date)) {
                $newYear = new Carbon($table_data->start_date);
                $endYear = new Carbon($table_data->end_date);
                $table_data->period = $newYear->format('d/m/Y') . ' - ' . $endYear->format('d/m/Y');
            }
            unset($table_data->start_date);
            unset($table_data->end_date);
            $tableArray[] = (array) $table_data;
        }

        // Generate and return the spreadsheet
        Excel::create('Subscriptions', function($excel) use($tableArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Subscriptions');
            $excel->setCreator('Creativity')->setCompany(config('global.appTitle'));
            $excel->setDescription('Subscriptions');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($tableArray) {
                $sheet->fromArray($tableArray, null, 'A1', false, false);
            });
        })->download('xlsx');
    }

    /**
     * Display a listing of the Attendance.
     */
    public function attendance(Request $request) {

        $this->table = 'registered_users';
        $this->packageTable = 'trainer_subscribers_package_details';

        $SubscribersList = DB::table('subscriber_attend_trainers As sat')
                ->leftjoin('trainer_subscribers_package_details As spd', 'sat.subscribed_package_id', '=', 'spd.id')
                ->leftjoin('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->select('registered_users.name', 'spd.name_en AS package_name', 'sat.date')
                ->where('spd.trainer_id', Auth::guard('trainer')->user()->id);



        if (Session::has('reportAttendance')) {
            $val = Session::get('reportAttendance');

            //if Request having Date Range
            if (Session::has('reportAttendance.date')) {
                $SubscribersList->whereBetween('spd.date', [$val['start_date'], $val['end_date']]);
            }
            // if Request having Subscriber id
            if (Session::has('reportAttendance.id')) {
                $val = Session::get('reportAttendance');
                $ID = $val['id'];
                $SubscribersList->where('spd.subscriber_id', $ID);
            }
            // if Request having Package Name
            if (Session::has('reportAttendance.name_en')) {
                $val = Session::get('reportAttendance');
                $name_en = $val['name_en'];
                $SubscribersList->where('spd.name_en', 'like', "$name_en%");
            }
        }

        $Subscribers = $SubscribersList->get()->toArray();

        // Define the Excel spreadsheet headers
        $tableArray[] = ['Name', 'Package Name', 'Date of Attend'];

        // Convert each member of the returned collection into an array,
        // and append it to the array.
        foreach ($Subscribers as $table_data) {

            if (!is_null($table_data->date)) {
                $newYear = new Carbon($table_data->date);
                $table_data->date = $newYear->format('d/m/Y g:i:A');
            }

            $tableArray[] = (array) $table_data;
        }

        // Generate and return the spreadsheet
        Excel::create('Subscriber Attendance', function($excel) use($tableArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Subscriber Attendance');
            $excel->setCreator('Creativity')->setCompany(config('global.appTitle'));
            $excel->setDescription('Subscriber Attendance');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($tableArray) {
                $sheet->fromArray($tableArray, null, 'A1', false, false);
            });
        })->download('xlsx');
    }

}
