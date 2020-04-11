<?php

namespace App\Http\Controllers\Admin;

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
use App\Models\Admin\Transaction;

class IncomeStatisticController extends Controller {

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:incomeStatistics');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $this->orderedTable = 'orders';
        $this->orderTotalTable = 'order_total';
        $this->instructorSubscriptionTable = 'instructor_subscribers_package_details';

        //Instructor Amount
        $instructorAmount = DB::table($this->instructorSubscriptionTable)
                ->where('module_id', 1)
                ->whereYear('created_at', '=', date('Y'))
                ->whereMonth('created_at', '=', date('m'))
                ->sum('price');

        $instructorProfit = DB::table($this->instructorSubscriptionTable)
                ->where('module_id', 1)
                ->whereYear('created_at', '=', date('Y'))
                ->whereMonth('created_at', '=', date('m'))
                ->sum('profit');

        $instructorAdminCommission = DB::table($this->instructorSubscriptionTable)
                ->where('module_id', 1)
                ->whereYear('created_at', '=', date('Y'))
                ->whereMonth('created_at', '=', date('m'))
                ->sum('commission');


        //Get total sale and total profit 
        $totalAmount = DB::table('subscribers_package_details')
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->sum('price');

        //Amount  Module 4
        $M4totalAmount = DB::table($this->orderedTable)
                ->whereYear('created_at', '=', date('Y'))
                ->whereMonth('created_at', '=', date('m'))
                ->where('order_status_id', '!=', 4)
                ->sum('total');

        $totalAmount = $totalAmount + $M4totalAmount + $instructorAmount;

        $totalProfit = DB::table('subscribers_package_details')
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->sum('commission');

        //Profit Moduel4
        $M4totalProfit = DB::table($this->orderedTable)
                ->whereYear('created_at', '=', date('Y'))
                ->whereMonth('created_at', '=', date('m'))
                ->where('order_status_id', '!=', 4)
                ->sum('commission');

        $totalProfit = $totalProfit + $M4totalProfit + $instructorAdminCommission;

        $vendorAmount = DB::table('subscribers_package_details')
                ->whereNotNull('vendor_id')
                ->WhereNull('trainer_id')
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->sum('profit');

        //Vendor Amount Module4
        $M4totalVendorProfit = DB::table($this->orderedTable)
                ->whereYear('created_at', '=', date('Y'))
                ->whereMonth('created_at', '=', date('m'))
                ->where('order_status_id', '!=', 4)
                ->sum('profit');

        $vendorAmount = $vendorAmount + $M4totalVendorProfit + $instructorProfit;

        $totalDeliverycharge = DB::table($this->orderTotalTable)
                ->leftJoin($this->orderedTable, 'orders.id', '=', 'order_total.order_id')
                ->whereYear('orders.created_at', '=', date('Y'))
                ->whereMonth('orders.created_at', '=', date('m'))
                ->where('orders.order_status_id', '!=', 4)
                ->sum('delivery_charge');

        $totalCoupon = DB::table($this->orderTotalTable)
                ->leftJoin($this->orderedTable, 'orders.id', '=', 'order_total.order_id')
                ->whereYear('orders.created_at', '=', date('Y'))
                ->whereMonth('orders.created_at', '=', date('m'))
                ->where('orders.order_status_id', '!=', 4)
                ->sum('coupon_discount');

        $trainerAmount = DB::table('subscribers_package_details')
                ->whereNotNull('trainer_id')
                ->WhereNull('vendor_id')
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
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


        //Module 1 Statistics
        //Get total sale and total profit 
        $module1TotalAmount = DB::table('subscribers_package_details')
                ->where('module_id', 1)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->sum('price');

        $module1TotalAmount = $instructorAmount + $module1TotalAmount;

        $module1TotalProfit = DB::table('subscribers_package_details')
                ->where('module_id', 1)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->sum('commission');

        $module1TotalProfit = $instructorAdminCommission + $module1TotalProfit;


        //Vendor profit
        $module1TotalVendorAmount = DB::table('subscribers_package_details')
                ->where('module_id', 1)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereNotNull('vendor_id')
                ->WhereNull('trainer_id')
                ->sum('profit');
        
        $module1TotalVendorAmount = $instructorProfit + $module1TotalVendorAmount;

        //Trainer profit
        $module1TotalTrainerAmount = DB::table('subscribers_package_details')
                ->where('module_id', 1)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereNotNull('trainer_id')
                ->WhereNull('vendor_id')
                ->sum('profit');

        //Module 2 Statistics
        //Get total sale and total profit 
        $module2TotalAmount = DB::table('subscribers_package_details')
                ->where('module_id', 2)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->sum('price');

        $module2TotalProfit = DB::table('subscribers_package_details')
                ->where('module_id', 2)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->sum('commission');


        //Vendor profit
        $module2TotalVendorAmount = DB::table('subscribers_package_details')
                ->where('module_id', 2)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereNotNull('vendor_id')
                ->WhereNull('trainer_id')
                ->sum('profit');

        //Module 3 Statistics
        //Get total sale
        $module3TotalAmount = DB::table('subscribers_package_details')
                ->where('module_id', 3)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->sum('price');

