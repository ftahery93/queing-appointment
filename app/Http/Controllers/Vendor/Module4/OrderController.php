<?php

namespace App\Http\Controllers\Vendor\Module4;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Input;
use Carbon\Carbon;
use App\Models\Admin\OrderHistory;
use App\Models\Admin\Order;
use App\Models\Admin\OrderProduct;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;
use App\Helpers\VendorDetail;
use Image;

class OrderController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;
    protected $guard = 'vendor';
    protected $configName;

    public function __construct() {
        $this->middleware($this->guard);
        $this->middleware('vendorPermission:orders');
        $this->configName = config('global.fitflowVendor') . config('global.storeAddress') . '/' . config('global.M4');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
       
        $customerID=0;
        
        if($request->customer_id!='')
        $customerID=$request->customer_id;

        $OrderList = DB::table('orders')
                ->join('order_status', 'order_status.id', '=', 'orders.order_status_id')
                ->select('orders.id', 'orders.name AS customer_name', 'order_status.name_en As status', 'orders.total', 'orders.created_at', 'orders.updated_at')
                ->where('orders.vendor_id', VendorDetail::getID());


        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $OrderList->whereBetween('orders.created_at', [$start_date, $end_date]);
        }
        
        if ($request->has('customer_name') && $request->get('customer_name') != '') {
            $customer_name = $request->get('customer_name');
            $OrderList->where('orders.name', 'like', "$customer_name%");
        }
        
        if($customerID!=0){
            $OrderList->where('orders.customer_id', $customerID);
        }
        
        $Order = $OrderList->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Order)
                            ->editColumn('created_at', function ($Order) {
                                $newYear = new Carbon($Order->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('updated_at', function ($Order) {
                                $newYear = new Carbon($Order->updated_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('action', function ($Order) {
                                return '<a href="' . url($this->configName . '/order') . '/' . $Order->id . '" class="btn btn-info tooltip-primary btn-small" data-toggle="modal" data-placement="top" title="View Order" data-original-title="View Order"><i class="entypo-eye"></i></a>';
                            })
                            ->make();
        }

        return view('fitflowVendor.module4.orders.index')->with('customerID', $customerID);
    }

    public function order(Request $request) {

        $id = $request->order_id;

        //order details
        $Order = Order::
                join('vendors', 'vendors.id', '=', 'orders.vendor_id')
                ->select('vendors.name AS vendor', DB::raw('(CASE WHEN orders.payment_method = 1 THEN "KNET" WHEN orders.payment_method = 2 THEN "Credit Card" ELSE "Cash On Delivery" END) AS payment_method'), 'orders.created_at'
                        , 'orders.name AS customer_name', 'orders.email', 'orders.mobile', 'orders.address_area', 'orders.address_street', 'orders.address_house_building_num'
                        , 'orders.address_avenue', 'orders.address_floor', 'orders.address_flat', 'orders.address_block', 'orders.pick_from_store', 'orders.order_status_id')
                ->where('orders.id', $id)
                ->first();


        //produc details
        $OrderProducts = DB::table('order_product')
                ->join('products', 'products.id', '=', 'order_product.product_id')
                ->select('order_product.*', 'products.name_en')
                ->where('order_product.order_id', $id)
                ->get();

        $OrderProductsoption = array();

        foreach ($OrderProducts AS $option) {
            $OrderProductsoption[$option->id] = OrderProduct::orderproductOptionValue($option->id);
        }

        //order total
        $OrderTotal = DB::table('order_total')
                ->where('order_id', $id)
                ->first();

        //order History
        $OrderHistory = DB::table('order_history')
                ->join('order_status', 'order_status.id', '=', 'order_history.order_status_id')
                ->select('order_history.comment', 'order_status.name_en As status', DB::raw('DATE_FORMAT(order_history.created_at,"%d/%m/%Y") AS created_at'))
                ->where('order_id', $id)
                ->get();


        //order status
        $OrderStatus = DB::table('order_status')
                ->get();

        return view('fitflowVendor.module4.orders.order')
                        ->with('Order', $Order)
                        ->with('order_id', $id)
                        ->with('OrderProducts', $OrderProducts)
                        ->with('OrderTotal', $OrderTotal)
                        ->with('OrderHistory', $OrderHistory)
                        ->with('OrderStatus', $OrderStatus)
                        ->with('OrderProductsoption', $OrderProductsoption);
    }

   public function orderInvoicePrint(Request $request) {
       
       //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('orders-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        $id = $request->order_id;

        //order details
        $Order = Order::
                join('vendors', 'vendors.id', '=', 'orders.vendor_id')
                ->select('vendors.name AS vendor', DB::raw('(CASE WHEN orders.payment_method = 1 THEN "KNET" WHEN orders.payment_method = 2 THEN "Credit Card" ELSE "Cash On Delivery" END) AS payment_method'), 'orders.created_at'
                        , 'orders.name AS customer_name', 'orders.email', 'orders.mobile', 'orders.address_area', 'orders.address_street', 'orders.address_house_building_num'
                        , 'orders.address_avenue', 'orders.address_floor', 'orders.address_flat', 'orders.address_block', 'orders.pick_from_store', 'orders.invoice_no', 'orders.invoice_prefix')
                ->where('orders.id', $id)
                ->first();

        //produc details
        $OrderProducts = DB::table('order_product')
                ->join('products', 'products.id', '=', 'order_product.product_id')
                ->select('order_product.*', 'products.name_en')
                ->where('order_product.order_id', $id)
                ->get();

        $OrderProductsoption = array();
        foreach ($OrderProducts AS $option) {
            $OrderProductsoption[$option->id] = OrderProduct::orderproductOptionValue($option->id);
        }

        //order total
        $OrderTotal = DB::table('order_total')
                ->where('order_id', $id)
                ->first();


        return view('fitflowVendor.module4.orders.invoicePrint')
                        ->with('Order', $Order)
                        ->with('order_id', $id)
                        ->with('OrderProducts', $OrderProducts)
                        ->with('OrderTotal', $OrderTotal)
                        ->with('OrderProductsoption', $OrderProductsoption);
    }
}
