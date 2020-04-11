<?php

namespace App\Http\Controllers\Vendor\Module2;

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
        //$this->middleware('vendorPermission:M2Dashboard');
        $this->configName = config('global.fitflowVendor') . config('global.storeAddress') . '/' . config('global.M2');
    }

    public function index() {

        $this->table = VendorDetail::getPrefix() . 'subscribers_package_details';
        $this->booking = VendorDetail::getPrefix() . 'bookings';

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('M2Dashboard-view');

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
                ->where('spd.module_id', 2)
                ->groupBy('spd.package_id')
                ->orderBy('amount', 'desc')
                ->limit(5)
                ->get();

        $topPackages = $topPackages->toArray();

        //Get Top Buyers    
        $topBuyers = DB::table($this->table . ' As spd')
                ->leftJoin('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->select('registered_users.id', 'registered_users.name', 'registered_users.profile_image', DB::raw("count('spd.subscriber_id') as subscriber_count"))
                ->whereDate('spd.start_date', '>=', $sale_setting->sale_setting)
                ->where('spd.module_id', 2)
                ->groupBy('spd.subscriber_id')
                ->orderBy('subscriber_count', 'desc')
                ->limit(16)
                ->get();


        //Get total sale and total profit and Admin Commission and subscribers     
        $totalSubscribers = DB::table($this->table)->where('module_id', 2)->whereDate('start_date', '>=', $sale_setting->sale_setting)->count(DB::raw('DISTINCT subscriber_id'));

        $totalAmount = DB::table($this->table)
                ->where('module_id', 2)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('price');

        $totalProfit = DB::table($this->table)
                ->where('module_id', 2)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('profit');

        $totalAdminCommission = DB::table($this->table)
                ->where('module_id', 2)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('commission');


        //Chart   
        $month_days = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));

        for ($i = 0; $i <= $month_days; $i++) {
            $xasis[] = $i;
            //Total Sale            
            $TotalSaleChart = DB::table($this->table)
                    ->where('module_id', 2)
                    ->whereYear('start_date', '=', date('Y'))
                    ->whereMonth('start_date', '=', date('m'))
                    ->whereDay('start_date', '=', $i)
                    ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                    ->first([DB::raw('SUM(price) as amount')]);

            if (!empty($TotalSaleChart->amount)) {
                $TotalSaleChartAmount[] = $TotalSaleChart->amount;
            } else {
                $TotalSaleChartAmount[] = 0;
            }

            //Total Profit           
            $TotalProfitChart = DB::table($this->table)
                    ->where('module_id', 2)
                    ->whereYear('start_date', '=', date('Y'))
                    ->whereMonth('start_date', '=', date('m'))
                    ->whereDay('start_date', '=', $i)
                    ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                    ->first([DB::raw('SUM(profit) as amount')]);

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
                join($this->table . ' As spd', 'spd.payment_id', '=', 'payment_details.id')
                ->leftjoin('registered_users', 'registered_users.id', '=', 'payment_details.subscriber_id')
                ->select('payment_details.amount', 'payment_details.post_date', 'spd.num_days', 'spd.name_en As package', 'spd.start_date', 'spd.end_date', 'registered_users.name AS user'
                        , DB::raw('(CASE WHEN payment_details.card_type = 1 THEN "KNET" ELSE "Credit Card" END) AS card_type'))
                ->where('payment_details.module_id', 2)
                ->where('spd.vendor_id', VendorDetail::getID())
                ->whereDate('spd.start_date', '>=', $sale_setting->sale_setting)
                ->whereNotNull('spd.payment_id')
                ->orderby('spd.id', 'DESC')
                ->get();

        //Get Top Classes  
        $topClasses = DB::table($this->booking . ' As b')
                ->join('class_master', 'b.class_master_id', '=', 'class_master.id')
                ->select('class_master.name_en', 'class_master.id', DB::raw("count('b.class_master_id') as class_count"))
                ->whereDate('b.created_at', '>=', $sale_setting->sale_setting)
                ->where('b.module_id', 2)
                ->groupBy('b.class_master_id')
                ->orderBy('class_count', 'desc')
                ->limit(5)
                ->get();

        $topClassCount = $topClasses->count();

        $topClassArray = array();

        foreach ($topClasses As $topClass) {

            //Get Top Class wise governorate  
            $topGovClass = DB::table($this->booking . ' As b')
                    ->join('class_master', 'b.class_master_id', '=', 'class_master.id')
                    ->join('governorates', 'b.governorate_id', '=', 'governorates.id')
                    ->select('governorates.name_en AS governorate', DB::raw("count('b.governorate_id') as class_count"))
                    ->whereDate('b.created_at', '>=', $sale_setting->sale_setting)
                    ->where('b.module_id', 2)
                    ->where('b.class_master_id', $topClass->id)
                    ->groupBy('b.governorate_id')
                    ->orderBy('class_count', 'desc')
                    ->get();


            $topClassArray[$topClass->name_en] = $topGovClass;
        }

        return view('fitflowVendor.module2.dashboard')
                        ->with('totalAmount', $totalAmount)
                        ->with('totalProfit', $totalProfit)
                        ->with('totalAdminCommission', $totalAdminCommission)
                        ->with('totalSubscribers', $totalSubscribers)
                        ->with('topPackages', $topPackages)
                        ->with('topBuyers', $topBuyers)
                        ->with('PaymentDetail', $PaymentDetail)
                        ->with('topClassArray', $topClassArray)
                        ->with('topClassCount', $topClassCount)
                        ->with('chart', $chart)
                        ->with('ViewAccess', $this->ViewAccess);
    }

}
