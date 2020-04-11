<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Admin\Users;
use App\Models\Admin\VendorLogActivity;
use App\Models\Admin\PaymentDetail;
use App\Helpers\VendorDetail;
use App\Helpers\Permit;
use Carbon\Carbon;
use DB;
use Charts;

class DashboardController extends Controller {

    protected $guard = 'vendor';
    protected $configName;
    protected $table;
    protected $memberTable;
    protected $invoiceTable;

    public function __construct() {
        $this->middleware($this->guard);
        $this->configName = config('global.fitflowVendor');
        $this->middleware('vendorPermission:dashboard');
    }

    public function index() {

        $this->table = VendorDetail::getPrefix() . 'subscribers_package_details';
        $this->memberTable = VendorDetail::getPrefix() . 'members';
        $this->invoiceTable = VendorDetail::getPrefix() . 'member_invoices';
        $this->booking = VendorDetail::getPrefix() . 'bookings';
        $this->registeredUserTable = 'registered_users';
        $this->bookingTable = 'bookings';
        $this->orderedTable = 'orders';
        $this->orderTotalTable = 'order_total';
        $this->orderProductTable = 'order_product';
        $this->instructorSubscriptionTable = 'instructor_subscribers_package_details';

        //Get Sale Count Start Date
        $sale_setting = DB::table('vendors')
                ->select('sale_setting')
                ->where('id', VendorDetail::getID())
                ->first();

        $SaleSetting = new Carbon($sale_setting->sale_setting);


        //Get all module
        $module1 = DB::table('modules')
                ->select('name_en')
                ->where(array('status' => 1, 'id' => 1))
                ->first();

        $module2 = DB::table('modules')
                ->select('name_en')
                ->where(array('status' => 1, 'id' => 2))
                ->first();

        $module3 = DB::table('modules')
                ->select('name_en')
                ->where(array('status' => 1, 'id' => 3))
                ->first();

        $module4 = DB::table('modules')
                ->select('name_en')
                ->where(array('status' => 1, 'id' => 4))
                ->first();


        //Get Latest Log Activity
        $LogActivity = VendorLogActivity::
                leftJoin('vendor_users', 'vendor_users.id', '=', 'vendor_log_activities.user_id')
                ->select('vendor_log_activities.subject', 'vendor_log_activities.created_at')
                ->where('vendor_log_activities.vendor_id', VendorDetail::getID())
                ->whereNotIn('vendor_users.user_role_id', [1])
                ->latest()
                ->limit(8)
                ->get();

        $logCount = sizeof($LogActivity);


        //Get Latest Members 
        $Members = DB::table($this->memberTable)
                ->select('name', 'id')
                ->latest()
                ->limit(8)
                ->get();

        //Get Top Buyers  
        $topBuyers = DB::table($this->table . ' As spd')
                ->leftJoin($this->registeredUserTable . ' As m', 'spd.subscriber_id', '=', 'm.id')
                ->select('m.id', 'm.name', DB::raw("count('spd.subscriber_id') as subscriber_count"))
                ->whereDate('spd.start_date', '>=', $sale_setting->sale_setting)
                ->whereNotNull('spd.subscriber_id')
                ->groupBy('spd.subscriber_id')
                ->orderBy('subscriber_count', 'desc')
                ->limit(8)
                ->get();

        //Get Latest Payments
        $invoiceList = DB::table($this->invoiceTable . ' As inv')
                ->join($this->memberTable . ' As m', 'inv.member_id', '=', 'm.id')
                ->join('vendor_users As vu', 'vu.id', '=', 'inv.collected_by')
                ->select('inv.receipt_num', 'm.name', 'inv.package_name', 'inv.created_at', 'vu.name AS collected_by', 'inv.cash', 'inv.knet', 'inv.price', 'inv.start_date', 'inv.end_date')
                ->whereDate('inv.start_date', '>=', $sale_setting->sale_setting)
                ->get();

        $M1PaymentDetail = PaymentDetail::
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

        $M2PaymentDetail = PaymentDetail::
                select('payment_details.amount', 'payment_details.post_date', 'spd.num_days', 'spd.name_en As package', 'spd.start_date', 'spd.end_date', 'm.name AS user'
                        , DB::raw('(CASE WHEN payment_details.card_type = 1 THEN "KNET" ELSE "Credit Card" END) AS card_type'))
                ->join($this->table . ' As spd', 'spd.payment_id', '=', 'payment_details.id')
                ->leftjoin($this->registeredUserTable . ' As m', 'm.id', '=', 'spd.subscriber_id')
                ->where('payment_details.module_id', 2)
                ->where('spd.vendor_id', VendorDetail::getID())
                ->whereDate('spd.start_date', '>=', $sale_setting->sale_setting)
                ->whereNotNull('spd.payment_id')
                ->orderby('spd.id', 'DESC')
                ->get();

        //Module4 Payment Details
        $M4PaymentDetail = PaymentDetail::
                select('payment_details.amount', 'payment_details.post_date', 'm.name AS user', 'orders.id', 'order_product.name_en'
                        , DB::raw('(CASE WHEN orders.payment_method = 1 THEN "KNET" WHEN orders.payment_method = 2 THEN "Credit Card" ELSE "Cash On Delivery" END) AS payment_method'))
                ->join($this->orderedTable, 'orders.payment_id', '=', 'payment_details.id')
                ->join('order_product', 'orders.id', '=', 'order_product.order_id')
                ->leftjoin($this->registeredUserTable . ' As m', 'm.id', '=', 'orders.customer_id')
                ->whereDate('orders.created_at', '>=', $sale_setting->sale_setting)
                ->where('orders.order_status_id', '!=', 4)
                ->where('orders.vendor_id', VendorDetail::getID())
                ->orderby('orders.id', 'DESC')
                ->get();

        //Get Top Products    
        $topProducts = DB::table($this->orderProductTable . ' As op')
                ->join($this->orderedTable, 'orders.id', '=', 'op.order_id')
                ->leftJoin('products AS p', 'p.id', '=', 'op.product_id')
                ->select('p.name_en', DB::raw("SUM(op.total) as amount"), DB::raw("count(op.product_id) as sold"))
                ->whereDate('orders.created_at', '>=', $sale_setting->sale_setting)
                ->where('orders.order_status_id', '!=', 4)
                ->where('orders.vendor_id', VendorDetail::getID())
                ->groupBy('op.product_id')
                ->orderBy('amount', 'desc')
                ->limit(5)
                ->get();

        $topProducts = $topProducts->toArray();

        //Latest Orders
        $latestOrders = DB::table($this->orderedTable)
                ->join($this->orderProductTable . ' As op', 'orders.id', '=', 'op.order_id')
                ->leftJoin('products AS p', 'p.id', '=', 'op.product_id')
                ->leftJoin('order_status', 'order_status.id', '=', 'orders.order_status_id')
                ->select('p.name_en', 'orders.id As order_id', 'orders.name', 'orders.email', 'orders.mobile', 'order_status.name_en As order_status', 'orders.total', 'orders.created_at')
                ->whereDate('orders.created_at', '>=', $sale_setting->sale_setting)
                ->where('orders.order_status_id', '!=', 4)
                ->where('orders.vendor_id', VendorDetail::getID())
                ->latest('orders.created_at')
                ->limit(10)
                ->get();


        //Get Top Packages    
        $topPackages = DB::table($this->table . ' As spd')
                ->leftJoin('vendor_packages', 'spd.package_id', '=', 'vendor_packages.id')
                ->select('vendor_packages.id', 'vendor_packages.name_en', DB::raw("SUM(spd.price) as amount"), DB::raw("count(spd.package_id) as sold"))
                ->whereDate('spd.start_date', '>=', $sale_setting->sale_setting)
                ->where('spd.module_id', '!=', 3)
                ->groupBy('spd.package_id')
                ->orderBy('amount', 'desc')
                ->limit(5)
                ->get();

        $topPackages = $topPackages->toArray();



        //Module1 Total Profit and Total Sale
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

        
        $M1totalAmount = DB::table($this->table)
                ->where('module_id', 1)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('price');
        
        $M1totalAmount = $instructorAmount + $M1totalAmount;

        $M1totalProfit = DB::table($this->table)
                ->where('module_id', 1)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('profit');
        
        $M1totalProfit = $instructorProfit + $M1totalProfit;

        //Module2 Total Profit and Total Sale
        $M2totalAmount = DB::table($this->table)
                ->where('module_id', 2)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('price');

        $M2totalProfit = DB::table($this->table)
                ->where('module_id', 2)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('profit');

        //Module 3  Total Bookings and Total Profit
        $totalBookings = DB::table($this->bookingTable . ' As b')
                        ->whereYear('created_at', '=', date('Y'))
                        ->whereMonth('created_at', '=', date('m'))
                        ->whereDate('b.created_at', '>=', $sale_setting->sale_setting)
                        ->where('b.vendor_id', VendorDetail::getID())
                        ->where('b.module_id', 3)->count();


        $totalBookedClassProfit = DB::table($this->bookingTable . ' As b')
                ->whereYear('created_at', '=', date('Y'))
                ->whereMonth('created_at', '=', date('m'))
                ->whereDate('b.created_at', '>=', $sale_setting->sale_setting)
                ->where('b.module_id', 3)
                ->where('b.vendor_id', VendorDetail::getID())
                ->sum('b.profit');

        //Module4 Total Profit and Total Sale
        $M4totalAmount = DB::table($this->orderedTable)
                ->whereYear('created_at', '=', date('Y'))
                ->whereMonth('created_at', '=', date('m'))
                ->where('order_status_id', '!=', 4)
                ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                ->where('vendor_id', VendorDetail::getID())
                ->sum('total');

        $M4totalProfit = DB::table($this->orderedTable)
                ->whereYear('created_at', '=', date('Y'))
                ->whereMonth('created_at', '=', date('m'))
                ->where('order_status_id', '!=', 4)
                ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                ->where('vendor_id', VendorDetail::getID())
                ->sum('profit');


        //Chart   
        $month_days = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));

        for ($i = 0; $i <= $month_days; $i++) {
            $xasis[] = $i;
            //Total Sale            
            $TotalSaleChart = DB::table($this->table)
                    ->whereYear('start_date', '=', date('Y'))
                    ->whereMonth('start_date', '=', date('m'))
                    ->whereDay('start_date', '=', $i)
                    ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                    ->first([DB::raw('SUM(price) as amount')]);

            //Total Ordered Sale
            $TotalOrderedChart = DB::table($this->orderedTable)
                    ->whereYear('created_at', '=', date('Y'))
                    ->whereMonth('created_at', '=', date('m'))
                    ->whereDay('created_at', '=', $i)
                    ->where('order_status_id', '!=', 4)
                    ->where('vendor_id', VendorDetail::getID())
                    ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                    ->first([DB::raw('SUM(total) as Total')]);
            
            //Instructor Sale
            $TotalInstructorSaleChart = DB::table($this->instructorSubscriptionTable)
                    ->where('module_id', 1)
                    ->whereYear('created_at', '=', date('Y'))
                    ->whereMonth('created_at', '=', date('m'))
                    ->whereDay('created_at', '=', $i)
                    ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                    ->where('vendor_id', VendorDetail::getID())
                    ->first([DB::raw('SUM(price) as amount')]);


            if (!empty($TotalSaleChart->amount) && !empty($TotalOrderedChart->Total)) {
                $TotalSaleChartAmount[] = $TotalSaleChart->amount + $TotalOrderedChart->Total + $TotalInstructorSaleChart->amount;
            } elseif (!empty($TotalSaleChart->amount) && empty($TotalOrderedChart->Total)) {
                $TotalSaleChartAmount[] = $TotalSaleChart->amount + $TotalInstructorSaleChart->amount;
            } elseif (empty($TotalSaleChart->amount) && !empty($TotalOrderedChart->Total)) {
                $TotalSaleChartAmount[] = $TotalOrderedChart->Total + $TotalInstructorSaleChart->amount;
            } else {
                $TotalSaleChartAmount[] = 0 + $TotalInstructorSaleChart->amount;
            }

            //Total Profit           
            $TotalProfitChart = DB::table($this->table)
                    ->whereYear('start_date', '=', date('Y'))
                    ->whereMonth('start_date', '=', date('m'))
                    ->whereDay('start_date', '=', $i)
                    ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                    ->first([DB::raw('SUM(profit) as amount')]);

            //Total Ordered Profit
            $TotalOrderedProfitChart = DB::table($this->orderedTable)
                    ->whereYear('created_at', '=', date('Y'))
                    ->whereMonth('created_at', '=', date('m'))
                    ->whereDay('created_at', '=', $i)
                    ->where('order_status_id', '!=', 4)
                    ->where('vendor_id', VendorDetail::getID())
                    ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                    ->first([DB::raw('SUM(profit) as Total')]);
            
             //Instructor Sale
            $TotalInstructorProfitChart = DB::table($this->instructorSubscriptionTable)
                    ->where('module_id', 1)
                    ->whereYear('created_at', '=', date('Y'))
                    ->whereMonth('created_at', '=', date('m'))
                    ->whereDay('created_at', '=', $i)
                    ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                    ->where('vendor_id', VendorDetail::getID())
                    ->first([DB::raw('SUM(profit) as amount')]);

            if (!empty($TotalProfitChart->amount) && !empty($TotalOrderedProfitChart->Total)) {
                $TotalProfitChartAmount[] = $TotalProfitChart->amount + $TotalOrderedProfitChart->Total + $TotalInstructorProfitChart->amount;
            } elseif (!empty($TotalProfitChart->amount) && empty($TotalOrderedProfitChart->Total)) {
                $TotalProfitChartAmount[] = $TotalProfitChart->amount + $TotalInstructorProfitChart->amount;
            } elseif (empty($TotalProfitChart->amount) && !empty($TotalOrderedProfitChart->Total)) {
                $TotalProfitChartAmount[] = $TotalOrderedProfitChart->Total + $TotalInstructorProfitChart->amount;
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


        //Get Top Module2 Classes  
        $topM2Classes = DB::table($this->booking . ' As b')
                ->join('class_master', 'b.class_master_id', '=', 'class_master.id')
                ->select('class_master.name_en', 'class_master.id', DB::raw("count('b.class_master_id') as class_count"))
                ->whereDate('b.created_at', '>=', $sale_setting->sale_setting)
                ->where('b.module_id', 2)
                ->groupBy('b.class_master_id')
                ->orderBy('class_count', 'desc')
                ->limit(5)
                ->get();

        $topM2ClassCount = $topM2Classes->count();

        $topM2ClassArray = array();

        foreach ($topM2Classes As $topClass) {

            //Get Top Class wise governorate  
            $topM2GovClass = DB::table($this->booking . ' As b')
                    ->join('class_master', 'b.class_master_id', '=', 'class_master.id')
                    ->join('governorates', 'b.governorate_id', '=', 'governorates.id')
                    ->select('governorates.name_en AS governorate', DB::raw("count('b.governorate_id') as class_count"))
                    ->whereDate('b.created_at', '>=', $sale_setting->sale_setting)
                    ->where('b.module_id', 2)
                    ->where('b.class_master_id', $topClass->id)
                    ->groupBy('b.governorate_id')
                    ->orderBy('class_count', 'desc')
                    ->get();


            $topM2ClassArray[$topClass->name_en] = $topM2GovClass;
        }

        //Get Top Module3 Classes  
        $topM3Classes = DB::table($this->bookingTable . ' As b')
                ->join('class_master', 'b.class_master_id', '=', 'class_master.id')
                ->select('class_master.name_en', 'class_master.id', DB::raw("count('b.class_master_id') as class_count"))
                ->whereDate('b.created_at', '>=', $sale_setting->sale_setting)
                ->where('b.module_id', 3)
                ->groupBy('b.class_master_id')
                ->orderBy('class_count', 'desc')
                ->limit(5)
                ->get();

        $topM3ClassCount = $topM3Classes->count();

        $topM3ClassArray = array();

        foreach ($topM3Classes As $topClass) {

            //Get Top Class wise governorate  
            $topM3GovClass = DB::table($this->bookingTable . ' As b')
                    ->join('class_master', 'b.class_master_id', '=', 'class_master.id')
                    ->join('governorates', 'b.governorate_id', '=', 'governorates.id')
                    ->select('governorates.name_en AS governorate', DB::raw("count('b.governorate_id') as class_count"))
                    ->whereDate('b.created_at', '>=', $sale_setting->sale_setting)
                    ->where('b.module_id', 3)
                    ->where('b.class_master_id', $topClass->id)
                    ->groupBy('b.governorate_id')
                    ->orderBy('class_count', 'desc')
                    ->get();


            $topM3ClassArray[$topClass->name_en] = $topM3GovClass;
        }





        return view('fitflowVendor.dashboard.dashboard')
                        ->with('LogActivity', $LogActivity)
                        ->with('Members', $Members)
                        ->with('M1totalAmount', $M1totalAmount)
                        ->with('M1totalProfit', $M1totalProfit)
                        ->with('M2totalAmount', $M2totalAmount)
                        ->with('M2totalProfit', $M2totalProfit)
                        ->with('topBuyers', $topBuyers)
                        ->with('module1', $module1)
                        ->with('module2', $module2)
                        ->with('module3', $module3)
                        ->with('module4', $module4)
                        ->with('M1PaymentDetail', $M1PaymentDetail)
                        ->with('M2PaymentDetail', $M2PaymentDetail)
                        ->with('M4PaymentDetail', $M4PaymentDetail)
                        ->with('M4totalAmount', $M4totalAmount)
                        ->with('M4totalProfit', $M4totalProfit)
                        ->with('invoiceList', $invoiceList)
                        ->with('topPackages', $topPackages)
                        ->with('logCount', $logCount)
                        ->with('topM2ClassArray', $topM2ClassArray)
                        ->with('topM2ClassCount', $topM2ClassCount)
                        ->with('topM3ClassArray', $topM3ClassArray)
                        ->with('topM3ClassCount', $topM3ClassCount)
                        ->with('totalBookings', $totalBookings)
                        ->with('totalBookedClassProfit', $totalBookedClassProfit)
                        ->with('latestOrders', $latestOrders)
                        ->with('topProducts', $topProducts)
                        ->with('chart', $chart);
    }

}
