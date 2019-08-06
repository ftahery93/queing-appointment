<?php

namespace App\Http\Controllers\Vendor\Module4;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Admin\Vendor;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Permit;
use App\Helpers\VendorDetail;
use App\Models\Admin\RegisteredUser;
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
        //$this->middleware('vendorPermission:M4Dashboard');
        $this->configName = config('global.fitflowVendor') . config('global.storeAddress') . '/' . config('global.M4');
    }

    public function index() {

        $this->orderedTable = 'orders';
        $this->orderProductTable = 'order_product';

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('M4Dashboard-view');

        //Get Sale Count Start Date
        $sale_setting = DB::table('vendors')
                ->select('sale_setting')
                ->where('id', VendorDetail::getID())
                ->first();

        $SaleSetting = new Carbon($sale_setting->sale_setting);

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



        //Get total sale and total profit and Admin Commission and subscribers     
        $totalSubscribers = DB::table($this->orderedTable)
                ->where('vendor_id', VendorDetail::getID())
                ->where('order_status_id', '!=', 4)
                ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                ->count(DB::raw('DISTINCT customer_id'));

        $totalOrders = DB::table($this->orderedTable)
                ->where('vendor_id', VendorDetail::getID())
                ->where('order_status_id', '!=', 4)
                ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                ->count();

        $totalAmount = DB::table($this->orderedTable)
                ->whereYear('created_at', '=', date('Y'))
                ->whereMonth('created_at', '=', date('m'))
                ->where('order_status_id', '!=', 4)
                ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                ->where('vendor_id', VendorDetail::getID())
                ->sum('total');

        $totalProfit = DB::table($this->orderedTable)
                ->whereYear('created_at', '=', date('Y'))
                ->whereMonth('created_at', '=', date('m'))
                ->where('order_status_id', '!=', 4)
                ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                ->where('vendor_id', VendorDetail::getID())
                ->sum('profit');

        $totalAdminCommission = DB::table($this->orderedTable)
                ->whereYear('created_at', '=', date('Y'))
                ->whereMonth('created_at', '=', date('m'))
                ->where('order_status_id', '!=', 4)
                ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                ->where('vendor_id', VendorDetail::getID())
                ->sum('commission');


        //Chart   
        $month_days = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));

        for ($i = 0; $i <= $month_days; $i++) {
            $xasis[] = $i;
            //Total Sale            
            $TotalSaleChart = DB::table($this->orderedTable)
                    ->whereYear('created_at', '=', date('Y'))
                    ->whereMonth('created_at', '=', date('m'))
                    ->whereDay('created_at', '=', $i)
                    ->where('order_status_id', '!=', 4)
                    ->where('vendor_id', VendorDetail::getID())
                    ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                    ->first([DB::raw('SUM(total) as Total')]);

            if (!empty($TotalSaleChart->amount)) {
                $TotalSaleChartAmount[] = $TotalSaleChart->amount;
            } else {
                $TotalSaleChartAmount[] = 0;
            }

            //Total Profit           
            $TotalProfitChart = DB::table($this->orderedTable)
                    ->whereYear('created_at', '=', date('Y'))
                    ->whereMonth('created_at', '=', date('m'))
                    ->whereDay('created_at', '=', $i)
                    ->where('order_status_id', '!=', 4)
                    ->where('vendor_id', VendorDetail::getID())
                    ->whereDate('created_at', '>=', $sale_setting->sale_setting)
                    ->first([DB::raw('SUM(profit) as Total')]);

            if (!empty($TotalProfitChart->amount)) {
                $TotalProfitChartAmount[] = $TotalProfitChart->amount;
            } else {
                $TotalProfitChartAmount[] = 0;
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


        $PaymentDetail = PaymentDetail::
                select('payment_details.amount', 'payment_details.post_date', 'orders.name AS user', 'orders.id', 'order_product.name_en'
                        , DB::raw('(CASE WHEN orders.payment_method = 1 THEN "KNET" WHEN orders.payment_method = 2 THEN "Credit Card" ELSE "Cash On Delivery" END) AS payment_method'))
                ->join($this->orderedTable, 'orders.payment_id', '=', 'payment_details.id')
                ->join('order_product', 'orders.id', '=', 'order_product.order_id')
                ->where('orders.order_status_id', '!=', 4)
                ->where('orders.vendor_id', VendorDetail::getID())
                ->orderby('orders.id', 'DESC')
                ->get();

        
        return view('fitflowVendor.module4.dashboard')
                        ->with('totalAmount', $totalAmount)
                        ->with('totalProfit', $totalProfit)
                        ->with('totalAdminCommission', $totalAdminCommission)
                        ->with('totalSubscribers', $totalSubscribers)
                        ->with('PaymentDetail', $PaymentDetail)
                        ->with('chart', $chart)
                        ->with('latestOrders', $latestOrders)
                        ->with('topProducts', $topProducts)
                        ->with('totalOrders', $totalOrders)
                        ->with('ViewAccess', $this->ViewAccess);
    }

}
