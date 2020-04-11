<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Requests;
use App\Models\Admin\Users;
use App\Models\Admin\LogActivity;
use App\Models\Admin\Trainer;
use App\Models\Admin\RegisteredUser;
use App\Models\Admin\PaymentDetail;
use App\Helpers\Permit;
use DB;
use Charts;

class DashboardController extends Controller {

    use AuthenticatesUsers;

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:dashboard');
    }

    public function index() {

        $this->registeredUserTable = 'registered_users';
        $this->table = 'subscribers_package_details';
        $this->registeredUserTable = 'registered_users';
        $this->bookingTable = 'bookings';
        $this->orderedTable = 'orders';
        $this->orderTotalTable = 'order_total';
        $this->orderProductTable = 'order_product';
        $this->instructorSubscriptionTable = 'instructor_subscribers_package_details';
        
        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('dashboard-view');

        //Total Vendors
        $vendor_records_count = DB::table('vendors')->whereNull('vendors.deleted_at')->count();

        //Total Registered Users
        $registered_users_records_count = DB::table($this->registeredUserTable)->whereNull('deleted_at')->count();

        //Total Trainers
        $trainer_records_count = DB::table('trainers')->whereNull('deleted_at')->count();

        //Total Android Users
        $android_users_count = DB::table('push_registration')->where('mobile_type', '=', 'a')->count();

        //Total Android Users
        $ios_users_count = DB::table('push_registration')->where('mobile_type', '=', 'i')->count();

        $total_device_users = $android_users_count + $ios_users_count;

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
        $LogActivity = LogActivity::
                select('subject', 'created_at')
                ->where('user_type', 0)
                ->latest()
                ->limit(8)
                ->whereNotIn('user_id', [1])
                ->get();

        //Get Latest Trainers 
        $Trainer = Trainer::
                select('name', 'profile_image', 'id')
                ->whereNull('deleted_at')
                ->latest()
                ->limit(8)
                ->get();

        //Get Latest Registered Users 
        $RegisteredUser = RegisteredUser::
                select('name', 'profile_image', 'id')
                ->whereNull('deleted_at')
                ->latest()
                ->limit(8)
                ->get();

        //Get Top Buyers  
        $topBuyers = DB::table('subscribers_package_details AS spd')
                ->leftJoin('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->select('registered_users.id', 'registered_users.name', 'registered_users.profile_image', DB::raw("count('spd.subscriber_id') as subscriber_count"))
                ->whereNotNull('spd.subscriber_id')
                ->groupBy('spd.subscriber_id')
                ->orderBy('subscriber_count', 'desc')
                ->limit(8)
                ->get();


        //Get Latest Trainer Payments 
        $trainerPaymentDetail = PaymentDetail::
                select('payment_details.reference_id', 'payment_details.amount', 'payment_details.post_date', 'payment_details.result', 'spd.num_points', 'spd.name_en As package', 'spd.start_date', 'spd.end_date', 'registered_users.name AS user', 'trainers.name As trainer'
                        , DB::raw('(CASE WHEN payment_details.card_type = 1 THEN "KNET" ELSE "Credit Card" END) AS card_type'))
                ->join('trainer_subscribers_package_details AS spd', 'spd.payment_id', '=', 'payment_details.id')
                ->leftjoin('registered_users', 'registered_users.id', '=', 'spd.subscriber_id')
                ->leftjoin('trainers', 'trainers.id', '=', 'spd.trainer_id')
                ->where('spd.module_id', 1)
                ->whereNotNull('spd.trainer_id')
                ->WhereNull('spd.vendor_id')
                ->whereNotNull('spd.payment_id')
                ->whereNotNull('spd.payment_id')
                ->orderby('spd.id', 'DESC')
                ->get();

        //Get Latest Vendor Module1 Payments 
        $M1PaymentDetail = PaymentDetail::
                select('spd.id', 'payment_details.reference_id', 'payment_details.amount', 'payment_details.post_date', 'payment_details.result', 'spd.num_points', 'spd.name_en As package', 'spd.start_date', 'spd.end_date', 'registered_users.name AS user', 'vendors.name As vendor'
                        , DB::raw('(CASE WHEN payment_details.card_type = 1 THEN "KNET" ELSE "Credit Card" END) AS card_type'))
                ->join('subscribers_package_details AS spd', 'spd.payment_id', '=', 'payment_details.id')
                ->leftjoin('registered_users', 'registered_users.id', '=', 'spd.subscriber_id')
                ->leftjoin('vendors', 'vendors.id', '=', 'spd.vendor_id')
                ->where('spd.module_id', 1)
                ->WhereNull('spd.trainer_id')
                ->whereNotNull('spd.vendor_id')
                ->whereNotNull('spd.payment_id')
                ->orderby('spd.id', 'DESC')
                ->get();


        //Module2 Payment Details
        $M2PaymentDetail = PaymentDetail::
                select('payment_details.amount', 'payment_details.post_date', 'spd.num_days', 'spd.name_en As package', 'spd.start_date', 'spd.end_date', 'm.name AS user'
                        , DB::raw('(CASE WHEN payment_details.card_type = 1 THEN "KNET" ELSE "Credit Card" END) AS card_type'))
                ->join($this->table . ' As spd', 'spd.payment_id', '=', 'payment_details.id')
                ->leftjoin($this->registeredUserTable . ' As m', 'm.id', '=', 'spd.subscriber_id')
                ->where('spd.module_id', 2)
                ->WhereNull('spd.trainer_id')
                ->whereNotNull('spd.vendor_id')
                ->whereNotNull('spd.payment_id')
                ->orderby('spd.id', 'DESC')
                ->get();

        //Module3 Payment Details
        $M3PaymentDetail = PaymentDetail::
                select('payment_details.amount', 'payment_details.post_date', 'spd.num_days', 'spd.name_en As package', 'spd.start_date', 'spd.end_date', 'm.name AS user'
                        , DB::raw('(CASE WHEN payment_details.card_type = 1 THEN "KNET" ELSE "Credit Card" END) AS card_type'))
                ->join($this->table . ' As spd', 'spd.payment_id', '=', 'payment_details.id')
                ->leftjoin($this->registeredUserTable . ' As m', 'm.id', '=', 'spd.subscriber_id')
                ->where('spd.module_id', 3)
                ->WhereNull('spd.trainer_id')
                ->WhereNull('spd.vendor_id')
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
                ->where('orders.order_status_id', '!=', 4)
                ->orderby('orders.id', 'DESC')
                ->get();

        //Get Top Packages    
        $topPackages = DB::table($this->table . ' As spd')
                ->leftJoin('packages', 'spd.package_id', '=', 'packages.id')
                ->select('packages.id', 'packages.name_en', DB::raw("SUM(spd.price) as amount"), DB::raw("count(spd.package_id) as sold"))
                ->where('spd.module_id', 3)
                ->groupBy('spd.package_id')
                ->orderBy('amount', 'desc')
                ->limit(4)
                ->get();

        $topPackages = $topPackages->toArray();




        //Get Top Vendors    
        $topVendors = DB::table('subscribers_package_details AS spd')
                ->leftJoin('vendors', 'spd.vendor_id', '=', 'vendors.id')
                ->select('vendors.name', DB::raw("SUM(spd.price) as amount"))
                ->whereNotNull('spd.vendor_id')
                ->groupBy('spd.vendor_id')
                ->orderBy('amount', 'desc')
                ->limit(6)
                ->get();

        $topVendors = $topVendors->toArray();

        //Module1 Total Profit and Total Sale
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
        
        $M1totalAmount = DB::table($this->table)
                ->where('module_id', 1)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->sum('price');
        
        $M1totalAmount = $instructorAmount + $M1totalAmount;

        $M1totalProfit = DB::table($this->table)
                ->where('module_id', 1)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->sum('commission');
        
         $M1totalProfit = $instructorProfit + $M1totalProfit;

        //Module2 Total Profit and Total Sale
        $M2totalAmount = DB::table($this->table)
                ->where('module_id', 2)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->sum('price');

        $M2totalProfit = DB::table($this->table)
                ->where('module_id', 2)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->sum('commission');


        //Module 3  Total Bookings and Total Profit
        $totalBookings = DB::table($this->bookingTable . ' As b')
                        ->whereYear('created_at', '=', date('Y'))
                        ->whereMonth('created_at', '=', date('m'))
                        ->where('b.module_id', 3)->count();


        $totalBookedClassProfit = DB::table($this->bookingTable . ' As b')
                ->whereYear('created_at', '=', date('Y'))
                ->whereMonth('created_at', '=', date('m'))
                ->where('b.module_id', 3)
                ->sum('b.commission');

        //Module4 Total Profit and Total Sale
        $M4totalAmount = DB::table($this->orderedTable)
                ->whereYear('created_at', '=', date('Y'))
                ->whereMonth('created_at', '=', date('m'))
                ->where('order_status_id', '!=', 4)
                ->sum('total');

        $M4totalProfit = DB::table($this->orderedTable)
                ->whereYear('created_at', '=', date('Y'))
                ->whereMonth('created_at', '=', date('m'))
                ->where('order_status_id', '!=', 4)
                ->sum('commission');

        $M4totalDeliverycharge = DB::table($this->orderTotalTable)
                ->leftJoin($this->orderedTable, 'orders.id', '=', 'order_total.order_id')
                ->whereYear('orders.created_at', '=', date('Y'))
                ->whereMonth('orders.created_at', '=', date('m'))
                ->where('orders.order_status_id', '!=', 4)
                ->sum('delivery_charge');

        $M4totalCoupon = DB::table($this->orderTotalTable)
                ->leftJoin($this->orderedTable, 'orders.id', '=', 'order_total.order_id')
                ->whereYear('orders.created_at', '=', date('Y'))
                ->whereMonth('orders.created_at', '=', date('m'))
                ->where('orders.order_status_id', '!=', 4)
                ->sum('coupon_discount');

        //Chart   
        $month_days = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));

        for ($i = 0; $i <= $month_days; $i++) {
            $xasis[] = $i;
            //Total Sale            
            $TotalSaleChart = DB::table('subscribers_package_details')
                    ->whereYear('start_date', '=', date('Y'))
                    ->whereMonth('start_date', '=', date('m'))
                    ->whereDay('start_date', '=', $i)
                    ->first([DB::raw('SUM(price) as amount')]);

            //Total Ordered Sale
            $TotalOrderedChart = DB::table($this->orderedTable)
                    ->whereYear('created_at', '=', date('Y'))
                    ->whereMonth('created_at', '=', date('m'))
                    ->whereDay('created_at', '=', $i)
                    ->where('order_status_id', '!=', 4)
                    ->first([DB::raw('SUM(total) as Total')]);
            
             //Instructor Sale
            $TotalInstructorSaleChart = DB::table($this->instructorSubscriptionTable)
                    ->where('module_id', 1)
                    ->whereYear('created_at', '=', date('Y'))
                    ->whereMonth('created_at', '=', date('m'))
                    ->whereDay('created_at', '=', $i)
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
            $TotalProfitChart = DB::table('subscribers_package_details')
                    ->whereYear('start_date', '=', date('Y'))
                    ->whereMonth('start_date', '=', date('m'))
                    ->whereDay('start_date', '=', $i)
                    ->first([DB::raw('SUM(commission) as amount')]);

            //Total Ordered Profit
            $TotalOrderedProfitChart = DB::table($this->orderedTable)
                    ->whereYear('created_at', '=', date('Y'))
                    ->whereMonth('created_at', '=', date('m'))
                    ->whereDay('created_at', '=', $i)
                    ->where('order_status_id', '!=', 4)
                    ->first([DB::raw('SUM(commission) as Total')]);
            
             //Instructor Sale
            $TotalInstructorProfitChart = DB::table($this->instructorSubscriptionTable)
                    ->where('module_id', 1)
                    ->whereYear('created_at', '=', date('Y'))
                    ->whereMonth('created_at', '=', date('m'))
                    ->whereDay('created_at', '=', $i)
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



        //Get Top Module3 Classes  
        $topM3Classes = DB::table($this->bookingTable . ' As b')
                ->join('class_master', 'b.class_master_id', '=', 'class_master.id')
                ->select('class_master.name_en', 'class_master.id', DB::raw("count('b.class_master_id') as class_count"))
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
                    ->where('b.module_id', 3)
                    ->where('b.class_master_id', $topClass->id)
                    ->groupBy('b.governorate_id')
                    ->orderBy('class_count', 'desc')
                    ->get();


            $topM3ClassArray[$topClass->name_en] = $topM3GovClass;
        }

        //Get Top Products    
        $topProducts = DB::table($this->orderProductTable . ' As op')
                ->join($this->orderedTable, 'orders.id', '=', 'op.order_id')
                ->leftJoin('products AS p', 'p.id', '=', 'op.product_id')
                ->select('p.name_en', DB::raw("SUM(op.total) as amount"), DB::raw("count(op.product_id) as sold"))
                ->where('orders.order_status_id', '!=', 4)
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
                ->where('orders.order_status_id', '!=', 4)
                ->latest('orders.created_at')
                ->limit(10)
                ->get();


        return view('admin.dashboard')
                        ->with('LogActivity', $LogActivity)
                        ->with('Trainer', $Trainer)
                        ->with('RegisteredUser', $RegisteredUser)
                        ->with('trainerPaymentDetail', $trainerPaymentDetail)
                        ->with('M1PaymentDetail', $M1PaymentDetail)
                        ->with('M2PaymentDetail', $M2PaymentDetail)
                        ->with('M3PaymentDetail', $M3PaymentDetail)
                        ->with('M4PaymentDetail', $M4PaymentDetail)
                        ->with('M1totalAmount', $M1totalAmount)
                        ->with('M1totalProfit', $M1totalProfit)
                        ->with('M2totalAmount', $M2totalAmount)
                        ->with('M2totalProfit', $M2totalProfit)
                        ->with('M4totalAmount', $M4totalAmount)
                        ->with('M4totalProfit', $M4totalProfit)
                        ->with('topBuyers', $topBuyers)
                        ->with('topVendors', $topVendors)
                        ->with('module1', $module1)
                        ->with('module2', $module2)
                        ->with('module3', $module3)
                        ->with('module4', $module4)
                        ->with('topM3ClassArray', $topM3ClassArray)
                        ->with('topM3ClassCount', $topM3ClassCount)
                        ->with('topPackages', $topPackages)
                        ->with('totalBookings', $totalBookings)
                        ->with('totalBookedClassProfit', $totalBookedClassProfit)
                        ->with('chart', $chart)
                        ->with('vendor_records_count', $vendor_records_count)
                        ->with('registered_users_records_count', $registered_users_records_count)
                        ->with('trainer_records_count', $trainer_records_count)
                        ->with('android_users_count', $android_users_count)
                        ->with('ios_users_count', $ios_users_count)
                        ->with('total_device_users', $total_device_users)
                        ->with('M4totalDeliverycharge', $M4totalDeliverycharge)
                        ->with('M4totalCoupon', $M4totalCoupon)
                        ->with('latestOrders', $latestOrders)
                        ->with('topProducts', $topProducts)
                        ->with('ViewAccess', $this->ViewAccess);
    }

}