        //Chart   
        $month_days = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));

        for ($i = 0; $i <= $month_days; $i++) {
            $xasis[] = $i;

            //Module1
            //Total Sale            
            $module1TotalSaleChart = DB::table('subscribers_package_details')
                    ->where('module_id', 1)
                    ->whereYear('start_date', '=', date('Y'))
                    ->whereMonth('start_date', '=', date('m'))
                    ->whereDay('start_date', '=', $i)
                    ->first([DB::raw('SUM(price) as amount')]);
            
             //Instructor Sale
            $TotalInstructorSaleChart = DB::table($this->instructorSubscriptionTable)
                    ->where('module_id', 1)
                    ->whereYear('created_at', '=', date('Y'))
                    ->whereMonth('created_at', '=', date('m'))
                    ->whereDay('created_at', '=', $i)
                    ->first([DB::raw('SUM(price) as amount')]);

            if (!empty($module1TotalSaleChart->amount)) {
                $module1TotalSaleChartAmount[] = $module1TotalSaleChart->amount + $TotalInstructorSaleChart->amount;
            } else {
                $module1TotalSaleChartAmount[] = 0 + $TotalInstructorSaleChart->amount;
            }

            //Vendor           
            $module1TotalVendorChart = DB::table('subscribers_package_details')
                    ->where('module_id', 1)
                    ->whereYear('start_date', '=', date('Y'))
                    ->whereMonth('start_date', '=', date('m'))
                    ->whereDay('start_date', '=', $i)
                    ->whereNotNull('vendor_id')
                    ->WhereNull('trainer_id')
                    ->first([DB::raw('SUM(profit) as amount')]);
            
             //Instructor Sale
            $TotalInstructorProfitChart = DB::table($this->instructorSubscriptionTable)
                    ->where('module_id', 1)
                    ->whereYear('created_at', '=', date('Y'))
                    ->whereMonth('created_at', '=', date('m'))
                    ->whereDay('created_at', '=', $i)
                    ->first([DB::raw('SUM(profit) as amount')]);

            if (!empty($module1TotalVendorChart->amount)) {
                $module1TotalVendorChartAmount[] = $module1TotalVendorChart->amount + $TotalInstructorProfitChart->amount;
            } else {
                $module1TotalVendorChartAmount[] = 0 + $TotalInstructorProfitChart->amount;
            }

            //Trainer            
            $module1TotalTrainerChart = DB::table('subscribers_package_details')
                    ->where('module_id', 1)
                    ->whereYear('start_date', '=', date('Y'))
                    ->whereMonth('start_date', '=', date('m'))
                    ->whereDay('start_date', '=', $i)
                    ->whereNotNull('trainer_id')
                    ->WhereNull('vendor_id')
                    ->first([DB::raw('SUM(profit) as amount')]);

            if (!empty($module1TotalTrainerChart->amount)) {
                $module1TotalTrainerChartAmount[] = $module1TotalTrainerChart->amount;
            } else {
                $module1TotalTrainerChartAmount[] = 0;
            }

            //Module2
            //Total Sale            
            $module2TotalSaleChart = DB::table('subscribers_package_details')
                    ->where('module_id', 2)
                    ->whereYear('start_date', '=', date('Y'))
                    ->whereMonth('start_date', '=', date('m'))
                    ->whereDay('start_date', '=', $i)
                    ->first([DB::raw('SUM(price) as amount')]);

            if (!empty($module2TotalSaleChart->amount)) {
                $module2TotalSaleChartAmount[] = $module2TotalSaleChart->amount;
            } else {
                $module2TotalSaleChartAmount[] = 0;
            }

            //Vendor           
            $module2TotalVendorChart = DB::table('subscribers_package_details')
                    ->where('module_id', 2)
                    ->whereYear('start_date', '=', date('Y'))
                    ->whereMonth('start_date', '=', date('m'))
                    ->whereDay('start_date', '=', $i)
                    ->whereNotNull('vendor_id')
                    ->WhereNull('trainer_id')
                    ->first([DB::raw('SUM(profit) as amount')]);

            if (!empty($module2TotalVendorChart->amount)) {
                $module2TotalVendorChartAmount[] = $module2TotalVendorChart->amount;
            } else {
                $module2TotalVendorChartAmount[] = 0;
            }

            //Module3
            //Total Sale            
            $module3TotalSaleChart = DB::table('subscribers_package_details')
                    ->where('module_id', 3)
                    ->whereYear('start_date', '=', date('Y'))
                    ->whereMonth('start_date', '=', date('m'))
                    ->whereDay('start_date', '=', $i)
                    ->first([DB::raw('SUM(price) as amount')]);

            if (!empty($module3TotalSaleChart->amount)) {
                $module3TotalSaleChartAmount[] = $module3TotalSaleChart->amount;
            } else {
                $module3TotalSaleChartAmount[] = 0;
            }

            //Module 4
            //Total Sale            
            $module4TotalSaleChart = DB::table($this->orderedTable)
                    ->whereYear('created_at', '=', date('Y'))
                    ->whereMonth('created_at', '=', date('m'))
                    ->whereDay('created_at', '=', $i)
                    ->where('order_status_id', '!=', 4)
                    ->first([DB::raw('SUM(total) as amount')]);

            if (!empty($module4TotalSaleChart->amount)) {
                $module4TotalSaleChartAmount[] = $module4TotalSaleChart->amount;
            } else {
                $module4TotalSaleChartAmount[] = 0;
            }

            //Vendor           
            $module4TotalVendorChart = DB::table($this->orderedTable)
                    ->whereYear('created_at', '=', date('Y'))
                    ->whereMonth('created_at', '=', date('m'))
                    ->whereDay('created_at', '=', $i)
                    ->where('order_status_id', '!=', 4)
                    ->first([DB::raw('SUM(profit) as amount')]);

            if (!empty($module4TotalVendorChart->amount)) {
                $module4TotalVendorChartAmount[] = $module4TotalVendorChart->amount;
            } else {
                $module4TotalVendorChartAmount[] = 0;
            }
        }
        $module1TotalSaleChartAmount = array_except($module1TotalSaleChartAmount, 0);
        $module1TotalVendorChartAmount = array_except($module1TotalVendorChartAmount, 0);
        $module1TotalTrainerChartAmount = array_except($module1TotalTrainerChartAmount, 0);

        //Module2
        $module2TotalSaleChartAmount = array_except($module2TotalSaleChartAmount, 0);
        $module2TotalVendorChartAmount = array_except($module2TotalVendorChartAmount, 0);

        //Module3
        $module3TotalSaleChartAmount = array_except($module3TotalSaleChartAmount, 0);

        //Module4
        $module4TotalSaleChartAmount = array_except($module4TotalSaleChartAmount, 0);
        $module4TotalVendorChartAmount = array_except($module4TotalVendorChartAmount, 0);


        $xasis = array_except($xasis, 0);

        $chart = Charts::multi('line', 'highcharts')
                ->colors(['#0fbbbd', '#ed164f', '#e78e24'])
                ->title('Sales Report')
                ->elementLabel("Amount (KD)")
                ->labels($xasis)
                ->dataset('Total Sales', $module1TotalSaleChartAmount)
                ->dataset('Total Vendors Amount', $module1TotalVendorChartAmount)
                ->dataset('Total Trainers Amount', $module1TotalTrainerChartAmount);

        //Module2 chart
        $chart2 = Charts::multi('line', 'highcharts')
                ->colors(['#0fbbbd', '#ed164f'])
                ->title('Sales Report')
                ->elementLabel("Amount (KD)")
                ->labels($xasis)
                ->dataset('Total Sales', $module2TotalSaleChartAmount)
                ->dataset('Total Vendors Amount', $module2TotalVendorChartAmount);

        //Module3 chart
        $chart3 = Charts::multi('line', 'highcharts')
                ->colors(['#ed164f'])
                ->title('Sales Report')
                ->elementLabel("Amount (KD)")
                ->labels($xasis)
                ->dataset('Total Sales', $module3TotalSaleChartAmount);

        //Module4 chart
        $chart4 = Charts::multi('line', 'highcharts')
                ->colors(['#0fbbbd', '#ed164f'])
                ->title('Sales Report')
                ->elementLabel("Amount (KD)")
                ->labels($xasis)
                ->dataset('Total Sales', $module4TotalSaleChartAmount)
                ->dataset('Total Vendors Amount', $module4TotalVendorChartAmount);





        return view('admin.incomeStatistics.index')
                        ->with('totalAmount', $totalAmount)
                        ->with('totalProfit', $totalProfit)
                        ->with('vendorAmount', $vendorAmount)
                        ->with('trainerAmount', $trainerAmount)
                        ->with('module1', $module1)
                        ->with('module2', $module2)
                        ->with('module3', $module3)
                        ->with('module4', $module4)
                        ->with('module1TotalAmount', $module1TotalAmount)
                        ->with('module1TotalProfit', $module1TotalProfit)
                        ->with('module1TotalVendorAmount', $module1TotalVendorAmount)
                        ->with('module1TotalTrainerAmount', $module1TotalTrainerAmount)
                        ->with('module2TotalAmount', $module2TotalAmount)
                        ->with('module2TotalProfit', $module2TotalProfit)
                        ->with('module2TotalVendorAmount', $module2TotalVendorAmount)
                        ->with('module3TotalAmount', $module3TotalAmount)
                        ->with('module4TotalAmount', $M4totalAmount)
                        ->with('module4TotalProfit', $M4totalProfit)
                        ->with('module4TotalVendorAmount', $M4totalVendorProfit)
                        ->with('totalDeliverycharge', $totalDeliverycharge)
                        ->with('totalCoupon', $totalCoupon)
                        ->with('chart', $chart)
                        ->with('chart2', $chart2)
                        ->with('chart3', $chart3)
                        ->with('chart4', $chart4);
    }

    public function ajaxchart(Request $request) {

        $this->orderedTable = 'orders';
        $this->orderTotalTable = 'order_total';
        $this->instructorSubscriptionTable = 'instructor_subscribers_package_details';

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
                        ->sum('price');

                $instructorProfit = DB::table($this->instructorSubscriptionTable)
                        ->where('module_id', 1)
                        ->whereYear('created_at', '=', $Year)
                        ->whereMonth('created_at', '=', $Month)
                        ->sum('profit');

                $instructorAdminCommission = DB::table($this->instructorSubscriptionTable)
                        ->where('module_id', 1)
                        ->whereYear('created_at', '=', $Year)
                        ->whereMonth('created_at', '=', $Month)
                        ->sum('commission');

                //Get total sale and total profit 
                $totalAmount = DB::table('subscribers_package_details')
                        ->whereYear('start_date', '=', $Year)
                        ->whereMonth('start_date', '=', $Month)
                        ->sum('price');

                //Amount Modul4
                $M4totalAmount = DB::table($this->orderedTable)
                        ->whereYear('created_at', '=', $Year)
                        ->whereMonth('created_at', '=', $Month)
                        ->where('order_status_id', '!=', 4)
                        ->sum('total');

                $totalAmount = $totalAmount + $M4totalAmount + $instructorAmount;

                $totalProfit = DB::table('subscribers_package_details')
                        ->whereYear('start_date', '=', $Year)
                        ->whereMonth('start_date', '=', $Month)
                        ->sum('commission');

                //Profit Module4
                $M4totalProfit = DB::table($this->orderedTable)
                        ->whereYear('created_at', '=', $Year)
                        ->whereMonth('created_at', '=', $Month)
                        ->where('order_status_id', '!=', 4)
                        ->sum('commission');

                $totalProfit = $totalProfit + $M4totalProfit + $instructorAdminCommission;


                $vendorAmount = DB::table('subscribers_package_details')
                        ->whereNotNull('vendor_id')
                        ->WhereNull('trainer_id')
                        ->whereYear('start_date', '=', $Year)
                        ->whereMonth('start_date', '=', $Month)
                        ->sum('profit');

                //Vendor Profit Module4
                $M4vendorAmount = DB::table($this->orderedTable)
                        ->whereYear('created_at', '=', $Year)
                        ->whereMonth('created_at', '=', $Month)
                        ->where('order_status_id', '!=', 4)
                        ->sum('profit');

                $vendorAmount = $vendorAmount + $M4vendorAmount + $instructorProfit;

                $trainerAmount = DB::table('subscribers_package_details')
                        ->whereNotNull('trainer_id')
                        ->WhereNull('vendor_id')
                        ->whereYear('start_date', '=', $Year)
                        ->whereMonth('start_date', '=', $Month)
                        ->sum('profit');

                $totalDeliverycharge = DB::table($this->orderTotalTable)
                        ->leftJoin($this->orderedTable, 'orders.id', '=', 'order_total.order_id')
                        ->whereYear('orders.created_at', '=', $Year)
                        ->whereMonth('orders.created_at', '=', $Month)
                        ->where('orders.order_status_id', '!=', 4)
                        ->sum('delivery_charge');

                $totalCoupon = DB::table($this->orderTotalTable)
                        ->leftJoin($this->orderedTable, 'orders.id', '=', 'order_total.order_id')
                        ->whereYear('orders.created_at', '=', $Year)
                        ->whereMonth('orders.created_at', '=', $Month)
                        ->where('orders.order_status_id', '!=', 4)
                        ->sum('coupon_discount');

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


                //Module 1 Statistics
                //Get total sale and total profit 
                $module1TotalAmount = DB::table('subscribers_package_details')
                        ->where('module_id', 1)
                        ->whereYear('start_date', '=', $Year)
                        ->whereMonth('start_date', '=', $Month)
                        ->sum('price');
                
                 $module1TotalAmount = $instructorAmount + $module1TotalAmount;

                $module1TotalProfit = DB::table('subscribers_package_details')
                        ->where('module_id', 1)
                        ->whereYear('start_date', '=', $Year)
                        ->whereMonth('start_date', '=', $Month)
                        ->sum('commission');
                
                $module1TotalProfit = $instructorAdminCommission + $module1TotalProfit;


                //Vendor profit
                $module1TotalVendorAmount = DB::table('subscribers_package_details')
                        ->where('module_id', 1)
                        ->whereYear('start_date', '=', $Year)
                        ->whereMonth('start_date', '=', $Month)
                        ->whereNotNull('vendor_id')
                        ->WhereNull('trainer_id')
                        ->sum('profit');
                
                $module1TotalVendorAmount = $instructorProfit + $module1TotalVendorAmount;

                //Trainer profit
                $module1TotalTrainerAmount = DB::table('subscribers_package_details')
                        ->where('module_id', 1)
                        ->whereYear('start_date', '=', $Year)
                        ->whereMonth('start_date', '=', $Month)
                        ->whereNotNull('trainer_id')
                        ->WhereNull('vendor_id')
                        ->sum('profit');

                //Module 2 Statistics
                //Get total sale and total profit 
                $module2TotalAmount = DB::table('subscribers_package_details')
                        ->where('module_id', 2)
                        ->whereYear('start_date', '=', $Year)
                        ->whereMonth('start_date', '=', $Month)
                        ->sum('price');

                $module2TotalProfit = DB::table('subscribers_package_details')
                        ->where('module_id', 2)
                        ->whereYear('start_date', '=', $Year)
                        ->whereMonth('start_date', '=', $Month)
                        ->sum('commission');


                //Vendor profit
                $module2TotalVendorAmount = DB::table('subscribers_package_details')
                        ->where('module_id', 2)
                        ->whereYear('start_date', '=', $Year)
                        ->whereMonth('start_date', '=', $Month)
                        ->whereNotNull('vendor_id')
                        ->WhereNull('trainer_id')
                        ->sum('profit');

                //Module 3 Statistics
                //Get total sale
                $module3TotalAmount = DB::table('subscribers_package_details')
                        ->where('module_id', 3)
                        ->whereYear('start_date', '=', $Year)
                        ->whereMonth('start_date', '=', $Month)
                        ->sum('price');


                //Chart 
                $month_days = cal_days_in_month(CAL_GREGORIAN, $Month, $Year);
                for ($i = 0; $i <= $month_days; $i++) {
                    $xasis[] = $i;
                    //Total Sale 
                    $module1TotalSaleChart = DB::table('subscribers_package_details')
                            ->where('module_id', 1)
                            ->whereYear('start_date', '=', $Year)
                            ->whereMonth('start_date', '=', $Month)
                            ->whereDay('start_date', '=', $i)
                            ->first([DB::raw('SUM(price) as amount')]);
                    
                    //Instructor Sale
                    $TotalInstructorSaleChart = DB::table($this->instructorSubscriptionTable)
                            ->where('module_id', 1)
                            ->whereYear('created_at', '=', $Year)
                            ->whereMonth('created_at', '=', $Month)
                            ->whereDay('created_at', '=', $i)
                            ->first([DB::raw('SUM(price) as amount')]);

                    if (!empty($module1TotalSaleChart->amount)) {
                        $module1TotalSaleChartAmount[] = $module1TotalSaleChart->amount + $TotalInstructorSaleChart->amount;
                    } else {
                        $module1TotalSaleChartAmount[] = 0 + $TotalInstructorSaleChart->amount;
                    }
                    //Vendor 
                    $module1TotalVendorChart = DB::table('subscribers_package_details')
                            ->where('module_id', 1)
                            ->whereYear('start_date', '=', $Year)
                            ->whereMonth('start_date', '=', $Month)
                            ->whereDay('start_date', '=', $i)
                            ->whereNotNull('vendor_id')
                            ->WhereNull('trainer_id')
                            ->first([DB::raw('SUM(profit) as amount')]);
                     //Instructor Sale
                    $TotalInstructorProfitChart = DB::table($this->instructorSubscriptionTable)
                            ->where('module_id', 1)
                            ->whereYear('created_at', '=', $Year)
                            ->whereMonth('created_at', '=', $Month)
                            ->whereDay('created_at', '=', $i)
                            ->first([DB::raw('SUM(profit) as amount')]);

                    if (!empty($module1TotalVendorChart->amount)) {
                        $module1TotalVendorChartAmount[] = $module1TotalVendorChart->amount + $TotalInstructorProfitChart->amount;
                    } else {
                        $module1TotalVendorChartAmount[] = 0 + $TotalInstructorProfitChart->amount;
                    }

                    //Trainer 
                    $module1TotalTrainerChart = DB::table('subscribers_package_details')
                            ->where('module_id', 1)
                            ->whereYear('start_date', '=', $Year)
                            ->whereMonth('start_date', '=', $Month)
                            ->whereDay('start_date', '=', $i)
                            ->whereNotNull('trainer_id')
                            ->WhereNull('vendor_id')
                            ->first([DB::raw('SUM(profit) as amount')]);

                    if (!empty($module1TotalTrainerChart->amount)) {
                        $module1TotalTrainerChartAmount[] = $module1TotalTrainerChart->amount;
                    } else {
                        $module1TotalTrainerChartAmount[] = 0;
                    }

                    //Module2
                    //Total Sale            
                    $module2TotalSaleChart = DB::table('subscribers_package_details')
                            ->where('module_id', 2)
                            ->whereYear('start_date', '=', $Year)
                            ->whereMonth('start_date', '=', $Month)
                            ->whereDay('start_date', '=', $i)
                            ->first([DB::raw('SUM(price) as amount')]);

                    if (!empty($module2TotalSaleChart->amount)) {
                        $module2TotalSaleChartAmount[] = $module2TotalSaleChart->amount;
                    } else {
                        $module2TotalSaleChartAmount[] = 0;
                    }

                    //Vendor           
                    $module2TotalVendorChart = DB::table('subscribers_package_details')
                            ->where('module_id', 2)
                            ->whereYear('start_date', '=', $Year)
                            ->whereMonth('start_date', '=', $Month)
                            ->whereDay('start_date', '=', $i)
                            ->whereNotNull('vendor_id')
                            ->WhereNull('trainer_id')
                            ->first([DB::raw('SUM(profit) as amount')]);

                    if (!empty($module2TotalVendorChart->amount)) {
                        $module2TotalVendorChartAmount[] = $module2TotalVendorChart->amount;
                    } else {
                        $module2TotalVendorChartAmount[] = 0;
                    }

                    //Module3
                    //Total Sale            
                    $module3TotalSaleChart = DB::table('subscribers_package_details')
                            ->where('module_id', 3)
                            ->whereYear('start_date', '=', $Year)
                            ->whereMonth('start_date', '=', $Month)
                            ->whereDay('start_date', '=', $i)
                            ->first([DB::raw('SUM(price) as amount')]);

                    if (!empty($module3TotalSaleChart->amount)) {
                        $module3TotalSaleChartAmount[] = $module3TotalSaleChart->amount;
                    } else {
                        $module3TotalSaleChartAmount[] = 0;
                    }

                    //Module4
                    //Total Sale            
                    $module4TotalSaleChart = DB::table($this->orderedTable)
                            ->whereYear('created_at', '=', $Year)
                            ->whereMonth('created_at', '=', $Month)
                            ->whereDay('created_at', '=', $i)
                            ->where('order_status_id', '!=', 4)
                            ->first([DB::raw('SUM(total) as amount')]);

                    if (!empty($module4TotalSaleChart->amount)) {
                        $module4TotalSaleChartAmount[] = $module4TotalSaleChart->amount;
                    } else {
                        $module4TotalSaleChartAmount[] = 0;
                    }

                    //Vendor           
                    $module4TotalVendorChart = DB::table($this->orderedTable)
                            ->whereYear('created_at', '=', $Year)
                            ->whereMonth('created_at', '=', $Month)
                            ->whereDay('created_at', '=', $i)
                            ->where('order_status_id', '!=', 4)
                            ->first([DB::raw('SUM(profit) as amount')]);

                    if (!empty($module4TotalVendorChart->amount)) {
                        $module4TotalVendorChartAmount[] = $module4TotalVendorChart->amount;
                    } else {
                        $module4TotalVendorChartAmount[] = 0;
                    }
                }

                $module1TotalSaleChartAmount = array_except($module1TotalSaleChartAmount, 0);
                $module1TotalVendorChartAmount = array_except($module1TotalVendorChartAmount, 0);
                $module1TotalTrainerChartAmount = array_except($module1TotalTrainerChartAmount, 0);

                //Module2
                $module2TotalSaleChartAmount = array_except($module2TotalSaleChartAmount, 0);
                $module2TotalVendorChartAmount = array_except($module2TotalVendorChartAmount, 0);

                //Module3
                $module3TotalSaleChartAmount = array_except($module3TotalSaleChartAmount, 0);

                //Module4
                $module4TotalSaleChartAmount = array_except($module4TotalSaleChartAmount, 0);
                $module4TotalVendorChartAmount = array_except($module4TotalVendorChartAmount, 0);

                $xasis = array_except($xasis, 0);

                $chart = Charts::multi('line', 'highcharts')
                        ->colors(['#0fbbbd', '#ed164f', '#e78e24'])
                        ->title('Sales Report')
                        ->elementLabel("Amount (KD)")
                        ->labels($xasis)
                        ->dataset('Total Sales', $module1TotalSaleChartAmount)
                        ->dataset('Total Vendors Amount', $module1TotalVendorChartAmount)
                        ->dataset('Total Trainers Amount', $module1TotalTrainerChartAmount);

                //Module2 chart
                $chart2 = Charts::multi('line', 'highcharts')
                        ->colors(['#0fbbbd', '#ed164f'])
                        ->title('Sales Report')
                        ->elementLabel("Amount (KD)")
                        ->labels($xasis)
                        ->dataset('Total Sales', $module2TotalSaleChartAmount)
                        ->dataset('Total Vendors Amount', $module2TotalVendorChartAmount);

                //Module3 chart
                $chart3 = Charts::multi('line', 'highcharts')
                        ->colors(['#ed164f'])
                        ->title('Sales Report')
                        ->elementLabel("Amount (KD)")
                        ->labels($xasis)
                        ->dataset('Total Sales', $module3TotalSaleChartAmount);

                //Module4 chart
                $chart4 = Charts::multi('line', 'highcharts')
                        ->colors(['#0fbbbd', '#ed164f'])
                        ->title('Sales Report')
                        ->elementLabel("Amount (KD)")
                        ->labels($xasis)
                        ->dataset('Total Sales', $module4TotalSaleChartAmount)
                        ->dataset('Total Vendors Amount', $module4TotalVendorChartAmount);

                $returnHTML = view('admin.incomeStatistics.ajaxchart')
                        ->with('totalAmount', $totalAmount)
                        ->with('totalProfit', $totalProfit)
                        ->with('vendorAmount', $vendorAmount)
                        ->with('trainerAmount', $trainerAmount)
                        ->with('module1', $module1)
                        ->with('module2', $module2)
                        ->with('module3', $module3)
                        ->with('module4', $module4)
                        ->with('module1TotalAmount', $module1TotalAmount)
                        ->with('module1TotalProfit', $module1TotalProfit)
                        ->with('module1TotalVendorAmount', $module1TotalVendorAmount)
                        ->with('module1TotalTrainerAmount', $module1TotalTrainerAmount)
                        ->with('module2TotalAmount', $module2TotalAmount)
                        ->with('module2TotalProfit', $module2TotalProfit)
                        ->with('module2TotalVendorAmount', $module2TotalVendorAmount)
                        ->with('module3TotalAmount', $module3TotalAmount)
                        ->with('module4TotalAmount', $M4totalAmount)
                        ->with('module4TotalProfit', $M4totalProfit)
                        ->with('module4TotalVendorAmount', $M4vendorAmount)
                        ->with('totalDeliverycharge', $totalDeliverycharge)
                        ->with('totalCoupon', $totalCoupon)
                        ->with('chart', $chart)
                        ->with('chart2', $chart2)
                        ->with('chart3', $chart3)
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
                    //Get total sale and total profit 
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
                            ->sum('price');

                    $instructorProfit = DB::table($this->instructorSubscriptionTable)
                            ->where('module_id', 1)
                            ->whereBetween('created_at', [$startdate, $enddate])
                            ->sum('profit');

                    $instructorAdminCommission = DB::table($this->instructorSubscriptionTable)
                            ->where('module_id', 1)
                            ->whereBetween('created_at', [$startdate, $enddate])
                            ->sum('commission');

                    $totalAmount = DB::table('subscribers_package_details')
                            ->whereBetween('start_date', [$startdate, $enddate])
                            ->sum('price');

                    //Amount Module4
                    $M4totalAmount = DB::table($this->orderedTable)
                            ->whereBetween('created_at', [$startdate, $enddate])
                            ->where('order_status_id', '!=', 4)
                            ->sum('total');

                    $totalAmount = $totalAmount + $M4totalAmount + $instructorAmount;

                    $totalProfit = DB::table('subscribers_package_details')
                            ->whereBetween('start_date', [$startdate, $enddate])
                            ->sum('commission');

                    //Profit Module4
                    $M4totalProfit = DB::table($this->orderedTable)
                            ->whereBetween('created_at', [$startdate, $enddate])
                            ->where('order_status_id', '!=', 4)
                            ->sum('commission');

                    $totalProfit = $totalProfit + $M4totalProfit + $instructorAdminCommission;


                    $vendorAmount = DB::table('subscribers_package_details')
                            ->whereNotNull('vendor_id')
                            ->WhereNull('trainer_id')
                            ->whereBetween('start_date', [$startdate, $enddate])
                            ->sum('profit');

                    //Vendor Profit 
                    $M4vendorAmount = DB::table($this->orderedTable)
                            ->whereBetween('created_at', [$startdate, $enddate])
                            ->where('order_status_id', '!=', 4)
                            ->sum('profit');

                    $vendorAmount = $vendorAmount + $M4vendorAmount + $instructorProfit;

                    $trainerAmount = DB::table('subscribers_package_details')
                            ->whereNotNull('trainer_id')
                            ->WhereNull('vendor_id')
                            ->whereBetween('start_date', [$startdate, $enddate])
                            ->sum('profit');

                    $totalDeliverycharge = DB::table($this->orderTotalTable)
                            ->leftJoin($this->orderedTable, 'orders.id', '=', 'order_total.order_id')
                            ->whereBetween('orders.created_at', [$startdate, $enddate])
                            ->where('orders.order_status_id', '!=', 4)
                            ->sum('delivery_charge');

                    $totalCoupon = DB::table($this->orderTotalTable)
                            ->leftJoin($this->orderedTable, 'orders.id', '=', 'order_total.order_id')
                            ->whereBetween('orders.created_at', [$startdate, $enddate])
                            ->where('orders.order_status_id', '!=', 4)
                            ->sum('coupon_discount');

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


                    //Module 1 Statistics
                    //Get total sale and total profit 
                    $module1TotalAmount = DB::table('subscribers_package_details')
                            ->where('module_id', 1)
                            ->whereBetween('start_date', [$startdate, $enddate])
                            ->sum('price');
                    
                    $module1TotalAmount = $instructorAmount + $module1TotalAmount;


                    $module1TotalProfit = DB::table('subscribers_package_details')
                            ->where('module_id', 1)
                            ->whereBetween('start_date', [$startdate, $enddate])
                            ->sum('commission');
                    
                    $module1TotalProfit = $instructorAdminCommission + $module1TotalProfit;


                    //Vendor profit
                    $module1TotalVendorAmount = DB::table('subscribers_package_details')
                            ->where('module_id', 1)
                            ->whereBetween('start_date', [$startdate, $enddate])
                            ->whereNotNull('vendor_id')
                            ->WhereNull('trainer_id')
                            ->sum('profit');
                    
                    $module1TotalVendorAmount = $instructorProfit + $module1TotalVendorAmount;

                    //Trainer profit
                    $module1TotalTrainerAmount = DB::table('subscribers_package_details')
                            ->where('module_id', 1)
                            ->whereBetween('start_date', [$startdate, $enddate])
                            ->whereNotNull('trainer_id')
                            ->WhereNull('vendor_id')
                            ->sum('profit');

                    //Module 2 Statistics
                    //Get total sale and total profit 
                    $module2TotalAmount = DB::table('subscribers_package_details')
                            ->where('module_id', 2)
                            ->whereBetween('start_date', [$startdate, $enddate])
                            ->sum('price');

                    $module2TotalProfit = DB::table('subscribers_package_details')
                            ->where('module_id', 2)
                            ->whereBetween('start_date', [$startdate, $enddate])
                            ->sum('commission');


                    //Vendor profit
                    $module2TotalVendorAmount = DB::table('subscribers_package_details')
                            ->where('module_id', 2)
                            ->whereBetween('start_date', [$startdate, $enddate])
                            ->whereNotNull('vendor_id')
                            ->WhereNull('trainer_id')
                            ->sum('profit');

                    //Module 3 Statistics
                    //Get total sale
                    $module3TotalAmount = DB::table('subscribers_package_details')
                            ->where('module_id', 3)
                            ->whereBetween('start_date', [$startdate, $enddate])
                            ->sum('price');



                    //Chart 
                    foreach ($months as $key => $val) {
                        $xasis[] = $key . '-' . $val;
                        //Total Sale 
                        $module1TotalSaleChart = DB::table('subscribers_package_details')
                                ->where('module_id', 1)
                                ->whereYear('start_date', '=', $val)
                                ->whereMonth('start_date', '=', $key)
                                ->first([DB::raw('SUM(price) as amount')]);
                        
                        //Instructor Sale
                        $TotalInstructorSaleChart = DB::table($this->instructorSubscriptionTable)
                                ->where('module_id', 1)
                                ->whereYear('created_at', '=', $val)
                                ->whereMonth('created_at', '=', $key)
                                ->first([DB::raw('SUM(price) as amount')]);
                        
                        if (!empty($module1TotalSaleChart->amount)) {
                            $module1TotalSaleChartAmount[] = $module1TotalSaleChart->amount + $TotalInstructorSaleChart->amount;
                        } else {
                            $module1TotalSaleChartAmount[] = 0 + $TotalInstructorSaleChart->amount;
                        }

                        //Vendor 
                        $module1TotalVendorChart = DB::table('subscribers_package_details')
                                ->where('module_id', 1)
                                ->whereYear('start_date', '=', $val)
                                ->whereMonth('start_date', '=', $key)
                                ->whereNotNull('vendor_id')
                                ->WhereNull('trainer_id')
                                ->first([DB::raw('SUM(profit) as amount')]);
                        
                         //Instructor Sale
                        $TotalInstructorProfitChart = DB::table($this->instructorSubscriptionTable)
                                ->where('module_id', 1)
                                ->whereYear('created_at', '=', $val)
                                ->whereMonth('created_at', '=', $key)
                                ->first([DB::raw('SUM(profit) as amount')]);
                        
                        if (!empty($module1TotalVendorChart->amount)) {
                            $module1TotalVendorChartAmount[] = $module1TotalVendorChart->amount + $TotalInstructorProfitChart->amount;
                        } else {
                            $module1TotalVendorChartAmount[] = 0 + $TotalInstructorProfitChart->amount;
                        }


                        //Trainer 
                        $module1TotalTrainerChart = DB::table('subscribers_package_details')
                                ->where('module_id', 1)
                                ->whereYear('start_date', '=', $val)
                                ->whereMonth('start_date', '=', $key)
                                ->whereNotNull('trainer_id')
                                ->WhereNull('vendor_id')
                                ->first([DB::raw('SUM(profit) as amount')]);
                        if (!empty($module1TotalTrainerChart->amount)) {
                            $module1TotalTrainerChartAmount[] = $module1TotalTrainerChart->amount;
                        } else {
                            $module1TotalTrainerChartAmount[] = 0;
                        }

                        //Module2
                        //Total Sale            
                        $module2TotalSaleChart = DB::table('subscribers_package_details')
                                ->where('module_id', 2)
                                ->whereYear('start_date', '=', $val)
                                ->whereMonth('start_date', '=', $key)
                                ->first([DB::raw('SUM(price) as amount')]);

                        if (!empty($module2TotalSaleChart->amount)) {
                            $module2TotalSaleChartAmount[] = $module2TotalSaleChart->amount;
                        } else {
                            $module2TotalSaleChartAmount[] = 0;
                        }

                        //Vendor           
                        $module2TotalVendorChart = DB::table('subscribers_package_details')
                                ->where('module_id', 2)
                                ->whereYear('start_date', '=', $val)
                                ->whereMonth('start_date', '=', $key)
                                ->whereNotNull('vendor_id')
                                ->WhereNull('trainer_id')
                                ->first([DB::raw('SUM(profit) as amount')]);

                        if (!empty($module2TotalVendorChart->amount)) {
                            $module2TotalVendorChartAmount[] = $module2TotalVendorChart->amount;
                        } else {
                            $module2TotalVendorChartAmount[] = 0;
                        }

                        //Module3
                        //Total Sale            
                        $module3TotalSaleChart = DB::table('subscribers_package_details')
                                ->where('module_id', 3)
                                ->whereYear('start_date', '=', $val)
                                ->whereMonth('start_date', '=', $key)
                                ->first([DB::raw('SUM(price) as amount')]);

                        if (!empty($module3TotalSaleChart->amount)) {
                            $module3TotalSaleChartAmount[] = $module3TotalSaleChart->amount;
                        } else {
                            $module3TotalSaleChartAmount[] = 0;
                        }

                        //Module4
                        //Total Sale            
                        $module4TotalSaleChart = DB::table($this->orderedTable)
                                ->whereYear('created_at', '=', $val)
                                ->whereMonth('created_at', '=', $key)
                                ->where('order_status_id', '!=', 4)
                                ->first([DB::raw('SUM(total) as amount')]);

                        if (!empty($module4TotalSaleChart->amount)) {
                            $module4TotalSaleChartAmount[] = $module4TotalSaleChart->amount;
                        } else {
                            $module4TotalSaleChartAmount[] = 0;
                        }

                        //Vendor           
                        $module4TotalVendorChart = DB::table($this->orderedTable)
                                ->whereYear('created_at', '=', $val)
                                ->whereMonth('created_at', '=', $key)
                                ->where('order_status_id', '!=', 4)
                                ->first([DB::raw('SUM(profit) as amount')]);

                        if (!empty($module4TotalVendorChart->amount)) {
                            $module4TotalVendorChartAmount[] = $module4TotalVendorChart->amount;
                        } else {
                            $module4TotalVendorChartAmount[] = 0;
                        }
                    }

                    $chart = Charts::multi('line', 'highcharts')
                            ->colors(['#0fbbbd', '#ed164f', '#e78e24'])
                            ->title('Sales Report')
                            ->elementLabel("Amount (KD)")
                            ->labels($xasis)
                            ->dataset('Total Sales', $module1TotalSaleChartAmount)
                            ->dataset('Total Vendors Amount', $module1TotalVendorChartAmount)
                            ->dataset('Total Trainers Amount', $module1TotalTrainerChartAmount);

                    //Module2 chart
                    $chart2 = Charts::multi('line', 'highcharts')
                            ->colors(['#0fbbbd', '#ed164f'])
                            ->title('Sales Report')
                            ->elementLabel("Amount (KD)")
                            ->labels($xasis)
                            ->dataset('Total Sales', $module2TotalSaleChartAmount)
                            ->dataset('Total Vendors Amount', $module2TotalVendorChartAmount);

                    //Module3 chart
                    $chart3 = Charts::multi('line', 'highcharts')
                            ->colors(['#ed164f'])
                            ->title('Sales Report')
                            ->elementLabel("Amount (KD)")
                            ->labels($xasis)
                            ->dataset('Total Sales', $module3TotalSaleChartAmount);

                    //Module4 chart
                    $chart4 = Charts::multi('line', 'highcharts')
                            ->colors(['#0fbbbd', '#ed164f'])
                            ->title('Sales Report')
                            ->elementLabel("Amount (KD)")
                            ->labels($xasis)
                            ->dataset('Total Sales', $module4TotalSaleChartAmount)
                            ->dataset('Total Vendors Amount', $module4TotalVendorChartAmount);

                    $returnHTML = view('admin.incomeStatistics.ajaxchart')
                            ->with('totalAmount', $totalAmount)
                            ->with('totalProfit', $totalProfit)
                            ->with('vendorAmount', $vendorAmount)
                            ->with('trainerAmount', $trainerAmount)
                            ->with('module1', $module1)
                            ->with('module2', $module2)
                            ->with('module3', $module3)
                            ->with('module4', $module4)
                            ->with('module1TotalAmount', $module1TotalAmount)
                            ->with('module1TotalProfit', $module1TotalProfit)
                            ->with('module1TotalVendorAmount', $module1TotalVendorAmount)
                            ->with('module1TotalTrainerAmount', $module1TotalTrainerAmount)
                            ->with('module2TotalAmount', $module2TotalAmount)
                            ->with('module2TotalProfit', $module2TotalProfit)
                            ->with('module2TotalVendorAmount', $module2TotalVendorAmount)
                            ->with('module3TotalAmount', $module3TotalAmount)
                            ->with('module4TotalAmount', $M4totalAmount)
                            ->with('module4TotalProfit', $M4totalProfit)
                            ->with('module4TotalVendorAmount', $M4vendorAmount)
                            ->with('totalDeliverycharge', $totalDeliverycharge)
                            ->with('totalCoupon', $totalCoupon)
                            ->with('chart', $chart)
                            ->with('chart2', $chart2)
                            ->with('chart3', $chart3)
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
