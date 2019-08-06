<?php

namespace App\Http\Controllers\Vendor\Module1;

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
use App\Helpers\VendorDetail;

class ReportExcelController extends Controller {

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
        //if Request having Date Range        
        if (Session::has('reportFavourites')) {
            $val = Session::get('reportFavourites');
            $favouriteList->whereBetween('f.created_at', [$val['start_date'], $val['end_date']]);
        }
        $Favourites = $favouriteList->get()->toArray();

        // Define the Excel spreadsheet headers
        $tableArray[] = ['Name', 'Created at'];

        // Convert each member of the returned collection into an array,
        // and append it to the array.
        foreach ($Favourites as $table_data) {
            if (!is_null($table_data->created_at)) {
                $sdate = new Carbon($table_data->created_at);
                $table_data->created_at = $sdate->format('d/m/Y');
            }
            $tableArray[] = (array) $table_data;
        }

        // Generate and return the spreadsheet
        Excel::create('Favourites', function($excel) use($tableArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Favourites');
            $excel->setCreator('Creativity')->setCompany(config('global.appTitle'));
            $excel->setDescription('Favourites');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($tableArray) {
                $sheet->fromArray($tableArray, null, 'A1', false, false);
            });
        })->download('xlsx');

        return redirect($this->configName . '/favourites');
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

        $tableArray = [];



        //Get Member & Invoice
        $invoiceList = DB::table($this->invoiceTable . ' As inv')
                ->join($this->table . ' As m', 'inv.member_id', '=', 'm.id')
                ->join('vendor_users As vu', 'vu.id', '=', 'inv.collected_by')
                ->select('inv.receipt_num', 'm.name', 'inv.package_name', 'inv.created_at', 'vu.name AS collected_by', 'inv.cash', 'inv.knet', 'inv.price');

        $invoiceAmountList = DB::table($this->invoiceTable . ' As inv')
                ->select(DB::raw('SUM(cash) as cash_amount')
                , DB::raw('SUM(knet) as knet_amount'), DB::raw('SUM(price) as fees'));

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
        $Invoices = $invoiceList->get()->toArray();
        $invoiceAmount = $invoiceAmountList->first();

        // Define the Excel spreadsheet headers
        $tableArray[] = ['Invoice No.', 'Name', 'Package Name', 'Collected On', 'Collected By', 'cash', 'knet', 'Fees'];

        // Convert each member of the returned collection into an array,
        // and append it to the array.
        foreach ($Invoices as $table_data) {

            if (!is_null($table_data->created_at)) {
                $sdate = new Carbon($table_data->created_at);
                $table_data->created_at = $sdate->format('d/m/Y');
            }

            $tableArray[] = (array) $table_data;
        }
        $count = count($tableArray) + 1;

        $tableArray[$count]['Total Amount'] = 'Total Amount (' . config('global.amountCurrency') . ') ' . $invoiceAmount->fees;
        $tableArray[$count]['Total Credit Card'] = 'Total Credit Card (' . config('global.amountCurrency') . ') ' . $invoiceAmount->cash_amount;
        $tableArray[$count]['Total KNET'] = 'Total KNET (' . config('global.amountCurrency') . ') ' . $invoiceAmount->knet_amount;


        // Generate and return the spreadsheet
        Excel::create('Payment Invoice', function($excel) use($tableArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Payment Invoice');
            $excel->setCreator('Creativity')->setCompany(config('global.appTitle'));
            $excel->setDescription('Payment Invoice');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($tableArray) {
                $sheet->fromArray($tableArray, null, 'A1', false, false);
            });
        })->download('xlsx');

        return redirect($this->configName . '/payments');
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

        $payments = $paymentList->get()->toArray();
        $Amount = $AmountList->first();
        $KnetAmount = $KnetAmountList->first();
        $CCAmount = $CCAmountList->first();

        // Define the Excel spreadsheet headers
        $tableArray[] = ['Name', 'Package Name', 'Reference ID', 'Amount', 'Collected On', 'Payment Method'];

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
        Excel::create('Online Payments', function($excel) use($tableArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Online Payments');
            $excel->setCreator('Creativity')->setCompany(config('global.appTitle'));
            $excel->setDescription('Online Payments');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($tableArray) {
                $sheet->fromArray($tableArray, null, 'A1', false, false);
            });
        })->download('xlsx');

        return redirect($this->configName . '/onlinePayments');
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

        $Members = $MemberList->get()->toArray();

        // Define the Excel spreadsheet headers
        $tableArray[] = ['Name', 'Email', 'Mobile', 'Gender', 'Package Name', 'Period'];

        // Convert each member of the returned collection into an array,
        // and append it to the array.
        foreach ($Members as $table_data) {

            if (!is_null($table_data->start_date) && !is_null($table_data->end_date)) {
                $newYear = new Carbon($table_data->start_date);
                $endYear = new Carbon($table_data->end_date);
                $table_data->period = $newYear->format('d/m/Y') . ' - ' . $endYear->format('d/m/Y');
            }
            unset($table_data->start_date);
            unset($table_data->end_date);
            unset($table_data->subscribed_from);

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

        return redirect($this->configName . '/subscriptionExpired');
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
             // if Member Status //1Week:0, 2Week:1, #Week:2
           if (Session::has('reportMemberSubscribed.expiry')) {
                $MemberList->whereBetween('m.end_date', [$val['current_date'], $val['expiry']]);
            }
        }

        $Members = $MemberList->get()->toArray();

        // Define the Excel spreadsheet headers
        $tableArray[] = ['Name', 'Email', 'Mobile', 'Gender', 'Package Name', 'Period'];

        // Convert each member of the returned collection into an array,
        // and append it to the array.
        foreach ($Members as $table_data) {

            if (!is_null($table_data->start_date) && !is_null($table_data->end_date)) {
                $newYear = new Carbon($table_data->start_date);
                $endYear = new Carbon($table_data->end_date);
                $table_data->period = $newYear->format('d/m/Y') . ' - ' . $endYear->format('d/m/Y');
            }
            unset($table_data->start_date);
            unset($table_data->end_date);
            unset($table_data->subscribed_from);

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

        return redirect($this->configName . '/subscriptions');
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

        $InstructorSubscription = $InstructorSubscriptionList->get()->toArray();

        // Define the Excel spreadsheet headers
        $tableArray[] = ['Subscriber', 'Mobile', 'Package', 'Price', 'No. Sessions', 'Booked', 'Subscribed On'];

        // Convert each member of the returned collection into an array,
        // and append it to the array.
        foreach ($InstructorSubscription as $table_data) {

            if (!is_null($table_data->created_at)) {
                $newYear = new Carbon($table_data->created_at);
                $table_data->created_at = $newYear->format('d/m/Y');
            }
            
            $tableArray[] = (array) $table_data;
        }

        // Generate and return the spreadsheet
        Excel::create('Instruction Subscriptions', function($excel) use($tableArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Instruction Subscriptions');
            $excel->setCreator('Creativity')->setCompany(config('global.appTitle'));
            $excel->setDescription('Instruction Subscriptions');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($tableArray) {
                $sheet->fromArray($tableArray, null, 'A1', false, false);
            });
        })->download('xlsx');

        return redirect($this->configName . '/instructorSubscriptions');
    }

}
