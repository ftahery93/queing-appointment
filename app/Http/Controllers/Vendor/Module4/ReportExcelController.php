<?php

namespace App\Http\Controllers\Vendor\Module4;

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
        $this->configName = config('global.fitflowVendor') . config('global.storeAddress') . '/' . config('global.M4');
        //$this->middleware('vendorPermission:M4reports');
    }

    public function customerOrders(Request $request) {

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('customerOrders-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        $this->orderedTable = 'orders';
        $this->orderProductTable = 'order_product';

        $OrderList = DB::table($this->orderedTable)
                ->select('orders.name AS customer_name', 'orders.email', 'orders.mobile', DB::raw("count(orders.id) as num_orders"), DB::raw("SUM(orders.total) as total"))
                ->where('orders.order_status_id', '!=', 4)
                ->where('orders.vendor_id', VendorDetail::getID());

        if (Session::has('reportModule4customerOrders')) {
            $val = Session::get('reportModule4customerOrders');
            if (Session::has('reportModule4customerOrders.customer_name')) {
                $customer_name = $val['customer_name'];
                $OrderList->where('orders.name', 'like', "$customer_name%");
            }
        }


        $Order = $OrderList->get()->toArray();
        $OrderCount = $OrderList->sum('id');
        $OrderAmountCount = $OrderList->sum('total');

        // Define the Excel spreadsheet headers
        $tableArray[] = ['Customer Name', 'Email', 'Mobile', 'No. Orders', 'Total'];

        // Convert each member of the returned collection into an array,
        // and append it to the array.
        foreach ($Order as $table_data) {
            $tableArray[] = (array) $table_data;
        }
        $count = count($tableArray) + 1;

        $tableArray[$count]['Total Orders'] = 'Total Orders: ' . $OrderCount;
        $tableArray[$count]['Total Amount '] = 'Total Amount (' . config('global.amountCurrency') . ') ' . $OrderAmountCount;

        // Generate and return the spreadsheet
        Excel::create(ucfirst(config('global.M4')) . '  Customer Orders', function($excel) use($tableArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle(ucfirst(config('global.M4')) . ' Customer Orders');
            $excel->setCreator('Creativity')->setCompany(config('global.appTitle'));
            $excel->setDescription(ucfirst(config('global.M4')) . ' Customer Orders');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($tableArray) {
                $sheet->fromArray($tableArray, null, 'A1', false, false);
            });
        })->download('xlsx');

        return redirect($this->configName . '/report/customerOrders');
    }

    public function coupons(Request $request) {

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('reportCoupons-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        $this->orderedTable = 'orders';
        $this->orderCouponTable = 'coupon_history';

        $OrderList = DB::table($this->orderCouponTable . ' As oc')
                ->join('coupons', 'coupons.id', '=', 'oc.coupon_id')
                ->join($this->orderedTable, 'orders.id', '=', 'oc.order_id')
                ->select('coupons.name_en AS coupon_name', 'coupons.code', DB::raw("count(oc.order_id) as num_orders"), DB::raw("SUM(oc.amount) as total"))
                ->where('coupons.vendor_id', VendorDetail::getID())
                ->where('orders.order_status_id', '!=', 4);


        $Order = $OrderList->groupby('oc.coupon_id')->get();

        // Define the Excel spreadsheet headers
        $tableArray[] = ['Coupon Name', 'Code', 'No. Orders', 'Total'];

        // Convert each member of the returned collection into an array,
        // and append it to the array.
        foreach ($Order as $table_data) {
            $tableArray[] = (array) $table_data;
        }

        // Generate and return the spreadsheet
        Excel::create(ucfirst(config('global.M4')) . '  Coupons', function($excel) use($tableArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle(ucfirst(config('global.M4')) . ' Coupons');
            $excel->setCreator('Creativity')->setCompany(config('global.appTitle'));
            $excel->setDescription(ucfirst(config('global.M4')) . ' Coupons');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($tableArray) {
                $sheet->fromArray($tableArray, null, 'A1', false, false);
            });
        })->download('xlsx');

        return redirect($this->configName . '/report/coupons');
    }

    public function orderPayments(Request $request) {

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('reportOrderPayment-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        $this->orderedTable = 'orders';

        $OrderList = DB::table('payment_details AS p')
                ->join($this->orderedTable, 'orders.payment_id', '=', 'p.id')
                ->select('orders.name AS user', 'orders.id As order_id', 'p.reference_id', 'p.amount', 'p.post_date'
                        , DB::raw('(CASE WHEN orders.payment_method = 1 THEN "KNET" WHEN orders.payment_method = 2 THEN "Credit Card" ELSE "Cash On Delivery" END) AS payment_method'))
                ->where('orders.order_status_id', '!=', 4)
                ->where('orders.vendor_id', VendorDetail::getID());

        $KnetAmountList = DB::table('payment_details As p')
                ->join($this->orderedTable, 'orders.payment_id', '=', 'p.id')
                ->join('knet_payments AS k', 'k.id', '=', 'p.payid')
                ->select(DB::raw('COALESCE(SUM(k.amount),0) as knet_amount'))
                ->where('p.card_type', 1)
                ->where('orders.order_status_id', '!=', 4)
                ->where('orders.vendor_id', VendorDetail::getID());

        $CCAmountList = DB::table('payment_details As p')
                ->join($this->orderedTable, 'orders.payment_id', '=', 'p.id')
                ->join('cc_payments AS c', 'c.id', '=', 'p.payid')
                ->select(DB::raw('COALESCE(SUM(c.amount),0) as cc_amount'))
                ->where('p.card_type', 2)
                ->where('orders.order_status_id', '!=', 4)
                ->where('orders.vendor_id', VendorDetail::getID());

        $AmountList = DB::table('payment_details As p')
                ->join($this->orderedTable, 'orders.payment_id', '=', 'p.id')
                ->select(DB::raw('COALESCE(SUM(orders.total),0) as fees'))
                ->where('orders.order_status_id', '!=', 4)
                ->where('orders.vendor_id', VendorDetail::getID());

        //if Request having Date Range
        if (Session::has('reportModule4OrderPayments')) {
            $val = Session::get('reportModule4OrderPayments');
            if (Session::has('reportModule4OrderPayments.start_date')) {
                $OrderList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
                $AmountList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
                $KnetAmountList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
                $CCAmountList->whereBetween('p.post_date', [$val['start_date'], $val['end_date']]);
            }
            // if Request having Member id
            if (Session::has('reportModule4OrderPayments.customer_name')) {
                $val = Session::get('reportModule4OrderPayments');
                $customer_name = $val['customer_name'];
                $OrderList->where('orders.name', 'like', "$customer_name%");
                $AmountList->where('orders.name', 'like', "$customer_name%");
                $KnetAmountList->where('orders.name', 'like', "$customer_name%");
                $CCAmountList->where('orders.name', 'like', "$customer_name%");
            }
        }


        $Orders = $OrderList->get();
        $Amount = $AmountList->first();
        $KnetAmount = $KnetAmountList->first();
        $CCAmount = $CCAmountList->first();

// Define the Excel spreadsheet headers
        $tableArray[] = ['Customer Name', 'Order ID', 'Reference ID', 'Amount', 'Collected On', 'Payment Method'];

        // Convert each member of the returned collection into an array,
        // and append it to the array.
        foreach ($Orders as $table_data) {

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
        Excel::create(ucfirst(config('global.M4')) . ' Ordered Payments', function($excel) use($tableArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle(ucfirst(config('global.M4')) . ' Ordered Payments');
            $excel->setCreator('Creativity')->setCompany(config('global.appTitle'));
            $excel->setDescription(ucfirst(config('global.M4')) . ' Ordered Payments');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($tableArray) {
                $sheet->fromArray($tableArray, null, 'A1', false, false);
            });
        })->download('xlsx');

        return redirect($this->configName . '/report/orderPayments');
    }

    public function productPurchased(Request $request) {

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('productPurchased-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        $this->orderedTable = 'orders';
        $this->orderProductTable = 'order_product';

        $OrderList = DB::table($this->orderProductTable . ' As op')
                ->join($this->orderedTable, 'orders.id', '=', 'op.order_id')
                ->select('op.name_en AS product_name', 'op.model', DB::raw("SUM(op.quantity) as quantity"), DB::raw("SUM(op.total) as total"))
                ->where('orders.order_status_id', '!=', 4)
                ->where('orders.vendor_id', VendorDetail::getID());

        if (Session::has('reportModule4ProductPurchased')) {
            $val = Session::get('reportModule4ProductPurchased');
            if (Session::has('reportModule4ProductPurchased.product_name')) {
                $product_name = $val['product_name'];
                $OrderList->where('op.name_en', 'like', "$product_name%");
            }
        }

        $Order = $OrderList->groupby('op.product_id')->get();

        // Define the Excel spreadsheet headers
        $tableArray[] = ['Product Name', 'Model', 'Quantity', 'Total'];

        // Convert each member of the returned collection into an array,
        // and append it to the array.
        foreach ($Order as $table_data) {
            $tableArray[] = (array) $table_data;
        }

        // Generate and return the spreadsheet
        Excel::create(ucfirst(config('global.M4')) . '  Product Purchased', function($excel) use($tableArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle(ucfirst(config('global.M4')) . ' Product Purchased');
            $excel->setCreator('Creativity')->setCompany(config('global.appTitle'));
            $excel->setDescription(ucfirst(config('global.M4')) . ' Product Purchased');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($tableArray) {
                $sheet->fromArray($tableArray, null, 'A1', false, false);
            });
        })->download('xlsx');

        return redirect($this->configName . '/report/productPurchased');
    }

}
