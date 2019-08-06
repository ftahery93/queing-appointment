<?php

namespace App\Http\Controllers\Vendor\Module2;

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
        $this->middleware('vendorPermission:M2IncomeStatistics');
        $this->configName = config('global.fitflowVendor') . config('global.storeAddress') . '/' . config('global.M2');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $this->table = VendorDetail::getPrefix() . 'subscribers_package_details';

        //Get Sale Count Start Date
        $sale_setting = DB::table('vendors')
                ->select('sale_setting')
                ->where('id', VendorDetail::getID())
                ->first();

        $SaleSetting = new Carbon($sale_setting->sale_setting);


        //Get total sale and total profit 
        $totalAmount = DB::table($this->table)
                ->where('module_id', 2)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('price');

        $totalAdminCommission = DB::table($this->table)
                ->where('module_id', 2)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('commission');

        $totalProfit = DB::table($this->table)
                ->where('module_id', 2)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                ->sum('profit');


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



        return view('fitflowVendor.module2.incomeStatistics.index')
                        ->with('totalAmount', $totalAmount)
                        ->with('totalAdminCommission', $totalAdminCommission)
                        ->with('totalProfit', $totalProfit)
                        ->with('chart', $chart);
    }

    public function ajaxchart(Request $request) {
        $this->table = VendorDetail::getPrefix() . 'subscribers_package_details';

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

                //Get total sale and total profit
                $totalAmount = DB::table($this->table)
                        ->where('module_id', 2)
                        ->whereYear('start_date', '=', $Year)
                        ->whereMonth('start_date', '=', $Month)
                        ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                        ->sum('price');

                $totalAdminCommission = DB::table($this->table)
                        ->where('module_id', 2)
                        ->whereYear('start_date', '=', $Year)
                        ->whereMonth('start_date', '=', $Month)
                        ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                        ->sum('commission');

                $totalProfit = DB::table($this->table)
                        ->where('module_id', 2)
                        ->whereYear('start_date', '=', $Year)
                        ->whereMonth('start_date', '=', $Month)
                        ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                        ->sum('profit');

                //Statistics
                //Chart 
                $month_days = cal_days_in_month(CAL_GREGORIAN, $Month, $Year);
                for ($i = 0; $i <= $month_days; $i++) {
                    $xasis[] = $i;
                    //Total Sale  
                    $TotalSaleChart = DB::table($this->table)
                            ->where('module_id', 2)
                            ->whereYear('start_date', '=', $Year)
                            ->whereMonth('start_date', '=', $Month)
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
                            ->whereYear('start_date', '=', $Year)
                            ->whereMonth('start_date', '=', $Month)
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


                $returnHTML = view('fitflowVendor.module2.incomeStatistics.ajaxchart')
                        ->with('totalAmount', $totalAmount)
                        ->with('totalAdminCommission', $totalAdminCommission)
                        ->with('totalProfit', $totalProfit)
                        ->with('chart', $chart)
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
                    //Get total sale   
                    $month_days = cal_days_in_month(CAL_GREGORIAN, $str_enddate_array[0], $str_enddate_array[1]);
                    $startdate = $str_startdate_array[1] . '-' . $str_startdate_array[0] . '-01';
                    $enddate = $str_enddate_array[1] . '-' . $str_enddate_array[0] . '-' . $month_days;
                    $exp = new Carbon($enddate);
                    $exp->addDays(1);
                    $enddate = $exp->format('Y-m-d');

                    $totalAmount = DB::table($this->table)
                            ->where('module_id', 2)
                            ->whereBetween('start_date', [$startdate, $enddate])
                            ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                            ->sum('price');

                    $totalAdminCommission = DB::table($this->table)
                            ->where('module_id', 2)
                            ->whereBetween('start_date', [$startdate, $enddate])
                            ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                            ->sum('commission');

                    $totalProfit = DB::table($this->table)
                            ->where('module_id', 2)
                            ->whereBetween('start_date', [$startdate, $enddate])
                            ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                            ->sum('profit');

                    //Chart 
                    foreach ($months as $key => $val) {
                        $xasis[] = $key . '-' . $val;
                        $TotalSaleChart = DB::table($this->table)
                                ->where('module_id', 2)
                                ->whereYear('start_date', '=', $val)
                                ->whereMonth('start_date', '=', $key)
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
                                ->whereYear('start_date', '=', $val)
                                ->whereMonth('start_date', '=', $key)
                                ->whereDate('start_date', '>=', $sale_setting->sale_setting)
                                ->first([DB::raw('SUM(profit) as amount')]);

                        if (!empty($TotalProfitChart->amount)) {
                            $TotalProfitChartAmount[] = $TotalProfitChart->amount;
                        } else {
                            $TotalProfitChartAmount[] = 0;
                        }
                    }

                    $chart = Charts::multi('line', 'highcharts')
                            ->colors(['#0fbbbd', '#ed164f'])
                            ->title('Sales Report')
                            ->elementLabel("Amount (KD)")
                            ->labels($xasis)
                            ->dataset('Total Sales', $TotalSaleChartAmount)
                            ->dataset('Total Profit', $TotalProfitChartAmount);



                    $returnHTML = view('fitflowVendor.module2.incomeStatistics.ajaxchart')
                            ->with('totalAmount', $totalAmount)
                            ->with('totalAdminCommission', $totalAdminCommission)
                            ->with('totalProfit', $totalProfit)
                            ->with('chart', $chart)
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
