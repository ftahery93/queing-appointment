<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use DateTime;
use App\Models\Admin\Coupon;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;

class CouponController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:coupons');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('coupons-create');

        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('coupons-delete');

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('coupons-edit');

        $Coupon = Coupon::
                select('id', 'name_en', 'code', 'discount', 'start_date', 'end_date', 'status')
                ->get();
        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Coupon)
                            ->editColumn('name_en', function ($Coupon) {
                                return str_limit($Coupon->name_en, 15);
                            })
                            ->editColumn('start_date', function ($Coupon) {
                                $newYear = new Carbon($Coupon->start_date);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('end_date', function ($Coupon) {
                                $newYear = new Carbon($Coupon->start_date);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('id', function ($Coupon) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $Coupon->id . '">';
                            })
                            ->editColumn('action', function ($Coupon) {
                                if ($this->EditAccess)
                                    return '<a href="' . url('admin/coupons') . '/' . $Coupon->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>'
                                        . ' <a href="' . url('admin/coupons') . '/' . $Coupon->id . '/couponHistory" class="btn btn-primary tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Coupon History" data-original-title="Coupon History"><i class="entypo-book"></i></a>';
                                        
                            })
                            ->make();
        }

        return view('admin.coupons.index')
                        ->with('CreateAccess', $this->CreateAccess)
                        ->with('DeleteAccess', $this->DeleteAccess)
                        ->with('EditAccess', $this->EditAccess);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('coupons-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        //Get all Vendors
        $vendors = DB::table('vendors')
                ->select('id', 'name')
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->get();

        return view('admin.coupons.create')
                        ->with('vendors', $vendors);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        // validate
        $validator = Validator::make($request->all(), [
                    'vendor_id' => 'required',
                    'name_en' => 'required',
                    'name_ar' => 'required',
                    'type' => 'required',
                    'discount' => array('required', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
                    'start_date' => 'required|date_format:d/m/Y',
                    'end_date' => 'required|date_format:d/m/Y|after_or_equal:start_date',
                    'code' => 'required',
                    'uses_total' => 'required|numeric',
                    'uses_customer' => 'required|numeric',
        ]);

        // validation failed
        if ($validator->fails()) {

            return redirect('admin/coupons/create')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->all();

            //Change Date Format
            $datetime = new DateTime();
            $newDate = $datetime->createFromFormat('d/m/Y', $request->start_date);
            $input['start_date'] = $newDate->format('Y-m-d');

            $endDate = $datetime->createFromFormat('d/m/Y', $request->end_date);
            $input['end_date'] = $endDate->format('Y-m-d');

            Coupon::create($input);

            //LogActivity
            LogActivity::addToLog('Coupon - ' . str_limit($request->name_en, 20), 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect('admin/coupons');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('coupons-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        $Coupon = Coupon::find($id);

        //Change Date Format
        $newdate = new Carbon($Coupon->start_date);
        $Coupon->start_date = $newdate->format('d/m/Y');

        $enddate = new Carbon($Coupon->end_date);
        $Coupon->end_date = $enddate->format('d/m/Y');

        //Get all Vendors
        $vendors = DB::table('vendors')
                ->select('id', 'name')
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->get();


        // show the edit form and pass the nerd
        return View::make('admin.coupons.edit')
                        ->with('Coupon', $Coupon)
                        ->with('vendors', $vendors);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('coupons-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            $Coupon = Coupon::findOrFail($id);
            $Coupon->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $Coupon = Coupon::findOrFail($id);
        // validate
        $validator = Validator::make($request->all(), [
                    'vendor_id' => 'required',
                    'name_en' => 'required',
                    'name_ar' => 'required',
                    'type' => 'required',
                    'discount' => array('required', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
                    'start_date' => 'required|date_format:d/m/Y',
                    'end_date' => 'required|date_format:d/m/Y|after_or_equal:start_date',
                    'code' => 'required',
                    'uses_total' => 'required|numeric',
                    'uses_customer' => 'required|numeric',
        ]);
        // validation failed
        if ($validator->fails()) {

            return redirect('admin/coupons/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->all();

            //Change Date Format
            //Change Date Format
            $datetime = new DateTime();
            $newDate = $datetime->createFromFormat('d/m/Y', $request->start_date);
            $input['start_date'] = $newDate->format('Y-m-d');

            $endDate = $datetime->createFromFormat('d/m/Y', $request->end_date);
            $input['end_date'] = $endDate->format('Y-m-d');

            $Coupon->fill($input)->save();

            //LogActivity
            LogActivity::addToLog('Coupon - ' . str_limit($request->name_en, 20), 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect('admin/coupons');
        }
    }

    /**
     * Remove the Multiple resource from storage.
     *
     * @param  int  $ids
     * @return \Illuminate\Http\Response
     */
    public function destroyMany(Request $request) {

        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('coupons-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $all_data = $request->except('_token', 'table-4_length');

        //LogActivity
        //fetch title
        $Coupon = Coupon::
                        select('name_en')
                        ->whereIn('id', $all_data['ids'])
                        ->get()->map(function ($Coupon) {
            $Coupon->name_en = str_limit($Coupon->name_en, 15);
            return $Coupon;
        });
        $name = $Coupon->pluck('name_en');
        $groupname = $name->toJson();

        LogActivity::addToLog('Coupon - ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            Coupon::destroy($id);
        }

        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect('admin/coupons');
    }

    //Coupon History
    public function couponHistory(Request $request) {

        $coupon_id = $request->coupon_id;

        $Contactus = DB::table('coupon_history As ch')
                ->join('registered_users As ru', 'ch.customer_id', '=', 'ru.id')
                ->select('ch.order_id', 'ru.name AS customer_name', 'ch.amount', 'ch.created_at')
                ->get();

        //Coupon Name
        $couponName = DB::table('coupons')->select('name_en')->where('id', $coupon_id)->first();



        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Contactus)
                            ->editColumn('created_at', function ($Contactus) {
                                $newYear = new Carbon($Contactus->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->make();
        }

        return view('admin.coupons.couponHistory')
                        ->with('couponName', $couponName->name_en)
                        ->with('coupon_id', $coupon_id);
    }

}
