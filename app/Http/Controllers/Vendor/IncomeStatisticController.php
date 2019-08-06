<?php

namespace App\Http\Controllers\Vendor;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Charts;
use Carbon\Carbon;
use DateTime;
use DateInterval;
use DatePeriod;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\VendorDetail;

class IncomeStatisticController extends Controller {

    protected $guard = 'vendor';
    protected $configName;
    protected $table;
    protected $memberTable;
    protected $invoiceTable;

    public function __construct() {
        $this->middleware($this->guard);
        $this->middleware('vendorPermission:incomeStatistics');
        $this->configName = config('global.fitflowVendor');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $this->table = VendorDetail::getPrefix() . 'subscribers_package_details';
        $this->orderedTable = 'orders';
        $this->orderTotalTable = 'order_total';
        $this->instructorSubscriptionTable = 'instructor_subscribers_package_details';

        //Get Sale Count Start Date
        $sale_setting = DB::table('vendors')
                ->select('sale_setting')
                ->where('id', VendorDetail::getID())
                ->first();

        $SaleSetting = new Carbon($sale_setting->sale_setting);


        //Get all module
        $module1 = DB::table('modules')
                ->select('id', 'name_en')
                ->where(array('status' => 1, 'id' => 1))
                ->first();

        $module2 = DB::table('modules')
                ->select('id', 'name_en')
                ->where(array('status' => 1, 'id' => 2))
                ->first();

        $module3 = DB::table('modules')
                ->select('id', 'name_en')
                ->where(array('status' => 1, 'id' => 3))
                ->first();

        $module4 = DB::table('modules')
                ->select('id', 'name_en')
                ->where(array('status' => 1, 'id' => 4))
                ->first();

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

        //Overall Sale and Profit
        $totalAmount = DB::table($this->table)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('price');

        //Amount  Module 4
        $M4totalAmount = DB::table($this->orderedTable)
                ->whereYear('created_at', '=', date('Y'))
                ->whereMonth('created_at', '=', date('m'))
                ->where('order_status_id', '!=', 4)
                ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                ->where('vendor_id', VendorDetail::getID())
                ->sum('total');

        $totalAmount = $totalAmount + $M4totalAmount + $instructorAmount;

        $totalAdminCommission = DB::table($this->table)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('commission');

        //Profit Moduel4
        $M4totalAdminCommission = DB::table($this->orderedTable)
                ->whereYear('created_at', '=', date('Y'))
                ->whereMonth('created_at', '=', date('m'))
                ->where('order_status_id', '!=', 4)
                ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                ->where('vendor_id', VendorDetail::getID())
                ->sum('commission');

        $totalAdminCommission = $totalAdminCommission + $M4totalAdminCommission + $instructorAdminCommission;


        $totalProfit = DB::table($this->table)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('profit');

        //Profit Moduel4
        $M4totalProfit = DB::table($this->orderedTable)
                ->whereYear('created_at', '=', date('Y'))
                ->whereMonth('created_at', '=', date('m'))
                ->where('order_status_id', '!=', 4)
                ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                ->where('vendor_id', VendorDetail::getID())
                ->sum('profit');

        $totalProfit = $totalProfit + $M4totalProfit + $instructorProfit;

        //Module 1 Statistics       
        //Get total sale and total profit 
        $module1TotalAmount = DB::table($this->table)
                ->where('module_id', 1)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('price');

        $module1TotalAmount = $instructorAmount + $module1TotalAmount;

        $module1TotalAdminCommission = DB::table($this->table)
                ->where('module_id', 1)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('commission');

        $module1TotalAdminCommission = $instructorAdminCommission + $module1TotalAdminCommission;


        $module1TotalProfit = DB::table($this->table)
                ->where('module_id', 1)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('profit');

        $module1TotalProfit = $instructorProfit + $module1TotalProfit;

        //Module 2 Statistics
        //Get total sale and total profit 
        $module2TotalAmount = DB::table($this->table)
                ->where('module_id', 2)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('price');

        $module2TotalAdminCommission = DB::table($this->table)
                ->where('module_id', 2)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('commission');


        $module2TotalProfit = DB::table($this->table)
                ->where('module_id', 2)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('profit');

        //Module 3 Statistics
        //Get total sale and total profit 
        $module3TotalAmount = DB::table($this->table)
                ->where('module_id', 3)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('price');

        $module3TotalAdminCommission = DB::table($this->table)
                ->where('module_id', 3)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('commission');


        $module3TotalProfit = DB::table($this->table)
                ->where('module_id', 3)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('profit');


        $month_days = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));

        for ($i = 0; $i <= $month_days; $i++) {
            $xasis[] = $i;

            // Module1
            //  Total Sale            
            $module1TotalSaleChart = DB::table($this->table)
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

            if (!empty($module1TotalSaleChart->amount)) {
                $module1TotalSaleChartAmount[] = $module1TotalSaleChart->amount + $TotalInstructorSaleChart->amount;
            } else {
                $module1TotalSaleChartAmount[] = 0 + $TotalInstructorSaleChart->amount;
            }

            //Total Profit           
            $module1TotalProfitChart = DB::table($this->table)
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

            if (!empty($module1TotalProfitChart->amount)) {
                $module1TotalProfitChartAmount[] = $module1TotalProfitChart->amount + $TotalInstructorProfitChart->amount;
            } else {
                $module1TotalProfitChartAmount[] = 0 + $TotalInstructorProfitChart->amount;
            }

            // Module2
            //  Total Sale            
            $module2TotalSaleChart = DB::table($this->table)
                    ->where('module_id', 2)
                    ->whereYear('start_date', '=', date('Y'))
                    ->whereMonth('start_date', '=', date('m'))
                    ->whereDay('start_date', '=', $i)
                    ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                    ->first([DB::raw('SUM(price) as amount')]);

            if (!empty($module2TotalSaleChart->amount)) {
                $module2TotalSaleChartAmount[] = $module2TotalSaleChart->amount;
            } else {
                $module2TotalSaleChartAmount[] = 0;
            }

            //Total Profit           
            $module2TotalProfitChart = DB::table($this->table)
                    ->where('module_id', 2)
                    ->whereYear('start_date', '=', date('Y'))
                    ->whereMonth('start_date', '=', date('m'))
                    ->whereDay('start_date', '=', $i)
                    ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                    ->first([DB::raw('SUM(profit) as amount')]);

            if (!empty($module2TotalProfitChart->amount)) {
                $module2TotalProfitChartAmount[] = $module2TotalProfitChart->amount;
            } else {
                $module2TotalProfitChartAmount[] = 0;
            }
            //Module 4
            //Total Sale            
            $module4TotalSaleChart = DB::table($this->orderedTable)
                    ->whereYear('created_at', '=', date('Y'))
                    ->whereMonth('created_at', '=', date('m'))
                    ->whereDay('created_at', '=', $i)
                    ->where('order_status_id', '!=', 4)
                    ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                    ->where('vendor_id', VendorDetail::getID())
                    ->first([DB::raw('SUM(total) as amount')]);

            if (!empty($module4TotalSaleChart->amount)) {
                $module4TotalSaleChartAmount[] = $module4TotalSaleChart->amount;
            } else {
                $module4TotalSaleChartAmount[] = 0;
            }

            //Vendor           
            $module4TotalProfitChart = DB::table($this->orderedTable)
                    ->whereYear('created_at', '=', date('Y'))
                    ->whereMonth('created_at', '=', date('m'))
                    ->whereDay('created_at', '=', $i)
                    ->where('order_status_id', '!=', 4)
                    ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                    ->where('vendor_id', VendorDetail::getID())
                    ->first([DB::raw('SUM(profit) as amount')]);

            if (!empty($module4TotalProfitChart->amount)) {
                $module4TotalProfitChartAmount[] = $module4TotalProfitChart->amount;
            } else {
                $module4TotalProfitChartAmount[] = 0;
            }
        }
        // Module1
        $module1TotalSaleChartAmount = array_except($module1TotalSaleChartAmount, 0);
        $module1TotalProfitChartAmount = array_except($module1TotalProfitChartAmount, 0);

        // Module2
        $module2TotalSaleChartAmount = array_except($module2TotalSaleChartAmount, 0);
        $module2TotalProfitChartAmount = array_except($module2TotalProfitChartAmount, 0);

        //Module4
        $module4TotalSaleChartAmount = array_except($module4TotalSaleChartAmount, 0);
        $module4TotalProfitChartAmount = array_except($module4TotalProfitChartAmount, 0);


        $xasis = array_except($xasis, 0);

        $chart = Charts::multi('line', 'highcharts')
                ->colors(['#0fbbbd', '#e78e24'])
                ->title('Sales Report')
                ->elementLabel("Amount (KD)")
                ->labels($xasis)
                ->dataset('Total Sales', $module1TotalSaleChartAmount)
                ->dataset('Total Profitt', $module1TotalProfitChartAmount);

        $chart2 = Charts::multi('line', 'highcharts')
                ->colors(['#0fbbbd', '#e78e24'])
                ->title('Sales Report')
                ->elementLabel("Amount (KD)")
                ->labels($xasis)
                ->dataset('Total Sales', $module2TotalSaleChartAmount)
                ->dataset('Total Profitt', $module2TotalProfitChartAmount);


        //Module4 chart
        $chart4 = Charts::multi('line', 'highcharts')
                ->colors(['#0fbbbd', '#ed164f'])
                ->title('Sales Report')
                ->elementLabel("Amount (KD)")
                ->labels($xasis)
                ->dataset('Total Sales', $module4TotalSaleChartAmount)
                ->dataset('Total Vendors Amount', $module4TotalProfitChartAmount);

        return view('fitflowVendor.incomeStatistics.index')
                        ->with('module1', $module1)
                        ->with('module2', $module2)
                        ->with('module3', $module3)
                        ->with('module4', $module4)
                        ->with('totalAmount', $totalAmount)
                        ->with('totalProfit', $totalProfit)
                        ->with('totalAdminCommission', $totalAdminCommission)
                        ->with('module1TotalAmount', $module1TotalAmount)
                        ->with('module1TotalAdminCommission', $module1TotalAdminCommission)
                        ->with('module1TotalProfit', $module1TotalProfit)
                        ->with('module2TotalAmount', $module2TotalAmount)
                        ->with('module2TotalAdminCommission', $module2TotalAdminCommission)
                        ->with('module2TotalProfit', $module2TotalProfit)
                        ->with('module4TotalAmount', $M4totalAmount)
                        ->with('module4TotalAdminCommission', $M4totalAdminCommission)
                        ->with('module4TotalProfit', $M4totalProfit)
                        ->with('chart', $chart)
                        ->with('chart2', $chart2)
                        ->with('chart4', $chart4);
    }

    public function ajaxchart(Request $request) {

        $this->table = VendorDetail::getPrefix() . 'subscribers_package_details';
        $this->memberTable = VendorDetail::getPrefix() . 'members';
        $this->invoiceTable = VendorDetail::getPrefix() . 'member_invoices';
        $this->orderedTable = 'orders';
        $this->instructorSubscriptionTable = 'instructor_subscribers_package_details';

        //Get Sale Count Start Date
        $sale_setting = DB::table('vendors')
                ->select('sale_setting')
                ->where('id', VendorDetail::getID())
                ->first();

        $SaleSetting = new Carbon($sale_setting->sale_setting);

        //Ajax request
        if (request()->ajax()) {
            if ($request->report_type == 1 && $request->has('daily_date')) {  // 1:Daily report 
                $str_array = explode('/', $request->daily_date);
                $Year = $str_array[1];
                $Month = $str_array[0];

                //Instructor Amount
                $instructorAmount = DB::table($this->instructorSubscriptionTable)
                        ->where('module_id', 1)
                        ->whereYear('created_at', '=', $Year)
                        ->whereMonth('created_at', '=', $Month)
                        ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                        ->where('vendor_id', VendorDetail::getID())
                        ->sum('price');

                $instructorProfit = DB::table($this->instructorSubscriptionTable)
                        ->where('module_id', 1)
                        ->whereYear('created_at', '=', $Year)
                        ->whereMonth('created_at', '=', $Month)
                        ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                        ->where('vendor_id', VendorDetail::getID())
                        ->sum('profit');

                $instructorAdminCommission = DB::table($this->instructorSubscriptionTable)
                        ->where('module_id', 1)
                        ->whereYear('created_at', '=', $Year)
                        ->whereMonth('created_at', '=', $Month)
                        ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                        ->where('vendor_id', VendorDetail::getID())
                        ->sum('commission');


                //Overall Sale and Profit
                $totalAmount = DB::table($this->table)
                        ->whereYear('start_date', '=', $Year)
                        ->whereMonth('start_date', '=', $Month)
                        ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                        ->sum('price');

                //Amount Modul4
                $M4totalAmount = DB::table($this->orderedTable)
                        ->whereYear('created_at', '=', $Year)
                        ->whereMonth('created_at', '=', $Month)
                        ->where('order_status_id', '!=', 4)
                        ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                        ->where('vendor_id', VendorDetail::getID())
                        ->sum('total');

                $totalAmount = $totalAmount + $M4totalAmount + $instructorAmount;

                $totalAdminCommission = DB::table($this->table)
                        ->whereYear('start_date', '=', $Year)
                        ->whereMonth('start_date', '=', $Month)
                        ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                        ->sum('commission');

                //Commission Module4
                $M4totalAdminCommission = DB::table($this->orderedTable)
                        ->whereYear('created_at', '=', $Year)
                        ->whereMonth('created_at', '=', $Month)
                        ->where('order_status_id', '!=', 4)
                        ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                        ->where('vendor_id', VendorDetail::getID())
                        ->sum('commission');

                $totalAdminCommission = $totalAdminCommission + $M4totalAdminCommission + $instructorAdminCommission;

                $totalProfit = DB::table($this->table)
                        ->whereYear('start_date', '=', $Year)
                        ->whereMonth('start_date', '=', $Month)
                        ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                        ->sum('profit');

                //Profit Module4
                $M4totalProfit = DB::table($this->orderedTable)
                        ->whereYear('created_at', '=', $Year)
                        ->whereMonth('created_at', '=', $Month)
                        ->where('order_status_id', '!=', 4)
                        ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                        ->where('vendor_id', VendorDetail::getID())
                        ->sum('profit');

                $totalProfit = $totalProfit + $M4totalProfit + $instructorProfit;

                //Module 1 Statistics
                //Get total sale and total profit 
                $module1TotalAmount = DB::table($this->table)
                        ->where('module_id', 1)
                        ->whereYear('start_date', '=', $Year)
                        ->whereMonth('start_date', '=', $Month)
                        ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                        ->sum('price');

                $module1TotalAmount = $instructorAmount + $module1TotalAmount;

                $module1TotalAdminCommission = DB::table($this->table)
                        ->where('module_id', 1)
                        ->whereYear('start_date', '=', $Year)
                        ->whereMonth('start_date', '=', $Month)
                        ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                        ->sum('commission');

                $module1TotalAdminCommission = $instructorAdminCommission + $module1TotalAdminCommission;


                $module1TotalProfit = DB::table($this->table)
                        ->where('module_id', 1)
                        ->whereYear('start_date', '=', $Year)
                        ->whereMonth('start_date', '=', $Month)
                        ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                        ->sum('profit');

                $module1TotalProfit = $instructorProfit + $module1TotalProfit;

                //Module 2 Statistics
                //Get total sale and total profit 
                $module2TotalAmount = DB::table($this->table)
                        ->where('module_id', 2)
                        ->whereYear('start_date', '=', $Year)
                        ->whereMonth('start_date', '=', $Month)
                        ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                        ->sum('price');

                $module2TotalAdminCommission = DB::table($this->table)
                        ->where('module_id', 2)
                        ->whereYear('start_date', '=', $Year)
                        ->whereMonth('start_date', '=', $Month)
                        ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                        ->sum('commission');


                $module2TotalProfit = DB::table($this->table)
                        ->where('module_id', 2)
                        ->whereYear('start_date', '=', $Year)
                        ->whereMonth('start_date', '=', $Month)
                        ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                        ->sum('profit');



                //Get all module
                $module1 = DB::table('modules')
                        ->select('id', 'name_en')
                        ->where(array('status' => 1, 'id' => 1))
                        ->first();

                $module2 = DB::table('modules')
                        ->select('id', 'name_en')
                        ->where(array('status' => 1, 'id' => 2))
                        ->first();

                $module3 = DB::table('modules')
                        ->select('id', 'name_en')
                        ->where(array('status' => 1, 'id' => 3))
                        ->first();

                $module4 = DB::table('modules')
                        ->select('id', 'name_en')
                        ->where(array('status' => 1, 'id' => 4))
                        ->first();


                //Chart 
                $month_days = cal_days_in_month(CAL_GREGORIAN, $Month, $Year);
                for ($i = 0; $i <= $month_days; $i++) {
                    $xasis[] = $i;

                    //Module1
                    //Total Sale            
                    $module1TotalSaleChart = DB::table('subscribers_package_details')
                            ->where('module_id', 1)
                            ->whereYear('start_date', '=', $Year)
                            ->whereMonth('start_date', '=', $Month)
                            ->whereDay('start_date', '=', $i)
                            ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                            ->first([DB::raw('SUM(price) as amount')]);

                    //Instructor Sale
                    $TotalInstructorSaleChart = DB::table($this->instructorSubscriptionTable)
                            ->where('module_id', 1)
                            ->whereYear('created_at', '=', $Year)
                            ->whereMonth('created_at', '=', $Month)
                            ->whereDay('created_at', '=', $i)
                            ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                            ->where('vendor_id', VendorDetail::getID())
                            ->first([DB::raw('SUM(price) as amount')]);

                    if (!empty($module1TotalSaleChart->amount)) {
                        $module1TotalSaleChartAmount[] = $module1TotalSaleChart->amount + $TotalInstructorSaleChart->amount;
                    } else {
                        $module1TotalSaleChartAmount[] = 0 + $TotalInstructorSaleChart->amount;
                    }

                    //Total Profit           
                    $module1TotalProfitChart = DB::table($this->table)
                            ->where('module_id', 1)
                            ->whereYear('start_date', '=', $Year)
                            ->whereMonth('start_date', '=', $Month)
                            ->whereDay('start_date', '=', $i)
                            ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                            ->first([DB::raw('SUM(profit) as amount')]);

                    //Instructor Sale
                    $TotalInstructorProfitChart = DB::table($this->instructorSubscriptionTable)
                            ->where('module_id', 1)
                            ->whereYear('created_at', '=', $Year)
                            ->whereMonth('created_at', '=', $Month)
                            ->whereDay('created_at', '=', $i)
                            ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                            ->where('vendor_id', VendorDetail::getID())
                            ->first([DB::raw('SUM(profit) as amount')]);

                    if (!empty($module1TotalProfitChart->amount)) {
                        $module1TotalProfitChartAmount[] = $module1TotalProfitChart->amount + $TotalInstructorProfitChart->amount;
                    } else {
                        $module1TotalProfitChartAmount[] = 0 + $TotalInstructorProfitChart->amount;
                    }

                    //Module2
                    //Total Sale            
                    $module2TotalSaleChart = DB::table('subscribers_package_details')
                            ->where('module_id', 2)
                            ->whereYear('start_date', '=', $Year)
                            ->whereMonth('start_date', '=', $Month)
                            ->whereDay('start_date', '=', $i)
                            ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                            ->first([DB::raw('SUM(price) as amount')]);

                    if (!empty($module2TotalSaleChart->amount)) {
                        $module2TotalSaleChartAmount[] = $module2TotalSaleChart->amount;
                    } else {
                        $module2TotalSaleChartAmount[] = 0;
                    }

                    //Total Profit           
                    $module2TotalProfitChart = DB::table($this->table)
                            ->where('module_id', 2)
                            ->whereYear('start_date', '=', $Year)
                            ->whereMonth('start_date', '=', $Month)
                            ->whereDay('start_date', '=', $i)
                            ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                            ->first([DB::raw('SUM(profit) as amount')]);

                    if (!empty($module2TotalProfitChart->amount)) {
                        $module2TotalProfitChartAmount[] = $module2TotalProfitChart->amount;
                    } else {
                        $module2TotalProfitChartAmount[] = 0;
                    }

                    //Module4
                    //Total Sale            
                    $module4TotalSaleChart = DB::table($this->orderedTable)
                            ->whereYear('created_at', '=', $Year)
                            ->whereMonth('created_at', '=', $Month)
                            ->whereDay('created_at', '=', $i)
                            ->where('order_status_id', '!=', 4)
                            ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                            ->where('vendor_id', VendorDetail::getID())
                            ->first([DB::raw('SUM(total) as amount')]);

                    if (!empty($module4TotalSaleChart->amount)) {
                        $module4TotalSaleChartAmount[] = $module4TotalSaleChart->amount;
                    } else {
                        $module4TotalSaleChartAmount[] = 0;
                    }

                    //Total Profit           
                    $module4TotalProfitChart = DB::table($this->orderedTable)
                            ->whereYear('created_at', '=', $Year)
                            ->whereMonth('created_at', '=', $Month)
                            ->whereDay('created_at', '=', $i)
                            ->where('order_status_id', '!=', 4)
                            ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                            ->where('vendor_id', VendorDetail::getID())
                            ->first([DB::raw('SUM(profit) as amount')]);

                    if (!empty($module4TotalProfitChart->amount)) {
                        $module4TotalProfitChartAmount[] = $module4TotalProfitChart->amount;
                    } else {
                        $module4TotalProfitChartAmount[] = 0;
                    }
                }

                //Module1
                $module1TotalSaleChartAmount = array_except($module1TotalSaleChartAmount, 0);
                $module1TotalProfitChartAmount = array_except($module1TotalProfitChartAmount, 0);

                //Module2
                $module2TotalSaleChartAmount = array_except($module2TotalSaleChartAmount, 0);
                $module2TotalProfitChartAmount = array_except($module2TotalProfitChartAmount, 0);

                //Module4
                $module4TotalSaleChartAmount = array_except($module4TotalSaleChartAmount, 0);
                $module4TotalProfitChartAmount = array_except($module4TotalProfitChartAmount, 0);

                $xasis = array_except($xasis, 0);

                $chart = Charts::multi('line', 'highcharts')
                        ->colors(['#0fbbbd', '#e78e24'])
                        ->title('Sales Report')
                        ->elementLabel("Amount (KD)")
                        ->labels($xasis)
                        ->dataset('Total Sales', $module1TotalSaleChartAmount)
                        ->dataset('Total Profitt', $module1TotalProfitChartAmount);

                $chart2 = Charts::multi('line', 'highcharts')
                        ->colors(['#0fbbbd', '#e78e24'])
                        ->title('Sales Report')
                        ->elementLabel("Amount (KD)")
                        ->labels($xasis)
                        ->dataset('Total Sales', $module2TotalSaleChartAmount)
                        ->dataset('Total Profitt', $module2TotalProfitChartAmount);

                $chart4 = Charts::multi('line', 'highcharts')
                        ->colors(['#0fbbbd', '#e78e24'])
                        ->title('Sales Report')
                        ->elementLabel("Amount (KD)")
                        ->labels($xasis)
                        ->dataset('Total Sales', $module4TotalSaleChartAmount)
                        ->dataset('Total Profitt', $module4TotalProfitChartAmount);


                $returnHTML = view('fitflowVendor.incomeStatistics.ajaxchart')
                        ->with('module1', $module1)
                        ->with('module2', $module2)
                        ->with('module3', $module3)
                        ->with('module4', $module4)
                        ->with('totalAmount', $totalAmount)
                        ->with('totalProfit', $totalProfit)
                        ->with('totalAdminCommission', $totalAdminCommission)
                        ->with('module1TotalAmount', $module1TotalAmount)
                        ->with('module1TotalAdminCommission', $module1TotalAdminCommission)
                        ->with('module1TotalProfit', $module1TotalProfit)
                        ->with('module2TotalAmount', $module2TotalAmount)
                        ->with('module2TotalAdminCommission', $module2TotalAdminCommission)
                        ->with('module2TotalProfit', $module2TotalProfit)
                        ->with('module4TotalAmount', $M4totalAmount)
                        ->with('module4TotalAdminCommission', $M4totalAdminCommission)
                        ->with('module4TotalProfit', $M4totalProfit)
                        ->with('chart', $chart)
                        ->with('chart2', $chart2)
                        ->with('chart4', $chart4)
                        ->render();

                return response()->json(array('success' => true, 'html' => $returnHTML));
            } elseif ($request->report_type == 2 && $request->has('startdate') && $request->has('enddate')) {  // 2: Monthly Report
                $str_startdate_array = explode('/', $request->startdate);
                $str_enddate_array = explode('/', $request->enddate);

                //check month difference
                $startdate = DateTime::createFromFormat('m/Y', $request->startdate);
                $enddate = DateTime::createFromFormat('m/Y', $request->enddate);
                $diff = $enddate->diff($startdate);
                $diff_in_months = $diff->format('%m');
                $diff_in_year = $diff->format('%y');

                $interval = DateInterval::createFromDateString('1 month');
                $period = new DatePeriod($startdate, $interval, $enddate);

                foreach ($period as $dt) {
                    $months[$dt->format("m")] = $dt->format("Y");
                }
                $months[$str_enddate_array[0]] = $str_enddate_array[1];

                if ($diff_in_months <= 6 && $diff_in_year == 0) {  // 6 Months Report only can allow 
                    //Overall Sale and Profit
                    $month_days = cal_days_in_month(CAL_GREGORIAN, $str_enddate_array[0], $str_enddate_array[1]);
                    $startdate = $str_startdate_array[1] . '-' . $str_startdate_array[0] . '-01';
                    $enddate = $str_enddate_array[1] . '-' . $str_enddate_array[0] . '-' . $month_days;
                    $exp = new Carbon($enddate);
                    $exp->addDays(1);
                    $enddate = $exp->format('Y-m-d');

                    //Instructor Amount
                    $instructorAmount = DB::table($this->instructorSubscriptionTable)
                            ->where('module_id', 1)
                            ->whereBetween('created_at', [$startdate, $enddate])
                            ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                            ->where('vendor_id', VendorDetail::getID())
                            ->sum('price');

                    $instructorProfit = DB::table($this->instructorSubscriptionTable)
                            ->where('module_id', 1)
                            ->whereBetween('created_at', [$startdate, $enddate])
                            ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                            ->where('vendor_id', VendorDetail::getID())
                            ->sum('profit');

                    $instructorAdminCommission = DB::table($this->instructorSubscriptionTable)
                            ->where('module_id', 1)
                            ->whereBetween('created_at', [$startdate, $enddate])
                            ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                            ->where('vendor_id', VendorDetail::getID())
                            ->sum('commission');


                    $totalAmount = DB::table($this->table)
                            ->whereBetween('start_date', [$startdate, $enddate])
                            ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                            ->sum('price');

                    //Amount Module4
                    $M4totalAmount = DB::table($this->orderedTable)
                            ->whereBetween('created_at', [$startdate, $enddate])
                            ->where('order_status_id', '!=', 4)
                            ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                            ->where('vendor_id', VendorDetail::getID())
                            ->sum('total');

                    $totalAmount = $totalAmount + $M4totalAmount + $instructorAmount;

                    $totalAdminCommission = DB::table($this->table)
                            ->whereBetween('start_date', [$startdate, $enddate])
                            ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                            ->sum('commission');

                    //Commission Module4
                    $M4totalAdminCommission = DB::table($this->orderedTable)
                            ->whereBetween('created_at', [$startdate, $enddate])
                            ->where('order_status_id', '!=', 4)
                            ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                            ->where('vendor_id', VendorDetail::getID())
                            ->sum('commission');

                    $totalAdminCommission = $totalAdminCommission + $M4totalAdminCommission + $instructorAdminCommission;

                    $totalProfit = DB::table($this->table)
                            ->whereBetween('start_date', [$startdate, $enddate])
                            ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                            ->sum('profit');

                    //Profit Module4
                    $M4totalProfit = DB::table($this->orderedTable)
                            ->whereBetween('created_at', [$startdate, $enddate])
                            ->where('order_status_id', '!=', 4)
                            ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                            ->where('vendor_id', VendorDetail::getID())
                            ->sum('profit');

                    $totalProfit = $totalProfit + $M4totalProfit + $instructorProfit;

                    //Module 1 Statistics
                    //Get total sale and total profit 
                    $module1TotalAmount = DB::table($this->table)
                            ->where('module_id', 1)
                            ->whereBetween('start_date', [$startdate, $enddate])
                            ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                            ->sum('price');

                    $module1TotalAmount = $instructorAmount + $module1TotalAmount;

                    $module1TotalAdminCommission = DB::table($this->table)
                            ->where('module_id', 1)
                            ->whereBetween('start_date', [$startdate, $enddate])
                            ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                            ->sum('commission');

                    $module1TotalAdminCommission = $instructorAdminCommission + $module1TotalAdminCommission;


                    $module1TotalProfit = DB::table($this->table)
                            ->where('module_id', 1)
                            ->whereBetween('start_date', [$startdate, $enddate])
                            ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                            ->sum('profit');

                    $module1TotalProfit = $instructorProfit + $module1TotalProfit;

                    //Module 2 Statistics
                    //Get total sale and total profit 
                    $module2TotalAmount = DB::table($this->table)
                            ->where('module_id', 2)
                            ->whereBetween('start_date', [$startdate, $enddate])
                            ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                            ->sum('price');

                    $module2TotalAdminCommission = DB::table($this->table)
                            ->where('module_id', 2)
                            ->whereBetween('start_date', [$startdate, $enddate])
                            ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                            ->sum('commission');


                    $module2TotalProfit = DB::table($this->table)
                            ->where('module_id', 2)
                            ->whereBetween('start_date', [$startdate, $enddate])
                            ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                            ->sum('profit');


                    //Get all module
                    $module1 = DB::table('modules')
                            ->select('id', 'name_en')
                            ->where(array('status' => 1, 'id' => 1))
                            ->first();

                    $module2 = DB::table('modules')
                            ->select('id', 'name_en')
                            ->where(array('status' => 1, 'id' => 2))
                            ->first();

                    $module3 = DB::table('modules')
                            ->select('id', 'name_en')
                            ->where(array('status' => 1, 'id' => 3))
                            ->first();

                    $module4 = DB::table('modules')
                            ->select('id', 'name_en')
                            ->where(array('status' => 1, 'id' => 4))
                            ->first();


                    //Chart 
                    foreach ($months as $key => $val) {
                        $xasis[] = $key . '-' . $val;

                        //Module1
                        //Total Sale            
                        $module1TotalSaleChart = DB::table('subscribers_package_details')
                                ->where('module_id', 1)
                                ->whereYear('start_date', '=', $val)
                                ->whereMonth('start_date', '=', $key)
                                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                                ->first([DB::raw('SUM(price) as amount')]);

                        //Instructor Sale
                        $TotalInstructorSaleChart = DB::table($this->instructorSubscriptionTable)
                                ->where('module_id', 1)
                                ->whereYear('created_at', '=', $val)
                                ->whereMonth('created_at', '=', $key)
                                ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                                ->where('vendor_id', VendorDetail::getID())
                                ->first([DB::raw('SUM(price) as amount')]);

                        if (!empty($module1TotalSaleChart->amount)) {
                            $module1TotalSaleChartAmount[] = $module1TotalSaleChart->amount + $TotalInstructorSaleChart->amount;
                        } else {
                            $module1TotalSaleChartAmount[] = 0 + $TotalInstructorSaleChart->amount;
                        }

                        //Total Profit           
                        $module1TotalProfitChart = DB::table($this->table)
                                ->where('module_id', 1)
                                ->whereYear('start_date', '=', $val)
                                ->whereMonth('start_date', '=', $key)
                                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                                ->first([DB::raw('SUM(profit) as amount')]);

                        //Instructor Sale
                        $TotalInstructorProfitChart = DB::table($this->instructorSubscriptionTable)
                                ->where('module_id', 1)
                                ->whereYear('created_at', '=', $val)
                                ->whereMonth('created_at', '=', $key)
                                ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                                ->where('vendor_id', VendorDetail::getID())
                                ->first([DB::raw('SUM(profit) as amount')]);

                        if (!empty($module1TotalProfitChart->amount)) {
                            $module1TotalProfitChartAmount[] = $module1TotalProfitChart->amount + $TotalInstructorProfitChart->amount;
                        } else {
                            $module1TotalProfitChartAmount[] = 0 + $TotalInstructorProfitChart->amount;
                        }

                        //Module2
                        //Total Sale            
                        $module2TotalSaleChart = DB::table('subscribers_package_details')
                                ->where('module_id', 2)
                                ->whereYear('start_date', '=', $val)
                                ->whereMonth('start_date', '=', $key)
                                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                                ->first([DB::raw('SUM(price) as amount')]);

                        if (!empty($module2TotalSaleChart->amount)) {
                            $module2TotalSaleChartAmount[] = $module2TotalSaleChart->amount;
                        } else {
                            $module2TotalSaleChartAmount[] = 0;
                        }

                        //Total Profit           
                        $module2TotalProfitChart = DB::table($this->table)
                                ->where('module_id', 2)
                                ->whereYear('start_date', '=', $val)
                                ->whereMonth('start_date', '=', $key)
                                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                                ->first([DB::raw('SUM(profit) as amount')]);

                        if (!empty($module2TotalProfitChart->amount)) {
                            $module2TotalProfitChartAmount[] = $module2TotalProfitChart->amount;
                        } else {
                            $module2TotalProfitChartAmount[] = 0;
                        }
                        //Module4
                        //Total Sale            
                        $module4TotalSaleChart = DB::table($this->orderedTable)
                                ->whereYear('created_at', '=', $val)
                                ->whereMonth('created_at', '=', $key)
                                ->where('order_status_id', '!=', 4)
                                ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                                ->where('vendor_id', VendorDetail::getID())
                                ->first([DB::raw('SUM(total) as amount')]);

                        if (!empty($module4TotalSaleChart->amount)) {
                            $module4TotalSaleChartAmount[] = $module4TotalSaleChart->amount;
                        } else {
                            $module4TotalSaleChartAmount[] = 0;
                        }

                        //Total Profit           
                        $module4TotalProfitChart = DB::table($this->orderedTable)
                                ->whereYear('created_at', '=', $val)
                                ->whereMonth('created_at', '=', $key)
                                ->where('order_status_id', '!=', 4)
                                ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                                ->where('vendor_id', VendorDetail::getID())
                                ->first([DB::raw('SUM(profit) as amount')]);

                        if (!empty($module4TotalProfitChart->amount)) {
                            $module4TotalProfitChartAmount[] = $module4TotalProfitChart->amount;
                        } else {
                            $module4TotalProfitChartAmount[] = 0;
                        }
                    }

                    $chart = Charts::multi('line', 'highcharts')
                            ->colors(['#0fbbbd', '#e78e24'])
                            ->title('Sales Report')
                            ->elementLabel("Amount (KD)")
                            ->labels($xasis)
                            ->dataset('Total Sales', $module1TotalSaleChartAmount)
                            ->dataset('Total Profitt', $module1TotalProfitChartAmount);

                    $chart2 = Charts::multi('line', 'highcharts')
                            ->colors(['#0fbbbd', '#e78e24'])
                            ->title('Sales Report')
                            ->elementLabel("Amount (KD)")
                            ->labels($xasis)
                            ->dataset('Total Sales', $module2TotalSaleChartAmount)
                            ->dataset('Total Profitt', $module2TotalProfitChartAmount);

                    $chart4 = Charts::multi('line', 'highcharts')
                            ->colors(['#0fbbbd', '#e78e24'])
                            ->title('Sales Report')
                            ->elementLabel("Amount (KD)")
                            ->labels($xasis)
                            ->dataset('Total Sales', $module4TotalSaleChartAmount)
                            ->dataset('Total Profitt', $module4TotalProfitChartAmount);

                    $returnHTML = view('fitflowVendor.incomeStatistics.ajaxchart')
                            ->with('module1', $module1)
                            ->with('module2', $module2)
                            ->with('module3', $module3)
                            ->with('module4', $module4)
                            ->with('totalAmount', $totalAmount)
                            ->with('totalProfit', $totalProfit)
                            ->with('totalAdminCommission', $totalAdminCommission)
                            ->with('module1TotalAmount', $module1TotalAmount)
                            ->with('module1TotalAdminCommission', $module1TotalAdminCommission)
                            ->with('module1TotalProfit', $module1TotalProfit)
                            ->with('module2TotalAmount', $module2TotalAmount)
                            ->with('module2TotalAdminCommission', $module2TotalAdminCommission)
                            ->with('module2TotalProfit', $module2TotalProfit)
                            ->with('module4TotalAmount', $M4totalAmount)
                            ->with('module4TotalAdminCommission', $M4totalAdminCommission)
                            ->with('module4TotalProfit', $M4totalProfit)
                            ->with('chart', $chart)
                            ->with('chart2', $chart2)
                            ->with('chart4', $chart4)
                            ->render();

                    return response()->json(array('success' => true, 'html' => $returnHTML));
                } else {
                    return response()->json(['response' => config('global.errorReports')]);
                }
            } else {
                return response()->json(['response' => config('global.fieldError')]);
            }
        }
    }

}
