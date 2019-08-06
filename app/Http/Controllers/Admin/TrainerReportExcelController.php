<?php

namespace App\Http\Controllers\Admin;

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
use App\Helpers\Permit;
use App\Helpers\TrainerDetail;

class TrainerReportExcelController extends Controller {

    protected $guard = 'auth';
    protected $PrintAccess;

    public function __construct() {
        $this->middleware($this->guard);
        // $this->middleware('permission:reports');
    }

    /**
     * Display a listing of the Payments.
     */
    public function payment(Request $request) {

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('trainerpayments-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        $this->table = 'registered_users';
        $this->packageTable = 'trainer_subscribers_package_details';

        $tableArray = [];

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

        //if Request having Date Range
        if (Session::has('reportTrainerPayments')) {
            $val = Session::get('reportTrainerPayments');
            if (Session::has('reportTrainerPayments.start_date')) {
                $paymentList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
                $AmountList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
                $KnetAmountList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
                $CCAmountList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
            }
            // if Request having Member id
            if (Session::has('reportTrainerPayments.id')) {
                $val = Session::get('reportTrainerPayments');
                $ID = $val['id'];
                $paymentList->where('sp.subscriber_id', $ID);
                $AmountList->where('sp.subscriber_id', $ID);
                $KnetAmountList->where('sp.subscriber_id', $ID);
                $CCAmountList->where('sp.subscriber_id', $ID);
            }
            // if Request having Trainer Name
            if (Session::has('reportTrainerPayments.trainer_id')) {
                $val = Session::get('reportTrainerPayments');
                $ID = $val['trainer_id'];
                $paymentList->where('sp.trainer_id', $ID);
                $AmountList->where('sp.trainer_id', $ID);
                $KnetAmountList->where('sp.trainer_id', $ID);
                $CCAmountList->where('sp.trainer_id', $ID);
            }
        }

        $payments = $paymentList->get()->toArray();
        $Amount = $AmountList->first();
        $KnetAmount = $KnetAmountList->first();
        $CCAmount = $CCAmountList->first();

        // Define the Excel spreadsheet headers
        $tableArray[] = ['Trainer Name','Name', 'Package Name', 'Reference ID', 'Amount (' . config('global.amountCurrency') . ')', 'Collected On', 'Payment Method'];

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
        Excel::create('Trainer Payments', function($excel) use($tableArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Trainer Payments');
            $excel->setCreator('Creativity')->setCompany(config('global.appTitle'));
            $excel->setDescription('Trainer Payments');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($tableArray) {
                $sheet->fromArray($tableArray, null, 'A1', false, false);
            });
        })->download('xlsx');

        return redirect('admin.trainerPrintReports.payments');
    }

    /**
     * Display a listing of the Subscription Expired.
     */
    public function subscriptionExpired(Request $request) {
        
       // TrainerDetail::setSubscriberPackageStatus();
        
         //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('trainerSubscriptionExpired-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        $this->table = 'registered_users';
        $this->packageTable = 'trainer_subscribers_package_details';

        $SubscribersList = DB::table('trainer_subscribers_package_details As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->leftjoin('subscriber_attend_trainers As sat ', 'sat.subscribed_package_id', '=', 'spd.id')
                ->leftjoin('gender_types', 'gender_types.id', '=', 'registered_users.gender_id')
                 ->leftjoin('trainers As t', 't.id', '=', 'spd.trainer_id')
                ->select('t.name AS trainer','registered_users.name', 'registered_users.email', 'registered_users.mobile', 'gender_types.name_en As gender_name', 'spd.name_en AS package_name'
                        , DB::raw('(CASE WHEN spd.num_points = 0 THEN "Unlimited" ELSE spd.num_points  END) AS num_points')
                        , DB::raw('COALESCE(SUM(sat.status),0) as count_class'), 'spd.start_date', 'spd.end_date')
                ->whereNotIn('spd.subscriber_id', function($query) {
                            $query->select(DB::raw('ts.subscriber_id'))
                            ->from('trainer_subscribers_package_details As ts')
                            ->where(function ($query) {
                                $query->where('ts.active_status', '=', 1)
                                ->orwhere('ts.active_status', '=', 0);
                            })
                            ->whereColumn('spd.subscriber_id', 'ts.subscriber_id');
                        });

         
        if (Session::has('reportTrainerMemberExpired')) {
            $val = Session::get('reportTrainerMemberExpired');

            //if Request having Date Range
            if (Session::has('reportTrainerMemberExpired.start_date')) {
                $SubscribersList->whereBetween('spd.end_date', [$val['start_date'], $val['end_date']]);
            }
            // if Request having Subscriber id
            if (Session::has('reportTrainerMemberExpired.id')) {
                $val = Session::get('reportTrainerMemberExpired');
                $ID = $val['id'];
                $SubscribersList->where('spd.subscriber_id', $ID);
            }
            // if Request having Package Name
            if (Session::has('reportTrainerMemberExpired.name_en')) {
                $val = Session::get('reportTrainerMemberExpired');
                $name_en = $val['name_en'];
                $SubscribersList->where('spd.name_en', 'like', "$name_en%");
            }
             // if Request having Trainer Name
            if (Session::has('reportTrainerMemberExpired.trainer_id')) {
                $val = Session::get('reportTrainerMemberExpired');
                $ID = $val['trainer_id'];
                $SubscribersList->where('spd.trainer_id', $ID);
            }
            // if Request having Gender id
            if (Session::has('reportTrainerMemberExpired.gender_id')) {
                $val = Session::get('reportTrainerMemberExpired');
                $GenderID = $val['gender_id'];
                $SubscribersList->where('registered_users.gender_id', 'like', "$GenderID%");
            }
        }

        
         $SubscribersList->groupby('spd.subscriber_id');

//        $SubscribersList->groupby('sat.subscribed_package_id')
//                ->havingRaw('MAX(spd.end_date) < NOW() or count_class  = spd.num_points');


        $Subscribers = $SubscribersList->get()->toArray();

        // Define the Excel spreadsheet headers
        $tableArray[] = ['Trainer Name','Name', 'Eamil', 'Mobile', 'Gender', 'Package Name', 'No. of Classes', 'Attendance', 'Period'];

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
        Excel::create('Trainer Subscription Expired', function($excel) use($tableArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Trainer Subscription Expired');
            $excel->setCreator('Creativity')->setCompany(config('global.appTitle'));
            $excel->setDescription('Trainer Subscription Expired');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($tableArray) {
                $sheet->fromArray($tableArray, null, 'A1', false, false);
            });
        })->download('xlsx');
        return redirect('admin.trainerPrintReports.trainerSubscriptionExpired');
    }

