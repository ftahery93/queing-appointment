<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use DateTime;
use App\Models\Admin\OrderHistory;
use App\Models\Admin\Order;
use App\Models\Admin\OrderProduct;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;
use App\Mail\Ordered;
use Mail;

class OrderController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:orders');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $customerID = 0;

        if ($request->customer_id != '')
            $customerID = $request->customer_id;

        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('orders-delete');

        $OrderList = DB::table('orders')
                ->join('order_status', 'order_status.id', '=', 'orders.order_status_id')
                ->join('vendors', 'vendors.id', '=', 'orders.vendor_id')
                ->select('orders.id', 'vendors.name AS vendor', 'orders.name AS customer_name', 'order_status.name_en As status', 'orders.total', 'orders.created_at', 'orders.updated_at');

        if ($request->has('vendor_id') && $request->get('vendor_id') != 0) {
            $OrderList->where('orders.vendor_id', $request->vendor_id);
        }

        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $OrderList->whereBetween('orders.created_at', [$start_date, $end_date]);
        }

        if ($request->has('customer_name') && $request->get('customer_name') != '') {
            $customer_name = $request->get('customer_name');
            $OrderList->where('orders.name', 'like', "$customer_name%");
        }

        if ($customerID != 0) {
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
                                return '<a href="' . url('admin/order') . '/' . $Order->id . '" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="View Order" data-original-title="View Order"><i class="entypo-eye"></i></a>';
                            })
//                            ->editColumn('id', function ($LanguageManagement) {
//                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $LanguageManagement->id . '">';
//                            })
                            ->make();
        }

        //Vendor List
        $Vendors = DB::table('vendors')
                ->select('name', 'id')
                ->whereNull('vendors.deleted_at')
                ->get();

        return view('admin.orders.index')
                        ->with('DeleteAccess', $this->DeleteAccess)
                        ->with('Vendors', $Vendors)
                        ->with('customerID', $customerID);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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

        // $OrderProducts = OrderProduct::find(2)->orderproductOption()->where('order_id', $id)->get();
//         foreach ($OrderProducts->productOption['1'] AS $option) {
//          dd($option->option_name_en);
//         }
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

        return view('admin.orders.order')
                        ->with('Order', $Order)
                        ->with('order_id', $id)
                        ->with('OrderProducts', $OrderProducts)
                        ->with('OrderTotal', $OrderTotal)
                        ->with('OrderHistory', $OrderHistory)
                        ->with('OrderStatus', $OrderStatus)
                        ->with('OrderProductsoption', $OrderProductsoption);
    }

    public function orderHistory(Request $request) {

        $id = $request->order_id;

        //Check Create Access Permission
        $this->EditAccess = Permit::AccessPermission('orders-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');


        // validate
        $validator = Validator::make($request->all(), [
                    'order_status_id' => 'required',
        ]);

        // validation failed
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        } else {
            $input = $request->all();
            $input['order_id'] = $id;
            OrderHistory::create($input);
            DB::table('orders')->where('id', $id)->update(['order_status_id' => $request->order_status_id, 'updated_at' => Carbon::now()->format('Y-m-d H:i:s')]);
            if ($request->order_status_id == 4) {
                //produc details
                $OrderProducts = DB::table('order_product')
                        ->select('product_id', 'quantity')
                        ->where('order_id', $id)
                        ->get();

                //product option details
                $OrderProductOptions = DB::table('order_product_option')
                        ->select('product_option_value_id', 'quantity')
                        ->where('order_id', $id)
                        ->get();


                foreach ($OrderProducts AS $OrderProduct) {
                    //get qunatity
                    $Product = DB::table('products')
                            ->select('quantity')
                            ->where('id', $OrderProduct->product_id)
                            ->first();
                    $quantity = $OrderProduct->quantity + $Product->quantity;
                    DB::table('products')->where('id', 1)->update(['quantity' => $quantity, 'updated_at' => Carbon::now()->format('Y-m-d H:i:s')]);
                }

                foreach ($OrderProductOptions AS $OrderProductOption) {
                    //get qunatity
                    $ProductOption = DB::table('product_option_value')
                            ->select('quantity')
                            ->where('product_option_value_id', $OrderProductOption->product_option_value_id)
                            ->first();
                    $quantity = $OrderProductOption->quantity + $ProductOption->quantity;
                    DB::table('product_option_value')->where('product_option_value_id', 1)->update(['quantity' => $quantity, 'updated_at' => Carbon::now()->format('Y-m-d H:i:s')]);
                }
            }


            //order details
            $Order = Order::
                    join('vendors', 'vendors.id', '=', 'orders.vendor_id')
                    ->select('vendors.name AS vendor', DB::raw('(CASE WHEN orders.payment_method = 1 THEN "KNET" WHEN orders.payment_method = 2 THEN "Credit Card" ELSE "Cash On Delivery" END) AS payment_method')
                            , 'orders.created_at', 'orders.name AS customer_name', 'orders.email', 'orders.mobile', 'orders.address_area', 'orders.address_street', 'orders.address_house_building_num'
                            , 'orders.address_avenue', 'orders.address_floor', 'orders.address_flat', 'orders.address_block', 'orders.pick_from_store', 'orders.invoice_no', 'orders.invoice_prefix', 'orders.id')
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


            $Order->OrderProducts = $OrderProducts;
            $Order->OrderProductsoption = $OrderProductsoption;
            $Order->OrderTotal = $OrderTotal;

            //Mail Sent ordered status completed
            if ($request->order_status_id == 3) {
                Mail::to($Order->email)->send(new Ordered($Order));
            }

            //order History
            $OrderHistory = DB::table('order_history')
                    ->join('order_status', 'order_status.id', '=', 'order_history.order_status_id')
                    ->select('order_history.comment', 'order_status.name_en As status', DB::raw('DATE_FORMAT(order_history.created_at,"%d/%m/%Y") AS created_at'))
                    ->where('order_id', $id)
                    ->get();

            //LogActivity
            LogActivity::addToLog('Order Status for #' . $id, 'updated');
            $returnHTML = view('admin.orders.ajaxOrderHistory')->with('OrderHistory', $OrderHistory)->render();
            return response()->json(['response' => config('global.updatedOrder'), 'html' => $returnHTML]);
        }
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


        return view('admin.orders.invoicePrint')
                        ->with('Order', $Order)
                        ->with('order_id', $id)
                        ->with('OrderProducts', $OrderProducts)
                        ->with('OrderTotal', $OrderTotal)
                        ->with('OrderProductsoption', $OrderProductsoption);
    }

}
