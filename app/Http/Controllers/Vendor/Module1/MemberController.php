<?php

namespace App\Http\Controllers\Vendor\Module1;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Input;
use Carbon\Carbon;
use DateTime;
use App\Models\Vendor\Member;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Permit;
use App\Helpers\LogActivity;
use App\Helpers\VendorDetail;
use App\Mail\InvoiceEmail;
use Mail;

class MemberController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;
    protected $guard = 'vendor';
    protected $configName;
    protected $table;
    protected $packagetable;

    public function __construct() {
        $this->middleware($this->guard);
        $this->middleware('vendorPermission:members');
        $this->configName = config('global.fitflowVendor') . config('global.storeAddress') . '/' . config('global.M1');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        Session::forget('excel_key');

        $this->table = VendorDetail::getPrefix() . 'members';
        $this->packagetable = VendorDetail::getPrefix() . 'subscribers_package_details';
        $this->invoiceTable = VendorDetail::getPrefix() . 'member_invoices';
        $this->instructorSubscriptionTable = 'instructor_subscribers_package_details';
        $this->instructorInvoiceTable = 'instructor_member_invoices';

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('members-create');

//Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('members-delete');

//Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('members-edit');


        $Member = DB::table($this->table . ' As m')
                ->leftjoin($this->invoiceTable . ' As inv', 'inv.member_id', '=', 'm.id')
                ->leftjoin($this->instructorSubscriptionTable . ' As ins', 'ins.member_id', '=', 'm.id')
                ->leftjoin($this->instructorInvoiceTable . ' As insInv', 'insInv.member_id', '=', 'm.id')
                ->leftjoin('gender_types As g', 'g.id', '=', 'm.gender_id')
                ->select('m.id', 'm.name', 'm.email', 'm.mobile', 'g.name_en AS gender_name', 'm.package_name', 'm.start_date', 'm.end_date', 'm.status', 'm.subscribed_from', 'inv.id As invoice_id', 'ins.num_points As sessions', 'ins.num_booked As session_booked'
                        , 'insInv.id As session_invoice_id')
                ->whereNull('m.deleted_at')
                ->groupby('m.id');

        //->havingRaw('MAX(spd.end_date) >= NOW()');
        // if Request having Pacakge name
        if ($request->has('name_en') && $request->get('name_en') != '') {
            $name_en = $request->get('name_en');
            $Member->where('m.package_name', 'like', "$name_en%");
        }
        // if Member Type //GYM:0, Fitflow:1
        if ($request->has('member_type') && $request->get('member_type') != '') {
            $member_type = $request->get('member_type');
            $Member->where('m.subscribed_from', $member_type);
        }
        // Subscription New:0, Renew:1
        if ($request->has('subscription') && $request->get('subscription') != '') {
            $subscription = $request->get('subscription');
            $Member->where('m.subscription', $subscription);
        }
        // if Member Status //1Week:0, 2Week:1, #Week:2
        if ($request->has('member_status') && $request->get('member_status') != '') {
            $member_status = $request->get('member_status');
            $current = Carbon::now();
            $expiry = $current->addWeek($member_status);
            $expiry = $expiry->format('Y-m-d');
            $currentDate = Carbon::now()->format('Y-m-d');

            $Member->whereBetween('m.end_date', [$currentDate, $expiry]);
        }
        // if Request having Gender id
        if ($request->has('gender_id') && $request->get('gender_id') != 0) {
            $GenderID = $request->get('gender_id');
            $Member->where('m.gender_id', 'like', "$GenderID%");
        }
        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $Member->whereBetween('m.end_date', [$start_date, $end_date]);
        }

        $Members = $Member->get();

        //Get All Packages 
        $Packages = DB::table('vendor_packages')
                ->select('id', 'name_en')
                ->where('vendor_id', VendorDetail::getID())
                ->get();

        //Get All Instructor Packages 
        $instructorPackages = DB::table('instructor_packages')
                ->select('id', 'name_en', 'price')
                ->where('vendor_id', VendorDetail::getID())
                ->get();

        //Get All Genders 
        $Genders = DB::table('gender_types')
                ->select('id', 'name_en')
                ->limit(2)
                ->get();



        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Members)
                            ->editColumn('end_date', function ($Members) {
                                $newYear = new Carbon($Members->start_date);
                                $endYear = new Carbon($Members->end_date);
                                return $newYear->format('d/m/Y') . ' - ' . $endYear->format('d/m/Y');
                            })
                            ->editColumn('status', function ($Members) {
                                return $Members->status == 1 ? '<div class="label label-success status" sid="' . $Members->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary status"  sid="' . $Members->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
//                            ->editColumn('gender_name', function ($Members) {
//                                return is_string($Members->gender_name)?$Members->gender_name:$Members->gender;
//                            })
//                            ->editColumn('package', function ($Members) {
//                                return is_string($Members->package)?$Members->package:$Members->package_name;
//                            })
                            ->editColumn('id', function ($Members) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $Members->id . '">';
                            })
                            ->editColumn('action', function ($Members) {
                                $str = '';
                                $edate = new Carbon($Members->end_date);
                                $edate->addDays(1);
                                $edate = $edate->format('d/m/Y');
                                if ($this->EditAccess) {
                                    $today = Carbon::yesterday();
                                    if ($today > $Members->end_date) {
                                        $edate = new Carbon();
                                        $edate = $edate->format('d/m/Y');
                                    }

                                    $str .= '<a href="' . url($this->configName . '/members') . '/' . $Members->id . '/edit" class="btn btn-info tooltip-primary btn-small edit" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>'
                                            . ' <a href="' . url($this->configName) . '/' . $Members->id . '/packageHistory" class="btn btn-orange tooltip-primary btn-small package_history" data-toggle="tooltip"  data-original-title="Package History" title="Package History"><i class="entypo-bag"></i></a>'
                                            . ' <a data-val="' . $Members->id . '" enddate="' . $edate . '" href="#myModal2" class="btn btn-success tooltip-success btn-small renew_package" data-toggle="modal"  data-original-title="Renew Package" title="Renew Package"><i class="fa fa-refresh"></i></a>';


                                    if ($Members->sessions == $Members->session_booked) {
                                        $str .= ' <a data-val="' . $Members->id . '"  href="#instructorModal" class="btn btn-danger tooltip-success btn-small instructor_package" data-toggle="modal"  data-original-title="Instructor Subscription" title="Instructor Subscription"><i class="entypo-plus"></i></a>';
                                    }

                                    if ($Members->invoice_id) {
                                        $str .= ' <a data-val="' . $Members->id . '" href="#myModal" class="btn btn-success tooltip-primary btn-small member_invoice" data-toggle="modal"  data-original-title="Invoice" title="Invoice"><i class="entypo-docs"></i></a>'
                                                . ' <a data-id="' . $Members->id . '" class="btn btn-orange tooltip-primary btn-small  sendInvoice" data-toggle="tooltip" data-placement="top" title="Send Invoice Detail" data-original-title="Send Invoice Detail"><i class="entypo-mail"></i></a>';
                                    }
                                    if ($Members->session_invoice_id) {
                                        $str .= ' <a data-val="' . $Members->id . '" href="#instructorInvoice" class="btn btn-primary tooltip-primary btn-small instructor_invoice" data-toggle="modal"  data-original-title="Instructor Subscription Invoice" title="Instructor Subscription Invoice"><i class="entypo-doc-text"></i></a>';
                                    }
                                    return $str;
                                    //} else {
//                                    $str .= '<a href="' . url($this->configName . '/members') . '/' . $Members->id . '/edit" class="btn btn-info tooltip-primary btn-small edit" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>'
//                                            . ' <a href="' . url($this->configName) . '/' . $Members->id . '/packageHistory" class="btn btn-orange tooltip-primary btn-small package_history" data-toggle="tooltip"  data-original-title="Package History" title="Package History"><i class="entypo-bag"></i></a>'
//                                            . ' <a data-val="' . $Members->id . '" enddate="' . $edate . '" href="#myModal2" class="btn btn-success tooltip-success btn-small renew_package" data-toggle="modal"  data-original-title="Renew Package" title="Renew Package"><i class="fa fa-refresh"></i></a>';
//                                    if ($Members->invoice_id) {
//                                        $str .= ' <a data-val="' . $Members->id . '" href="#myModal" class="btn btn-success tooltip-primary btn-small member_invoice" data-toggle="modal"  data-original-title="Invoice" title="Invoice"><i class="entypo-docs"></i></a>'
//                                                . ' <a data-id="' . $Members->id . '" class="btn btn-orange tooltip-primary btn-small  sendInvoice" data-toggle="tooltip" data-placement="top" title="Send Invoice Detail" data-original-title="Send Invoice Detail"><i class="entypo-mail"></i></a>';
//                                        // }
//                                        return $str;
//                                    }
                                }
                            })
                            ->make();
        }

        return view('fitflowVendor.module1.members.index')
                        ->with('CreateAccess', $this->CreateAccess)
                        ->with('DeleteAccess', $this->DeleteAccess)
                        ->with('EditAccess', $this->EditAccess)
                        ->with('Genders', $Genders)
                        ->with('Packages', $Packages)
                        ->with('instructorPackages', $instructorPackages);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $this->table = VendorDetail::getPrefix() . 'members';

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('members-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        //Get all Gender Type
        $gender_types = DB::table('gender_types')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->limit(2)
                ->get();

        //Get all Areas
        $areas = DB::table('areas')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();

        //Get all Pacakges
        $packages = DB::table('vendor_packages')
                ->select('id', 'name_en')
                ->where('vendor_id', VendorDetail::getID())
                ->where('status', 1)
                ->get();

        return view('fitflowVendor.module1.members.create')
                        ->with('packages', $packages)
                        ->with('areas', $areas)
                        ->with('gender_types', $gender_types);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $this->table = VendorDetail::getPrefix() . 'members';

        //custom message
        $messages = [
            'cash.required' => config('global.paymentValidate'),
        ];

        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|unique:' . $this->table,
                    'mobile' => 'required|digits:8|unique:' . $this->table,
                    'package_id' => 'required|numeric',
                    'start_date' => 'required|date_format:d/m/Y',
                    //'end_date' => 'required|date_format:d/m/Y',
                    'dob' => 'required|date_format:d/m/Y|before_or_equal:' . Carbon::now(),
                    'area_id' => 'required',
                    'gender_id' => 'required',
                    'cash' => array('required_without:knet', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
                    'knet' => array('required_without:cash', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
        ]);

        // validation failed
        if ($validator->fails()) {
            return redirect($this->configName . '/members/create')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->only(['name', 'email', 'mobile', 'dob', 'area_id', 'gender_id', 'package_id', 'start_date', 'cash', 'knet']);

            //Change Date Format
            $datetime = new DateTime();
            $newDate = $datetime->createFromFormat('d/m/Y', $request->start_date);
            $input['start_date'] = $newDate->format('Y-m-d');

            //Package Detail
            $packageID = DB::table('vendor_packages')->where('id', $input['package_id'])->where('vendor_id', VendorDetail::getID())->first();
            $input['package_name'] = $packageID->name_en;
            $exp = new Carbon($input['start_date']);
            $exp->addDays($packageID->num_days);
            $input['end_date'] = $exp->format('Y-m-d');

            //Set notification date
            $notify = new Carbon($input['end_date']);
            $notify->subDays($packageID->expired_notify_duration);
            $input['notification_date'] = $notify->format('Y-m-d');

            $input['package_name_ar'] = $packageID->name_ar;

//            $endDate = $datetime->createFromFormat('d/m/Y', $request->end_date);
//            $input['end_date'] = $endDate->format('Y-m-d');

            $dob = $datetime->createFromFormat('d/m/Y', $request->dob);
            $input['dob'] = $dob->format('Y-m-d');
            $input['created_at'] = Carbon::now();
            $input['updated_at'] = Carbon::now();
            $input['price'] = $packageID->price;

//            $Member = new Member;
//            $Member->setTable($this->table);
//            $Member->fill($input)->save();

            $lastID = DB::table($this->table)->insertGetId($input);

            $sale_setting = VendorDetail::getSalesCountDate();


            // check package start date is greater than or equal to vendor sales count date if true then insert into subscriber package table.
            if ($input['start_date'] >= $sale_setting) {

                $package_array['member_id'] = $lastID;
                $package_array['module_id'] = 1;
                $package_array['vendor_id'] = VendorDetail::getID();
                $package_array['package_id'] = $input['package_id'];
                $package_array['start_date'] = $input['start_date'];
                $package_array['end_date'] = $input['end_date'];
                $package_array['name_en'] = $packageID->name_en;
                $package_array['name_ar'] = $packageID->name_ar;
                $package_array['description_en'] = $packageID->description_en;
                $package_array['description_ar'] = $packageID->description_ar;
                $package_array['area_name_en'] = VendorDetail::getArea(1, $input['package_id']);
                $package_array['area_name_ar'] = VendorDetail::getArea(2, $input['package_id']);
                $package_array['num_days'] = $packageID->num_days;
                $package_array['price'] = $packageID->price;
                $package_array['profit'] = VendorDetail::getProfitCommission($packageID->price, 0);
                $package_array['commission'] = VendorDetail::getProfitCommission($packageID->price, 1);
                $package_array['cash'] = $request->cash;
                $package_array['knet'] = $request->knet;
                $exp = new Carbon($input['end_date']);
                $exp->subDays($packageID->expired_notify_duration);
                $package_array['notification_date'] = $exp->format('Y-m-d');
                $package_array['created_at'] = Carbon::now();
                $package_array['updated_at'] = Carbon::now();

                $subscription_table = VendorDetail::getPrefix() . 'subscribers_package_details';
                $admin_subscription_table = 'subscribers_package_details';
                $vendor_package_reference_id = DB::table($admin_subscription_table)->insertGetId($package_array);
                $package_array['vendor_package_reference_id'] = $vendor_package_reference_id;
                $subscriberedLastID = DB::table($subscription_table)->insertGetId($package_array);


                $dt = Carbon::now();
                $invoice_array['created_at'] = Carbon::now();
                $invoice_array['updated_at'] = Carbon::now();
                $invoice_array['member_id'] = $lastID;
                $invoice_array['receipt_num'] = $dt->year . $dt->month . $dt->day . $dt->hour . $lastID;
                $invoice_array['subscribed_package_id'] = $subscriberedLastID;
                $invoice_array['collected_by'] = Auth::guard('vendor')->user()->id;
                $invoice_array['cash'] = $request->cash;
                $invoice_array['knet'] = $request->knet;
                $invoice_array['price'] = $packageID->price;
                $invoice_array['package_id'] = $input['package_id'];
                $invoice_array['start_date'] = $input['start_date'];
                $invoice_array['end_date'] = $input['end_date'];
                $invoice_array['package_name'] = $packageID->name_en;

                //Invoice Table
                $invoice_table = VendorDetail::getPrefix() . 'member_invoices';
                DB::table($invoice_table)->insert($invoice_array);
            }


            //logActivity
            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Member - ' . $request->name, 'created');
            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Member - ' . $request->name, 'created package');

            Session::flash('message', config('global.addedRecords'));

            return redirect($this->configName . '/members');
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
    public function edit(Request $request, $id) {
        $id = $request->id;

        $this->table = VendorDetail::getPrefix() . 'members';
        $this->packagetable = VendorDetail::getPrefix() . 'subscribers_package_details';

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('members-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Check Edit Current Package Permission
        $currentPackagePermission = Permit::AccessPermission('memberCurrentPackage-edit');


        $Members = DB::table($this->table)->find($id);

        //Change Date Format
        $newdate = new Carbon($Members->start_date);
        $Members->start_date = $newdate->format('d/m/Y');

        $enddate = new Carbon($Members->end_date);
        $Members->end_date = $enddate->format('d/m/Y');

        $dob = new Carbon($Members->dob);
        $Members->dob = $dob->format('d/m/Y');


        $Members->price = $Members->price;
        $Members->cash = $Members->cash;
        $Members->knet = $Members->knet;


        //Get all Gender Type
        $gender_types = DB::table('gender_types')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->limit(2)
                ->get();

        //Get all Areas
        $areas = DB::table('areas')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();

        //Get all Pacakges
        $packages = DB::table('vendor_packages')
                ->select('id', 'name_en')
                ->where('vendor_id', VendorDetail::getID())
                ->where('status', 1)
                ->get();

        // show the edit form and pass the nerd
        return View::make('fitflowVendor.module1.members.edit')
                        ->with('packages', $packages)
                        ->with('areas', $areas)
                        ->with('gender_types', $gender_types)
                        ->with('Members', $Members)
                        ->with('currentPackagePermission', $currentPackagePermission);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $id = $request->id;

        $this->table = VendorDetail::getPrefix() . 'members';

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('members-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            DB::table($this->table)->where('id', $id)->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }

        //Check Edit Current Package Permission
        $currentPackagePermission = Permit::AccessPermission('memberCurrentPackage-edit');

        // Initiate Class
        $Member = new Member;
        $Member->setTable($this->table);

        //custom message
        $messages = [
            'cash.required' => config('global.paymentValidate'),
        ];

        //Check Current Package Edit Permission
        if ($currentPackagePermission == 1) {
            $validator = Validator::make($request->all(), [
                        'name' => 'required',
                        'email' => 'required|email|unique:' . $this->table . ',email,' . $id,
                        'mobile' => 'required|digits:8|unique:' . $this->table . ',mobile,' . $id,
                        'package_id' => 'required|numeric',
                        'start_date' => 'required|date_format:d/m/Y',
                        //'end_date' => 'required|date_format:d/m/Y',
                        'dob' => 'required|date_format:d/m/Y|before_or_equal:' . Carbon::now(),
                        'area_id' => 'required',
                        'gender_id' => 'required',
                        'cash' => array('required_without:knet', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
                        'knet' => array('required_without:cash', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ]);
        } else {
            $validator = Validator::make($request->only(['name', 'email', 'mobile', 'dob', 'area_id', 'gender_id']), [
                        'name' => 'required',
                        'email' => 'required|email|unique:' . $this->table . ',email,' . $id,
                        'mobile' => 'required|digits:8|unique:' . $this->table . ',mobile,' . $id,
                        'dob' => 'required|date_format:d/m/Y|before_or_equal:' . Carbon::now(),
                        'area_id' => 'required',
                        'gender_id' => 'required',
            ]);
        }

        // validation failed
        if ($validator->fails()) {
            return redirect($this->configName . '/members/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->only(['name', 'email', 'mobile', 'dob', 'area_id', 'gender_id', 'package_id', 'start_date', 'cash', 'knet']);

            //Check Current Package Edit Permission
            if ($currentPackagePermission == 1) {

                $input['package_id'] = $request->package_id;

                //Change Date Format
                $datetime = new DateTime();
                $newDate = $datetime->createFromFormat('d/m/Y', $request->start_date);
                $input['start_date'] = $newDate->format('Y-m-d');

                //Package Detail
                $packageID = DB::table('vendor_packages')->where('id', $input['package_id'])->where('vendor_id', VendorDetail::getID())->first();
                $input['package_name'] = $packageID->name_en;
                $exp = new Carbon($input['start_date']);
                $exp->addDays($packageID->num_days);
                $input['end_date'] = $exp->format('Y-m-d');

                //Set notification date
                $notify = new Carbon($input['end_date']);
                $notify->subDays($packageID->expired_notify_duration);
                $input['notification_date'] = $notify->format('Y-m-d');

                $input['package_name_ar'] = $packageID->name_ar;
                $input['created_at'] = Carbon::now();
                $input['updated_at'] = Carbon::now();
                $input['price'] = $packageID->price;

                $sale_setting = VendorDetail::getSalesCountDate();


                // check package start date is greater than or equal to vendor sales count date if true then insert into subscriber package table.
                if ($input['start_date'] >= $sale_setting) {

                    $previousData = DB::table($this->table)
                                    ->select('start_date', 'end_date', 'package_id', 'subscriber_id')
                                    ->where('id', $id)->first();

                    //Subscription package
                    $package_array['subscriber_id'] = $previousData->subscriber_id;
                    $package_array['member_id'] = $id;
                    $package_array['module_id'] = 1;
                    $package_array['vendor_id'] = VendorDetail::getID();
                    $package_array['package_id'] = $input['package_id'];
                    $package_array['start_date'] = $input['start_date'];
                    $package_array['end_date'] = $input['end_date'];
                    $package_array['name_en'] = $packageID->name_en;
                    $package_array['name_ar'] = $packageID->name_ar;
                    $package_array['description_en'] = $packageID->description_en;
                    $package_array['description_ar'] = $packageID->description_ar;
                    $package_array['area_name_en'] = VendorDetail::getArea(1, $input['package_id']);
                    $package_array['area_name_ar'] = VendorDetail::getArea(2, $input['package_id']);
                    $package_array['num_days'] = $packageID->num_days;
                    $package_array['price'] = $packageID->price;
                    $package_array['profit'] = VendorDetail::getProfitCommission($packageID->price, 0);
                    $package_array['commission'] = VendorDetail::getProfitCommission($packageID->price, 1);
                    $package_array['cash'] = $request->cash;
                    $package_array['knet'] = $request->knet;
                    $exp = new Carbon($input['end_date']);
                    $exp->subDays($packageID->expired_notify_duration);
                    $package_array['notification_date'] = $exp->format('Y-m-d');
                    $package_array['created_at'] = Carbon::now();
                    $package_array['updated_at'] = Carbon::now();

                    $subscription_table = VendorDetail::getPrefix() . 'subscribers_package_details';
                    $admin_subscription_table = 'subscribers_package_details';


                    $exist = DB::table($subscription_table)
                            ->where(array('package_id' => $previousData->package_id, 'start_date' => $previousData->start_date, 'end_date' => $previousData->end_date
                                , 'member_id' => $id, 'module_id' => 1))
                            ->first();
                    if (count($exist) > 0) {
                        DB::table($subscription_table)
                                ->where(array('package_id' => $previousData->package_id, 'start_date' => $previousData->start_date, 'end_date' => $previousData->end_date
                                    , 'member_id' => $id, 'module_id' => 1))
                                ->update($package_array);

                        DB::table($admin_subscription_table)
                                ->where(array('package_id' => $previousData->package_id, 'start_date' => $previousData->start_date, 'end_date' => $previousData->end_date
                                    , 'member_id' => $id, 'module_id' => 1))
                                ->update($package_array);

                        $dt = Carbon::now();
                        $invoice_array['created_at'] = Carbon::now();
                        $invoice_array['updated_at'] = Carbon::now();
                        $invoice_array['collected_by'] = Auth::guard('vendor')->user()->id;
                        $invoice_array['cash'] = $request->cash;
                        $invoice_array['knet'] = $request->knet;
                        $invoice_array['price'] = $packageID->price;
                        $invoice_array['package_id'] = $input['package_id'];
                        $invoice_array['start_date'] = $input['start_date'];
                        $invoice_array['end_date'] = $input['end_date'];
                        $invoice_array['package_name'] = $packageID->name_en;

                        //Invoice Table
                        $invoice_table = VendorDetail::getPrefix() . 'member_invoices';
                        DB::table($invoice_table)
                                ->where(array('package_id' => $previousData->package_id, 'start_date' => $previousData->start_date, 'end_date' => $previousData->end_date
                                    , 'member_id' => $id))
                                ->update($invoice_array);
                    } else {
                        DB::table($admin_subscription_table)->insert($package_array);
                        $subscriberedLastID = DB::table($subscription_table)->insertGetId($package_array);


                        $dt = Carbon::now();
                        $invoice_array['created_at'] = Carbon::now();
                        $invoice_array['updated_at'] = Carbon::now();
                        $invoice_array['member_id'] = $id;
                        $invoice_array['receipt_num'] = $dt->year . $dt->month . $dt->day . $dt->hour . $id;
                        $invoice_array['subscribed_package_id'] = $subscriberedLastID;
                        $invoice_array['collected_by'] = Auth::guard('vendor')->user()->id;
                        $invoice_array['cash'] = $request->cash;
                        $invoice_array['knet'] = $request->knet;
                        $invoice_array['price'] = $packageID->price;
                        $invoice_array['package_id'] = $input['package_id'];
                        $invoice_array['start_date'] = $input['start_date'];
                        $invoice_array['end_date'] = $input['end_date'];
                        $invoice_array['package_name'] = $packageID->name_en;

                        //Invoice Table
                        $invoice_table = VendorDetail::getPrefix() . 'member_invoices';
                        DB::table($invoice_table)->insert($invoice_array);
                    }
                }


                LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Member - ' . $request->name, 'updated package details');
            }

            $dob = $datetime->createFromFormat('d/m/Y', $request->dob);
            $input['dob'] = $dob->format('Y-m-d');
            $input['cash'] = $request->cash;
            $input['knet'] = $request->knet;
            $input['price'] = $packageID->price;

            DB::table($this->table)->where('id', $id)->update($input);

            //logActivity
            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Member - ' . $request->name, 'updated');


            Session::flash('message', config('global.updatedRecords'));

            return redirect($this->configName . '/members');
        }
    }

    /**
     * Display a Trashed listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashedlist(Request $request) {

        $this->table = VendorDetail::getPrefix() . 'members';

        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('trainers-delete');
        $Members = DB::table($this->table)
                ->select('id', 'name', 'deleted_at')
                ->whereNotNull('deleted_at')
                ->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Members)
                            ->editColumn('deleted_at', function ($Members) {
                                $newYear = new Carbon($Members->deleted_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('action', function ($Members) {
                                if ($this->DeleteAccess)
                                    return '<a  class="btn btn-success tooltip-primary btn-small restore" data-id="' . $Members->id . '"  data-toggle="tooltip" data-placement="top" title="Restore Record" data-original-title="Restore Record"><i class="entypo-ccw"></i></a>';
                                // . '<a  class="btn btn-danger tooltip-primary btn-small delete" data-id="' . $Trainer->id . '"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete Record" style="margin-left:10px;"><i class="entypo-cancel"></i></a>';
                            })
                            ->make();
        }

        return view('fitflowVendor.module1.members.trashedlist')->with('DeleteAccess', $this->DeleteAccess);
    }

    /**
     * Restore Record.
     *
     * @param  int  $ids
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request, $id) {

        $this->table = VendorDetail::getPrefix() . 'members';
        $id = $request->id;

        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('members-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {

            //logActivity
            //fetch title
            $Member = DB::table($this->table)
                    ->whereNotNull('deleted_at')
                    ->where('id', $id)
                    ->first();

            $groupname = $Member->name;

            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Member - ' . $groupname, 'restore');
            DB::table($this->table)->where('id', $id)->whereNotNull('deleted_at')->update(array('deleted_at' => null));

            return response()->json(['response' => config('global.restoreRecord')]);
        }
    }

    /**
     * Remove the Multiple resource from storage.
     *
     * @param  int  $ids
     * @return \Illuminate\Http\Response
     */
    public function trashMany(Request $request) {

        $this->table = VendorDetail::getPrefix() . 'members';

        // Initiate Class
        $Member = new Member;
        $Member->setTable($this->table);

        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('members-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $all_data = $request->except('_token', 'table-4_length');


        //logActivity
        //fetch title
        $Members = DB::table($this->table)->select('name')
                ->whereIn('id', $all_data['ids'])
                ->get();

        $name = $Members->pluck('name');
        $groupname = $name->toJson();

        LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Members - ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            $Member->where('id', $id)->delete();
        }

        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect($this->configName . '/members');
    }

    public function packageHistory(Request $request, $id) {

        $id = $request->id;

        $this->table = VendorDetail::getPrefix() . 'members';
        $this->packagetable = VendorDetail::getPrefix() . 'subscribers_package_details';

        //Get Subscriber name
        $username = DB::table($this->table)
                ->select('name')
                ->where('id', $id)
                ->first();

        //Sum Amount
        $Amount = DB::table($this->packagetable . ' As spd')
                ->where(array('spd.member_id' => $id))
                ->where('spd.module_id', 1)
                ->sum('spd.price');

        //Get all Archived Subscriber History
        $packageHistory = DB::table($this->packagetable . ' As spd')
                ->select('spd.name_en', 'spd.price', 'spd.start_date', 'spd.end_date', 'spd.num_days', 'spd.payment_id As payment_method', 'spd.cash', 'spd.knet', 'spd.payment_id')
                ->where(array('spd.member_id' => $id, 'spd.module_id' => 1))
                ->get();


        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($packageHistory)
                            ->editColumn('start_date', function ($packageHistory) {
                                $newYear = new Carbon($packageHistory->start_date);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('end_date', function ($packageHistory) {
                                $newYear = new Carbon($packageHistory->end_date);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('payment_method', function ($packageHistory) {
                                $str = '';
                                if ($packageHistory->payment_method == 0) {
                                    if (!is_null($packageHistory->cash) && $packageHistory->cash != 0) {
                                        $str .= 'Cash-' . $packageHistory->cash;
                                    }
                                    if (!is_null($packageHistory->knet) && $packageHistory->knet != 0) {
                                        $str .= ' KNET-' . $packageHistory->knet;
                                    }
                                    return $str;
                                } else {
                                    return ' ';
                                }
                            })
                            ->editColumn('action', function ($packageHistory) {
                                if ($packageHistory->payment_id != null)
                                    return '<a  class="btn btn-green tooltip-primary btn-small package_details" data-toggle="modal"  data-original-title="Package Details" title="Package Details"  href="#myModal" data-val="' . $packageHistory->payment_id . '"><i class="fa fa-money"></i></a>';
                            })
                            ->make();
        }

        return view('fitflowVendor.module1.members.packageHistory')
                        ->with('id', $id)
                        ->with('username', $username)
                        ->with('Amount', $Amount);
    }

    public function packagePayment(Request $request, $id) {

        $payment_id = $request->id;

        //Get package payment details
        $payment = DB::table('payment_details')
                ->select('reference_id', 'amount', 'post_date', 'result', DB::raw('(CASE WHEN card_type = 1 THEN "KNET" ELSE "Credit Card" END) AS card_type'))
                ->where(array('id' => $payment_id, 'module_id' => 1))
                ->first();



        //Change Start Date Format
        $newdate = new Carbon($payment->post_date);
        $payment->post_date = $newdate->format('d/m/Y');

        $returnHTML = view('fitflowVendor.module1.members.packagePayment')->with('payment', $payment)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

    //Edit Current Package
    public function renewPackage(Request $request) {

        $this->table = VendorDetail::getPrefix() . 'members';

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('renewPackage');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {

            //custom message
            $messages = [
                'cash.required' => config('global.paymentValidate'),
            ];

            $validator = Validator::make($request->all(), [
                        'id' => 'required',
                        'package_id' => 'required|numeric',
                        'start_date' => 'required|date_format:d/m/Y',
                        'cash' => array('required_without:knet', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
                        'knet' => array('required_without:cash', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ]);

            // validation failed
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->all()]);
            } else {


                $input = $request->except('_token', 'cash', 'knet');

                $previousData = DB::table($this->table)
                                ->select('subscriber_id', 'end_date')
                                ->where('id', $input['id'])->first();

                //Change Date Format
                $datetime = new DateTime();
                $newDate = $datetime->createFromFormat('d/m/Y', $request->start_date);
                $input['start_date'] = $newDate->format('Y-m-d');

                //Check start date should be greater than previous package end date.
                if ($input['start_date'] <= $previousData->end_date) {
                    return response()->json(['error' => config('global.errorRenewPackage')]);
                } else {

                    //Package Detail
                    $packageID = DB::table('vendor_packages')->where('id', $input['package_id'])->where('vendor_id', VendorDetail::getID())->first();
                    $input['package_name'] = $packageID->name_en;
                    $exp = new Carbon($input['start_date']);
                    $exp->addDays($packageID->num_days);
                    $input['end_date'] = $exp->format('Y-m-d');
                    $input['subscription'] = 1;

                    //Set notification date
                    $notify = new Carbon($input['end_date']);
                    $notify->subDays($packageID->expired_notify_duration);
                    $input['notification_date'] = $notify->format('Y-m-d');

                    $input['package_name_ar'] = $packageID->name_ar;

                    DB::table($this->table)->where('id', $input['id'])->update($input);



                    //Subscription package
                    $package_array['subscriber_id'] = $previousData->subscriber_id;
                    $package_array['member_id'] = $input['id'];
                    $package_array['module_id'] = 1;
                    $package_array['vendor_id'] = VendorDetail::getID();
                    $package_array['package_id'] = $input['package_id'];
                    $package_array['start_date'] = $input['start_date'];
                    $package_array['end_date'] = $input['end_date'];
                    $package_array['name_en'] = $packageID->name_en;
                    $package_array['name_ar'] = $packageID->name_ar;
                    $package_array['description_en'] = $packageID->description_en;
                    $package_array['description_ar'] = $packageID->description_ar;
                    $package_array['area_name_en'] = VendorDetail::getArea(1, $input['package_id']);
                    $package_array['area_name_ar'] = VendorDetail::getArea(2, $input['package_id']);
                    $package_array['num_days'] = $packageID->num_days;
                    $package_array['price'] = $packageID->price;
                    $package_array['profit'] = VendorDetail::getProfitCommission($packageID->price, 0);
                    $package_array['commission'] = VendorDetail::getProfitCommission($packageID->price, 1);
                    $package_array['cash'] = $request->cash;
                    $package_array['knet'] = $request->knet;
                    $exp = new Carbon($input['end_date']);
                    $exp->subDays($packageID->expired_notify_duration);
                    $package_array['notification_date'] = $exp->format('Y-m-d');
                    $package_array['created_at'] = Carbon::now();
                    $package_array['updated_at'] = Carbon::now();

                    $subscription_table = VendorDetail::getPrefix() . 'subscribers_package_details';
                    $admin_subscription_table = 'subscribers_package_details';
                    $vendor_package_reference_id = DB::table($admin_subscription_table)->insertGetId($package_array);
                    $package_array['vendor_package_reference_id'] = $vendor_package_reference_id;
                    $subscriberedLastID = DB::table($subscription_table)->insertGetId($package_array);


                    $dt = Carbon::now();
                    $invoice_array['created_at'] = Carbon::now();
                    $invoice_array['updated_at'] = Carbon::now();
                    $invoice_array['member_id'] = $input['id'];
                    $invoice_array['receipt_num'] = $dt->year . $dt->month . $dt->day . $dt->hour . $input['id'];
                    $invoice_array['subscribed_package_id'] = $subscriberedLastID;
                    $invoice_array['collected_by'] = Auth::guard('vendor')->user()->id;
                    $invoice_array['cash'] = $request->cash;
                    $invoice_array['knet'] = $request->knet;
                    $invoice_array['price'] = $packageID->price;
                    $invoice_array['package_id'] = $input['package_id'];
                    $invoice_array['start_date'] = $input['start_date'];
                    $invoice_array['end_date'] = $input['end_date'];
                    $invoice_array['package_name'] = $packageID->name_en;

                    //Invoice Table
                    $invoice_table = VendorDetail::getPrefix() . 'member_invoices';
                    DB::table($invoice_table)->insert($invoice_array);

                    //logActivity
                    //Get Subscriber name
                    $username = DB::table($this->table)
                            ->select('name')
                            ->where('id', $input['id'])
                            ->first();

                    LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Member - ' . $username->name, 'Package Renewed');

                    return response()->json(['response' => config('global.addedRecords')]);
                }
            }
        }
    }

    public function getPackageDetail(Request $request) {

        $payment_id = $request->id;
        $start_date = $request->start_date;

        if ($request->has(['id', 'start_date'])) {
            //Package Detail
            $packageID = DB::table('vendor_packages')->select('num_days', 'price')->where('id', $payment_id)->where('vendor_id', VendorDetail::getID())->first();
            $datetime = new DateTime();
            $packageID->start_date = $start_date;
            $newDate = $datetime->createFromFormat('d/m/Y', $packageID->start_date);
            $start_date = $newDate->format('Y-m-d');
            $exp = new Carbon($start_date);
            $exp->addDays($packageID->num_days);
            $packageID->end_date = $exp->format('d/m/Y');


            return response()->json(array('packages' => $packageID));
        } else {
            return response()->json(array('error' => true));
        }
    }

    public function invoice(Request $request, $id) {

        $member_id = $request->id;

        $this->table = VendorDetail::getPrefix() . 'members';
        $this->invoiceTable = VendorDetail::getPrefix() . 'member_invoices';


        //Get Member & Invoice
        $Invoice = DB::table($this->table . ' As m')
                ->join($this->invoiceTable . ' As inv', 'inv.end_date', '=', 'm.end_date')
                ->select('m.name', 'm.mobile', 'inv.receipt_num', 'inv.created_at', 'inv.package_name', 'inv.start_date', 'inv.end_date', 'inv.cash', 'inv.knet', 'inv.price')
                ->where('m.id', $member_id)
                ->first();


        //Change Start Date Format
        $newdate = new Carbon($Invoice->start_date);
        $Invoice->start_date = $newdate->format('d/m/Y');

        $edate = new Carbon($Invoice->end_date);
        $Invoice->end_date = $edate->format('d/m/Y');

        $cdate = new Carbon($Invoice->created_at);
        $Invoice->cdate = $cdate->format('d/m/Y');

        $Invoice->amount = $Invoice->cash + $Invoice->knet;
        $Invoice->title = 'Membership';

        $returnHTML = view('fitflowVendor.module1.members.invoice')->with('Invoice', $Invoice)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

    public function sendInvoice(Request $request, $id) {
        $id = $request->id;
        $this->table = VendorDetail::getPrefix() . 'members';
        $this->invoiceTable = VendorDetail::getPrefix() . 'member_invoices';
        $this->instructorInvoiceTable = 'instructor_member_invoices';

        //Ajax request
        if (request()->ajax()) {

            // Initiate Class
            $Member = new Member;
            $Member->setTable($this->table);

            //fetch Record
            $Member = $Member
                    ->select('name', 'id', 'email')
                    ->where(array('status' => 1, 'id' => $id))
                    ->first();


            //check record exist
            $count = DB::table($this->table)->where(array('status' => 1, 'id' => $id))->count();

            if ($count != 0) {
                $Member->assign = 'Member';
                //Get Member & Invoice
                $Invoice = DB::table($this->table . ' As m')
                        ->join($this->invoiceTable . ' As inv', 'inv.end_date', '=', 'm.end_date')
                        ->select('m.name', 'm.mobile', 'inv.receipt_num', 'inv.created_at', 'inv.package_name', 'inv.start_date', 'inv.end_date', 'inv.cash', 'inv.knet', 'inv.price')
                        ->where('m.id', $id)
                        ->first();

                //Get Member & Instructor Invoice
                $instructorInvoice = DB::table($this->table . ' As m')
                        ->join($this->instructorInvoiceTable . ' As inv', 'inv.member_id', '=', 'm.id')
                        ->select('m.name', 'm.mobile', 'inv.receipt_num', 'inv.created_at', 'inv.package_name', 'inv.start_date', 'inv.end_date', 'inv.cash', 'inv.knet', 'inv.price')
                        ->where('m.id', $id)
                        ->orderby('inv.id', 'DESC')
                        ->limit(1)
                        ->first();

                $instructorCount = count($instructorInvoice);


                //Change Start Date Format
                $newdate = new Carbon($Invoice->start_date);
                $Member->start_date = $newdate->format('d/m/Y');

                $edate = new Carbon($Invoice->end_date);
                $Member->end_date = $edate->format('d/m/Y');

                $cdate = new Carbon($Invoice->created_at);
                $Member->cdate = $cdate->format('d/m/Y');

                $Member->amount = $Invoice->cash + $Invoice->knet;
                $Member->price = $Invoice->price;
                $Member->mobile = $Invoice->mobile;
                $Member->receipt_num = $Invoice->receipt_num;
                $Member->package_name = $Invoice->package_name;
                $Member->title = 'Membership';
                //$Member->created_at =$Invoice->created_at;
                //Email with attachment 
                Mail::to($Member->email)->send(new InvoiceEmail($Member));

                if ($instructorCount != 0) {
                    //Change Start Date Format
                    $newdate = new Carbon($instructorInvoice->start_date);
                    $Member->start_date = $newdate->format('d/m/Y');

                    $edate = new Carbon($instructorInvoice->end_date);
                    $Member->end_date = $edate->format('d/m/Y');

                    $cdate = new Carbon($instructorInvoice->created_at);
                    $Member->cdate = $cdate->format('d/m/Y');

                    $Member->amount = $instructorInvoice->cash + $instructorInvoice->knet;
                    $Member->price = $instructorInvoice->price;
                    $Member->mobile = $instructorInvoice->mobile;
                    $Member->receipt_num = $instructorInvoice->receipt_num;
                    $Member->package_name = $instructorInvoice->package_name;
                    $Member->title = 'Subscription';
                    //Email with attachment 
                    Mail::to($Member->email)->send(new InvoiceEmail($Member));
                }

                return response()->json(['response' => config('global.sentEmail')]);
            } else {

                return response()->json(['response' => config('global.unsentEmail')]);
            }
        }
    }

    //Instructor Subscription
    public function instructorSubscription(Request $request) {

        $this->table = VendorDetail::getPrefix() . 'members';

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('instructorSubscription');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {

            //custom message
            $messages = [
                'cash.required' => config('global.paymentValidate'),
            ];

            $validator = Validator::make($request->all(), [
                        'id' => 'required',
                        'package_id' => 'required|numeric',
                        'cash' => array('required_without:knet', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
                        'knet' => array('required_without:cash', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ]);

            // validation failed
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->all()]);
            } else {

                $input = $request->except('_token', 'cash', 'knet');
                $input['start_date'] = date('Y-m-d');

                $previousData = DB::table($this->table)
                                ->select('subscriber_id')
                                ->where('id', $input['id'])->first();

                //Package Detail
                $packageID = DB::table('instructor_packages')->where('id', $input['package_id'])->where('vendor_id', VendorDetail::getID())->first();
                $exp = new Carbon($input['start_date']);
                $exp->addDays($packageID->num_days);
                $input['end_date'] = $exp->format('Y-m-d');


                //Set notification date
                $notify = new Carbon($input['end_date']);
                $notify->subDays($packageID->expired_notify_duration);
                $input['notification_date'] = $notify->format('Y-m-d');

                //Instructor Subscription package
                $package_array['subscriber_id'] = $previousData->subscriber_id;
                $package_array['member_id'] = $input['id'];
                $package_array['module_id'] = 1;
                $package_array['vendor_id'] = VendorDetail::getID();
                $package_array['package_id'] = $input['package_id'];
                $package_array['start_date'] = $input['start_date'];
                $package_array['end_date'] = $input['end_date'];
                $package_array['num_points'] = $packageID->num_points;
                $package_array['name_en'] = $packageID->name_en;
                $package_array['name_ar'] = $packageID->name_ar;
                $package_array['description_en'] = $packageID->description_en;
                $package_array['description_ar'] = $packageID->description_ar;
                $package_array['area_name_en'] = VendorDetail::getArea(1, $input['package_id']);
                $package_array['area_name_ar'] = VendorDetail::getArea(2, $input['package_id']);
                $package_array['num_days'] = $packageID->num_days;
                $package_array['price'] = $packageID->price;
                $package_array['profit'] = VendorDetail::getProfitCommission($packageID->price, 0);
                $package_array['commission'] = VendorDetail::getProfitCommission($packageID->price, 1);
                $package_array['cash'] = $request->cash;
                $package_array['knet'] = $request->knet;
                $exp = new Carbon($input['end_date']);
                $exp->subDays($packageID->expired_notify_duration);
                $package_array['notification_date'] = $exp->format('Y-m-d');
                $package_array['created_at'] = Carbon::now();
                $package_array['updated_at'] = Carbon::now();

                $subscription_table = 'instructor_subscribers_package_details';
                $subscriberedLastID = DB::table($subscription_table)->insertGetId($package_array);

                $dt = Carbon::now();
                $invoice_array['vendor_id'] = VendorDetail::getID();
                $invoice_array['created_at'] = Carbon::now();
                $invoice_array['updated_at'] = Carbon::now();
                $invoice_array['member_id'] = $input['id'];
                $invoice_array['receipt_num'] = $dt->year . $dt->month . $dt->day . $dt->hour . $input['id'];
                $invoice_array['subscribed_package_id'] = $subscriberedLastID;
                $invoice_array['collected_by'] = Auth::guard('vendor')->user()->id;
                $invoice_array['cash'] = $request->cash;
                $invoice_array['knet'] = $request->knet;
                $invoice_array['price'] = $packageID->price;
                $invoice_array['package_id'] = $input['package_id'];
                $invoice_array['start_date'] = $input['start_date'];
                $invoice_array['end_date'] = $input['end_date'];
                $invoice_array['package_name'] = $packageID->name_en;

                //Invoice Table
                $invoice_table = 'instructor_member_invoices';
                DB::table($invoice_table)->insert($invoice_array);


                //logActivity
                //Get Subscriber name
                $username = DB::table($this->table)
                        ->select('name')
                        ->where('id', $input['id'])
                        ->first();

                LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Member - ' . $username->name, 'Instructor Package Subscribed');

                return response()->json(['response' => config('global.addedRecords')]);
            }
        }
    }

    // Instructor Invoice
    public function instructorInvoice(Request $request, $id) {

        $member_id = $request->id;

        $this->table = VendorDetail::getPrefix() . 'members';
        $this->invoiceTable = 'instructor_member_invoices';


        //Get Member & Invoice
        $Invoice = DB::table($this->table . ' As m')
                ->join($this->invoiceTable . ' As inv', 'inv.member_id', '=', 'm.id')
                ->select('m.name', 'm.mobile', 'inv.receipt_num', 'inv.created_at', 'inv.package_name', 'inv.start_date', 'inv.end_date', 'inv.cash', 'inv.knet', 'inv.price')
                ->where('m.id', $member_id)
                ->orderby('inv.id', 'DESC')
                ->limit(1)
                ->first();


        //Change Start Date Format
        $newdate = new Carbon($Invoice->start_date);
        $Invoice->start_date = $newdate->format('d/m/Y');

        $edate = new Carbon($Invoice->end_date);
        $Invoice->end_date = $edate->format('d/m/Y');

        $cdate = new Carbon($Invoice->created_at);
        $Invoice->cdate = $cdate->format('d/m/Y');

        $Invoice->amount = $Invoice->cash + $Invoice->knet;
        $Invoice->title = 'Subscription';

        $returnHTML = view('fitflowVendor.module1.members.invoice')->with('Invoice', $Invoice)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

}