    /**
     * Display a listing of the Subscriptions.
     */
    public function subscriptions(Request $request) {
        
        //TrainerDetail::setSubscriberPackageStatus();
        
        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('trainerSubscriptions-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        $this->table = 'registered_users';
        $this->packageTable = 'trainer_subscribers_package_details';

        $SubscribersList = DB::table('trainer_subscribers_package_details As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->leftjoin('subscriber_attend_trainers As sat ', 'sat.subscribed_package_id', '=', 'spd.id')
                ->leftjoin('gender_types', 'gender_types.id', '=', 'registered_users.gender_id')
                ->select('registered_users.name', 'registered_users.email', 'registered_users.mobile', 'gender_types.name_en As gender_name', 'spd.name_en AS package_name'
                        , DB::raw('(CASE WHEN spd.num_points = 0 THEN "Unlimited" ELSE spd.num_points  END) AS num_points')
                        , DB::raw('COALESCE(SUM(sat.status),0) as count_class'), 'spd.start_date', 'spd.end_date');



        if (Session::has('reportTrainerMemberSubscribed')) {
            $val = Session::get('reportTrainerMemberSubscribed');

            //if Request having Date Range
            if (Session::has('reportTrainerMemberSubscribed.start_date')) {
                $SubscribersList->whereBetween('spd.start_date', [$val['start_date'], $val['end_date']]);
            }
            // if Request having Subscriber id
            if (Session::has('reportTrainerMemberSubscribed.id')) {
                $val = Session::get('reportTrainerMemberSubscribed');
                $ID = $val['id'];
                $SubscribersList->where('spd.subscriber_id', $ID);
            }
            // if Request having Package Name
            if (Session::has('reportTrainerMemberSubscribed.name_en')) {
                $val = Session::get('reportTrainerMemberSubscribed');
                $name_en = $val['name_en'];
                $SubscribersList->where('spd.name_en', 'like', "$name_en%");
            }
            
            // if Request having Trainer Name
            if (Session::has('reportTrainerMemberSubscribed.trainer_id')) {
                $val = Session::get('reportTrainerMemberSubscribed');
                $ID = $val['trainer_id'];
                $SubscribersList->where('spd.trainer_id', $ID);
            }
            
            // if Request having Member Type
           if (Session::has('reportTrainerMemberSubscribed.expiry')) {
                $SubscribersList->whereBetween('spd.end_date', [$val['current_date'], $val['expiry']]);
            }
            // if Request having Gender id
            if (Session::has('reportTrainerMemberSubscribed.gender_id')) {
                $val = Session::get('reportTrainerMemberSubscribed');
                $GenderID = $val['gender_id'];
                $SubscribersList->where('registered_users.gender_id', 'like', "$GenderID%");
            }
        }

        $SubscribersList->where('spd.active_status', 1)->groupby('spd.subscriber_id');

//        $SubscribersList->groupby('sat.subscribed_package_id')
//                ->havingRaw('MAX(spd.end_date) >= NOW()')
//                ->havingRaw('count_class  < spd.num_points');


        $Subscribers = $SubscribersList->get()->toArray();

        // Define the Excel spreadsheet headers
        $tableArray[] = ['Trainer Name','Name', 'Eamil', 'Mobile', 'Gender', 'Package Name', 'No. of Classes', 'Attendance', 'Period'];

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
        Excel::create('Trainer Subscriptions', function($excel) use($tableArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Trainer Subscriptions');
            $excel->setCreator('Creativity')->setCompany(config('global.appTitle'));
            $excel->setDescription('Trainer Subscriptions');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($tableArray) {
                $sheet->fromArray($tableArray, null, 'A1', false, false);
            });
        })->download('xlsx');
        return redirect('admin.trainerPrintReports.trainerSubscriptions');
    }

   

}
