<?php

namespace App\Http\Controllers\Vendor\Module1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Admin\Vendor;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Permit;
use App\Helpers\VendorDetail;
use App\Models\Admin\PaymentDetail;
use Carbon\Carbon;
use Charts;
use DB;

class DashboardController extends Controller {

    protected $guard = 'vendor';
    protected $configName;
    protected $table;
    protected $memberTable;
    protected $invoiceTable;

    public function __construct() {
        $this->middleware($this->guard);
        //$this->middleware('vendorPermission:M1Dashboard');
        $this->configName = config('global.fitflowVendor') . config('global.storeAddress') . '/' . config('global.M1');
    }

    public function index() {

        $this->table = VendorDetail::getPrefix() . 'subscribers_package_details';
        $this->memberTable = VendorDetail::getPrefix() . 'members';
        $this->invoiceTable = VendorDetail::getPrefix() . 'member_invoices';
        $this->registeredUserTable = 'registered_users';
        $this->instructorSubscriptionTable = 'instructor_subscribers_package_details';
        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('M1Dashboard-view');

        //Get Sale Count Start Date
        $sale_setting = DB::table('vendors')
                ->select('sale_setting')
                ->where('id', VendorDetail::getID())
                ->first();

        $SaleSetting = new Carbon($sale_setting->sale_setting);


        //Get Top Packages    
        $topPackages = DB::table($this->table . ' As spd')
                ->leftJoin('vendor_packages', 'spd.package_id', '=', 'vendor_packages.id')
                ->select('vendor_packages.id', 'vendor_packages.name_en', DB::raw("SUM(spd.price) as amount"), DB::raw("count(spd.package_id) as sold"))
                ->whereDate('spd.start_date', '>=', $sale_setting->sale_setting)
                ->where('spd.module_id', 1)
                ->groupBy('spd.package_id')
                ->orderBy('amount', 'desc')
                ->limit(5)
                ->get();

        $topPackages = $topPackages->toArray();

        //Get Top Buyers    
        $topBuyers = DB::table($this->table . ' As spd')
                ->leftJoin($this->memberTable . ' As m', 'spd.member_id', '=', 'm.id')
                ->select('m.id', 'm.name', DB::raw("count('spd.member_id') as subscriber_count"))
                ->whereDate('spd.start_date', '>=', $sale_setting->sale_setting)
                ->where('spd.module_id', 1)
                ->groupBy('spd.member_id')
                ->orderBy('subscriber_count', 'desc')
                ->limit(8)
                ->get();

        //Get Latest Members 
        $Members = DB::table($this->memberTable)
                ->select('name', 'id')
                ->latest()
                ->limit(8)
                ->get();


        //Get total sale and total profit and Admin Commission and subscribers     
        $totalSubscribers = DB::table($this->table)->where('module_id', 1)->whereDate('start_date', '>=', $sale_setting->sale_setting)->count(DB::raw('DISTINCT member_id'));

        //Instructor Amount
        $instructorAmount = DB::table($this->instructorSubscriptionTable)
                ->where('module_id', 1)
                ->whereYear('created_at', '=', date('Y'))
                ->whereMonth('created_at', '=', date('m'))
                ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                ->where('vendor_id', VendorDetail::getID())
                ->sum('price');

        $instructorProfit = DB::table($this->instructorSubscriptionTable)
                ->where('module_id', 1)
                ->whereYear('created_at', '=', date('Y'))
                ->whereMonth('created_at', '=', date('m'))
                ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                ->where('vendor_id', VendorDetail::getID())
                ->sum('profit');

        $instructorAdminCommission = DB::table($this->instructorSubscriptionTable)
                ->where('module_id', 1)
                ->whereYear('created_at', '=', date('Y'))
                ->whereMonth('created_at', '=', date('m'))
                ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                ->where('vendor_id', VendorDetail::getID())
                ->sum('commission');


        $totalAmount = DB::table($this->table)
                ->where('module_id', 1)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('price');

        $totalAmount = $instructorAmount + $totalAmount;


        $totalProfit = DB::table($this->table)
                ->where('module_id', 1)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('profit');

        $totalProfit = $instructorProfit + $totalProfit;

        $totalAdminCommission = DB::table($this->table)
                ->where('module_id', 1)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('commission');

        $totalAdminCommission = $instructorAdminCommission + $totalAdminCommission;


        //Chart   
        $month_days = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));

        for ($i = 0; $i <= $month_days; $i++) {
            $xasis[] = $i;
            //Total Sale            
            $TotalSaleChart = DB::table($this->table)
                    ->where('module_id', 1)
                    ->whereYear('start_date', '=', date('Y'))
                    ->whereMonth('start_date', '=', date('m'))
                    ->whereDay('start_date', '=', $i)
                    ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                    ->first([DB::raw('SUM(price) as amount')]);

            //Instructor Sale
            $TotalInstructorSaleChart = DB::table($this->instructorSubscriptionTable)
                    ->where('module_id', 1)
                    ->whereYear('created_at', '=', date('Y'))
                    ->whereMonth('created_at', '=', date('m'))
                    ->whereDay('created_at', '=', $i)
                    ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                    ->where('vendor_id', VendorDetail::getID())
                    ->first([DB::raw('SUM(price) as amount')]);


            if (!empty($TotalSaleChart->amount)) {
                $TotalSaleChartAmount[] = $TotalSaleChart->amount + $TotalInstructorSaleChart->amount;
            } else {
                $TotalSaleChartAmount[] = 0 + $TotalInstructorSaleChart->amount;
            }

            //Total Profit           
            $TotalProfitChart = DB::table($this->table)
                    ->where('module_id', 1)
                    ->whereYear('start_date', '=', date('Y'))
                    ->whereMonth('start_date', '=', date('m'))
                    ->whereDay('start_date', '=', $i)
                    ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                    ->first([DB::raw('SUM(profit) as amount')]);

            //Instructor Sale
            $TotalInstructorProfitChart = DB::table($this->instructorSubscriptionTable)
                    ->where('module_id', 1)
                    ->whereYear('created_at', '=', date('Y'))
                    ->whereMonth('created_at', '=', date('m'))
                    ->whereDay('created_at', '=', $i)
                    ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                    ->where('vendor_id', VendorDetail::getID())
                    ->first([DB::raw('SUM(profit) as amount')]);

            if (!empty($TotalProfitChart->amount)) {
                $TotalProfitChartAmount[] = $TotalProfitChart->amount + $TotalInstructorProfitChart->amount;
            } else {
                $TotalProfitChartAmount[] = 0 + $TotalInstructorProfitChart->amount;
            }
        }
        $TotalSaleChartAmount = array_except($TotalSaleChartAmount, 0);
        $TotalProfitChartAmount = array_except($TotalProfitChartAmount, 0);
        $xasis = array_except($xasis, 0);

        $chart = Charts::multi('line', 'highcharts')
                ->colors(['#0fbbbd', '#ed164f'])
                ->title('Sales Report')
                ->elementLabel("Amount (KD)")
                ->labels($xasis)
                ->dataset('Total Sales', $TotalSaleChartAmount)
                ->dataset('Total Profit', $TotalProfitChartAmount);

        //Get Latest Payments
        $invoiceList = DB::table($this->invoiceTable . ' As inv')
                ->join($this->memberTable . ' As m', 'inv.member_id', '=', 'm.id')
                ->join('vendor_users As vu', 'vu.id', '=', 'inv.collected_by')
                ->select('inv.receipt_num', 'm.name', 'inv.package_name', 'inv.created_at', 'vu.name AS collected_by', 'inv.cash', 'inv.knet', 'inv.price', 'inv.start_date', 'inv.end_date')
                ->whereDate('inv.start_date', '>=', $sale_setting->sale_setting)
                ->get();

        $PaymentDetail = PaymentDetail::
                select('payment_details.amount', 'payment_details.post_date', 'spd.num_days', 'spd.name_en As package', 'spd.start_date', 'spd.end_date', 'm.name AS user'
                        , DB::raw('(CASE WHEN payment_details.card_type = 1 THEN "KNET" ELSE "Credit Card" END) AS card_type'))
                ->join($this->table . ' As spd', 'spd.payment_id', '=', 'payment_details.id')
                ->leftjoin($this->registeredUserTable . ' As m', 'm.id', '=', 'spd.subscriber_id')
                ->where('payment_details.module_id', 1)
                ->where('spd.vendor_id', VendorDetail::getID())
                ->whereDate('spd.start_date', '>=', $sale_setting->sale_setting)
                ->whereNotNull('spd.payment_id')
                ->orderby('spd.id', 'DESC')
                ->get();


        return view('fitflowVendor.module1.dashboard')
                        ->with('totalAmount', $totalAmount)
                        ->with('totalProfit', $totalProfit)
                        ->with('totalAdminCommission', $totalAdminCommission)
                        ->with('totalSubscribers', $totalSubscribers)
                        ->with('topPackages', $topPackages)
                        ->with('topBuyers', $topBuyers)
                        ->with('Members', $Members)
                        ->with('invoiceList', $invoiceList)
                        ->with('PaymentDetail', $PaymentDetail)
                        ->with('chart', $chart)
                        ->with('ViewAccess', $this->ViewAccess);
    }

}
