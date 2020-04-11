<?php

namespace App\Http\Controllers\Vendor\Module4;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use DateTime;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\VendorDetail;

class ReportController extends Controller {

    protected $guard = 'vendor';
    protected $configName;
    protected $ViewAccess;
    protected $PrintAccess;

    public function __construct() {
        $this->middleware($this->guard);
        $this->configName = config('global.fitflowVendor') . config('global.storeAddress') . '/' . config('global.M4');
        //$this->middleware('vendorPermission:M2reports');
    }

    public function customerOrders(Request $request) {

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('customerOrders-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('customerOrders-print');

        $this->orderedTable = 'orders';
        $this->orderProductTable = 'order_product';

        $OrderList = DB::table($this->orderedTable)
                ->select('orders.customer_id', 'orders.name AS customer_name', 'orders.email', 'orders.mobile', DB::raw("count(orders.id) as num_orders"), DB::raw("SUM(orders.total) as total"))
                ->where('orders.order_status_id', '!=', 4)
                ->where('orders.vendor_id', VendorDetail::getID());


        if ($request->has('customer_name') && $request->get('customer_name') != '') {
            $customer_name = $request->get('customer_name');
            Session::set('reportModule4customerOrders', ['customer_name' => $customer_name]);
            Session::flash('reportModule4customerOrders', Session::get('reportModule4customerOrders'));
            $OrderList->where('orders.name', 'like', "$customer_name%");
        }

        $Order = $OrderList->groupby('orders.customer_id')->get();
        $OrderCount = $OrderList->sum('id');
        $OrderAmountCount = $OrderList->sum('total');

        //Ajax request
        if (request()->ajax()) {
            return Datatables::of($Order)
                            ->editColumn('action', function ($Order) {
                                return '<a href="' . url($this->configName) . '/orders/' . $Order->customer_id . '" class="btn btn-primary tooltip-primary btn-small product_details" data-toggle="tooltip" data-placement="top" title="View Products" data-original-title="View Products"><i class="entypo-eye"></i></a>';
                            })
                            ->with('OrderCount', $OrderCount)
                            ->with('OrderAmountCount', $OrderAmountCount)
                            ->make();
        }


        return view('fitflowVendor.module4.reports.customerOrders')
                        ->with('PrintAccess', $this->PrintAccess)
                        ->with('OrderCount', $OrderCount)
                        ->with('OrderAmountCount', $OrderAmountCount);
    }

    public function coupons(Request $request) {

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('reportCoupons-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('reportCoupons-print');

        $this->orderedTable = 'orders';
        $this->orderCouponTable = 'coupon_history';

        $OrderList = DB::table($this->orderCouponTable . ' As oc')
                ->join('coupons', 'coupons.id', '=', 'oc.coupon_id')
                ->join($this->orderedTable, 'orders.id', '=', 'oc.order_id')
                ->select('coupons.name_en AS coupon_name', 'coupons.code', DB::raw("count(oc.order_id) as num_orders"), DB::raw("SUM(oc.amount) as total"))
                ->where('coupons.vendor_id', VendorDetail::getID())
                ->where('orders.order_status_id', '!=', 4);


        $Order = $OrderList->groupby('oc.coupon_id')->get();

        //Ajax request
        if (request()->ajax()) {
            return Datatables::of($Order)->make();
        }

        return view('fitflowVendor.module4.reports.coupons')
                        ->with('PrintAccess', $this->PrintAccess);
    }

    public function orderPayments(Request $request) {

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('reportOrderPayment-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('reportOrderPayment-print');

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

        if ($request->has('customer_name') && $request->get('customer_name') != '') {
            $customer_name = $request->get('customer_name');
            Session::set('reportModule4OrderPayments', ['customer_name' => $customer_name]);
            Session::flash('reportModule4OrderPayments', Session::get('reportModule4OrderPayments'));
            $OrderList->where('orders.name', 'like', "$customer_name%");
            $AmountList->where('orders.name', 'like', "$customer_name%");
            $KnetAmountList->where('orders.name', 'like', "$customer_name%");
            $CCAmountList->where('orders.name', 'like', "$customer_name%");
        }
        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            Session::set('reportModule4OrderPayments', ['start_date' => $start_date, 'end_date' => $end_date]);
            Session::flash('reportModule4OrderPayments', Session::get('reportModule4OrderPayments'));
            $OrderList->whereBetween('p.post_date', [$start_date, $end_date]);
            $AmountList->whereBetween('p.post_date', [$start_date, $end_date]);
            $KnetAmountList->whereBetween('p.post_date', [$start_date, $end_date]);
            $CCAmountList->whereBetween('p.post_date', [$start_date, $end_date]);
        }


        $Order = $OrderList->get();
        $Amount = $AmountList->first();
        $KnetAmount = $KnetAmountList->first();
        $CCAmount = $CCAmountList->first();

        //Ajax request
        if (request()->ajax()) {
            return Datatables::of($Order)
                            ->editColumn('post_date', function ($payments) {
                                $newYear = new Carbon($payments->post_date);
                                return $newYear->format('d/m/Y');
                            })
                            ->with('Amount', $Amount)
                            ->with('KnetAmount', $KnetAmount)
                            ->with('CCAmount', $CCAmount)
                            ->make();
        }

        return view('fitflowVendor.module4.reports.orderPayments')
                        ->with('Amount', $Amount)
                        ->with('KnetAmount', $KnetAmount)
                        ->with('CCAmount', $CCAmount)
                        ->with('PrintAccess', $this->PrintAccess);
    }

    public function productPurchased(Request $request) {

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('productPurchased-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('productPurchased-print');

        $this->orderedTable = 'orders';
        $this->orderProductTable = 'order_product';

        $OrderList = DB::table($this->orderProductTable . ' As op')
                ->join($this->orderedTable, 'orders.id', '=', 'op.order_id')
                ->select('op.name_en AS product_name', 'op.model', DB::raw("SUM(op.quantity) as quantity"), DB::raw("SUM(op.total) as total"))
                ->where('orders.order_status_id', '!=', 4)
                ->where('orders.vendor_id', VendorDetail::getID());


        if ($request->has('product_name') && $request->get('product_name') != '') {
            $product_name = $request->get('product_name');
            Session::set('reportModule4ProductPurchased', ['product_name' => $product_name]);
            Session::flash('reportModule4ProductPurchased', Session::get('reportModule4ProductPurchased'));
            $OrderList->where('op.name_en', 'like', "$product_name%");
        }

        $Order = $OrderList->groupby('op.product_id')->get();

        //Ajax request
        if (request()->ajax()) {
            return Datatables::of($Order)->make();
        }

        return view('fitflowVendor.module4.reports.productPurchased')
                        ->with('PrintAccess', $this->PrintAccess);
    }

}
