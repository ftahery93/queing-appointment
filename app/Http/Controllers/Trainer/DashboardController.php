<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Requests;
use App\Models\Trainer\Trainer;
use App\Models\Admin\RegisteredUser;
use App\Models\Admin\PaymentDetail;
use App\Models\Admin\Transaction;
use DB;
use Charts;

class DashboardController extends Controller {

    use AuthenticatesUsers;

    public function __construct() {
        $this->middleware('trainer');
    }

    public function index() {

        //Get Latest Registered Users 
        $RegisteredUser =  DB::table('trainer_subscribers_package_details AS spd')
                ->leftJoin('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->select('registered_users.name', 'registered_users.profile_image')
                ->groupBy('spd.subscriber_id')
                ->where('spd.trainer_id', Auth::guard('trainer')->user()->id)
                ->whereNotNull('spd.subscriber_id')
                ->limit(16)
                ->get();
        
        //Get Top Buyers  
        $topBuyers = DB::table('trainer_subscribers_package_details AS spd')
                ->leftJoin('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->select('registered_users.id', 'registered_users.name', 'registered_users.profile_image', DB::raw("count('registered_users.id') as subscriber_count"))
                ->groupBy('spd.subscriber_id')
                ->orderBy('subscriber_count', 'desc')
                ->where('spd.trainer_id', Auth::guard('trainer')->user()->id)
                 ->whereNotNull('spd.subscriber_id')
                ->limit(16)
                ->get();


        //Get Latest Payments 
        $PaymentDetail = PaymentDetail::
                select('payment_details.reference_id', 'payment_details.amount', 'payment_details.post_date',  'payment_details.result', 'spd.num_points', 'spd.name_en As package', 'spd.start_date', 'spd.end_date', 'registered_users.name AS user'
                        , DB::raw('(CASE WHEN payment_details.card_type = 1 THEN "KNET" ELSE "Credit Card" END) AS card_type'))
                ->join('trainer_subscribers_package_details AS spd', 'spd.payment_id', '=', 'payment_details.id')
                ->leftjoin('registered_users', 'registered_users.id', '=', 'payment_details.subscriber_id')
                ->where('payment_details.trainer_id', Auth::guard('trainer')->user()->id)
                ->where('payment_details.module_id', 1)
                ->WhereNull('payment_details.vendor_id')
                ->get();

        $totalAmount = DB::table('trainer_subscribers_package_details')
                ->where('module_id', 1)
                ->where('trainer_id', Auth::guard('trainer')->user()->id)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->sum('price');

        $totalProfit = DB::table('trainer_subscribers_package_details')
                ->where('module_id', 1)
                ->where('trainer_id', Auth::guard('trainer')->user()->id)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->sum('profit');

        $totalAdminCommission = DB::table('trainer_subscribers_package_details')
                ->where('module_id', 1)
                ->where('trainer_id', Auth::guard('trainer')->user()->id)
                ->whereYear('start_date', '=', date('Y'))
                ->whereMonth('start_date', '=', date('m'))
                ->sum('commission');


        //Most Selling Packages
        $topPackages = DB::table('trainer_subscribers_package_details AS spd')
                ->leftJoin('trainer_packages', 'spd.package_id', '=', 'trainer_packages.id')
                ->select('trainer_packages.id', 'spd.name_en', 'spd.price', DB::raw("count('spd.package_id') as sold"), DB::raw("SUM(spd.price) as amount"))
                ->groupBy('spd.package_id')
                ->orderBy('amount', 'desc')
                ->where('spd.trainer_id', Auth::guard('trainer')->user()->id)
                ->get();

        $topPackages = $topPackages->toArray();

        //Chart   
        $month_days = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));

        for ($i = 0; $i <= $month_days; $i++) {
            $xasis[] = $i;
            //Total Sale            
            $TotalSaleChart = DB::table('trainer_subscribers_package_details')
                    ->whereYear('start_date', '=', date('Y'))
                    ->whereMonth('start_date', '=', date('m'))
                    ->whereDay('start_date', '=', $i)
                    ->where('module_id', 1)
                    ->where('trainer_id', Auth::guard('trainer')->user()->id)
                    ->first([DB::raw('SUM(price) as amount')]);

            if (!empty($TotalSaleChart->amount)) {
                $TotalSaleChartAmount[] = $TotalSaleChart->amount;
            } else {
                $TotalSaleChartAmount[] = 0;
            }

            //Total Profit           
            $TotalProfitChart = DB::table('trainer_subscribers_package_details')
                    ->where('module_id', 1)
                    ->whereYear('start_date', '=', date('Y'))
                    ->whereMonth('start_date', '=', date('m'))
                    ->whereDay('start_date', '=', $i)
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


        return view('trainer.dashboard')
                        ->with('RegisteredUser', $RegisteredUser)
                        ->with('PaymentDetail', $PaymentDetail)
                        ->with('totalAmount', $totalAmount)
                        ->with('totalProfit', $totalProfit)
                        ->with('totalAdminCommission', $totalAdminCommission)
                        ->with('topPackages', $topPackages)
                        ->with('topBuyers', $topBuyers)
                        ->with('chart', $chart);
    }

}
