<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use DateTime;
use App\Models\Vendor\Classes;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;
use App\Helpers\VendorDetail;
use App\Helpers\Cron;
use App\Models\Admin\Vendor;
use App\Mail\classApprovedEmail;
use Illuminate\Support\Arr;
use Mail;

class PendingClassController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:pendingClasses');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('pendingClasses-edit');

        $VendorList = DB::table('classes')
                ->join('vendors', 'classes.vendor_id', '=', 'vendors.id')
                ->select('vendors.id', 'vendors.name', 'vendors.mobile')
                ->where('classes.approved_status', 0)
                ->groupby('classes.vendor_id');

        // if Request having Pacakge name
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            $VendorList->where('classes.vendor_id', $ID);
        }
        $Vendors = $VendorList->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Vendors)
                            ->editColumn('action', function ($Vendor) {
                                return '<a href="' . url('admin/pendingClasses') . '/' . $Vendor->id . '" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="View Records" data-original-title="View Records"><i class="entypo-eye"></i></a>';
                            })
                            ->make();
        }
        $Vendors = DB::table('classes')
                ->join('vendors', 'classes.vendor_id', '=', 'vendors.id')
                ->select('vendors.id', 'vendors.name')
                ->where('classes.approved_status', 0)
                ->groupby('classes.vendor_id')
                ->get();

        return view('admin.pendingClasses.index')
                        ->with('EditAccess', $this->EditAccess)
                        ->with('Vendors', $Vendors);
    }

    //Edit Seats
    public function pendingClasses(Request $request) {

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('pendingClasses-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        $Classes = DB::table('classes')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select('classes.id AS ID', DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS name_en'), 'classes.temp_num_seats', 'classes.temp_fitflow_seats', 'classes.temp_price'
                        , 'classes.temp_commission_perc', 'classes.reason', 'classes.id', 'classes.commission_perc As action', 'classes.commission_perc')
                ->where(array('classes.vendor_id' => $request->vendor_id, 'classes.status' => 1, 'class_master.status' => 1, 'classes.approved_status' => 0))
                ->get();

        //if Count 0 then redirect to pending vendor list
        if ($Classes->count() == 0)
            return redirect('admin/pendingVendorClasses');


        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Classes)
                            ->editColumn('id', function ($Classes) {
                                if ($Classes->commission_perc == null) {
                                    return '<span style="color:red;">' . config('global.commissionNote') . '</span>'
                                            . '<input type="hidden" value="' . $Classes->id . '" name="ids" class="ids">';
                                } else {
                                    return '<input tabindex="5" type="radio" class="icheck-14 radio class_status"   name="class_status' . $Classes->id . '" value="1" '
                                            . 'id="approved' . $Classes->id . '"><label class="control-label" for="approved' . $Classes->id . '">Approved</label>  </br> '
                                            . '<input tabindex="5" type="radio" class="icheck-14 radio class_status"   name="class_status' . $Classes->id . '" value="2" '
                                            . 'id="rejected' . $Classes->id . '"><label class="control-label"  for="rejected' . $Classes->id . '">Rejected</label>'
                                            . '<input type="hidden" value="' . $Classes->id . '" name="ids" class="ids">';
                                }
                            })
                            ->editColumn('reason', function ($Classes) {
                                if ($Classes->commission_perc == null) {
                                    return '<textarea class="form-control reason resize" name="reason" col="3" disabled></textarea>';
                                } else {
                                    return '<textarea class="form-control reason resize" name="reason" col="3"></textarea>';
                                }
                            })
                            ->editColumn('action', function ($Classes) {
                                return '<a  class="btn btn-gold tooltip-primary btn-small previousDetail" data-toggle="modal" data-placement="top" title="View Records" data-original-title="View Records"  href="#myModal" data-val="' . $Classes->id . '"><i class="entypo-eye"></i></a>';
                            })
                            ->setRowId(function ($Classes) {
                                return $Classes->id;
                            })
                            ->make();
        }

        $vendorName = DB::table('vendors')->select('name')->where('id', $request->vendor_id)->first();

        return view('admin.pendingClasses.pendinglist')
                        ->with('EditAccess', $this->EditAccess)
                        ->with('vendor_id', $request->vendor_id)
                        ->with('vendorName', $vendorName);
    }

    public function editClasses(Request $request) {

        $arrayData = $request->jsonData;
        $arrayData = array_filter($arrayData);
        //dd($arrayData);
        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('pendingClasses-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');
        //Ajax request
        if (request()->ajax()) {
            $error = array();
            $ids = array();

            foreach ($arrayData as $val) {
                $classStatus = '';
                if (array_has($val, 'class_status')) {
                    $classStatus = $val['class_status'];
                }
                if ($classStatus == '') {
                    array_push($error, $val['ids']);
                } else {
                    //Get temp details
                    $Classes = DB::table('classes')
                                    ->select('temp_num_seats', 'temp_fitflow_seats', 'temp_gym_seats', 'temp_price')
                                    ->where('id', $val['ids'])->first();


                    $input['num_seats'] = $Classes->temp_num_seats;
                    $input['fitflow_seats'] = $Classes->temp_fitflow_seats;
                    $input['available_seats'] = $Classes->temp_gym_seats;
                    $input['reason'] = $val['reason'];
                    $input['price'] = $Classes->temp_price;
                    $input['approved_status'] = $val['class_status'];

                    if ($val['class_status'] == 1) {
                        $input['temp_num_seats'] = null;
                        $input['temp_fitflow_seats'] = null;
                        $input['temp_gym_seats'] = null;
                        $input['temp_price'] = null;
                        $input['temp_commission_perc'] = null;
                        $input['temp_commission_kd'] = null;
                    }

                    //if reject then update only status
                    if ($val['class_status'] == 2) {
                        $update = DB::table('classes')
                                ->where('id', $val['ids'])
                                ->update(array('approved_status' => $val['class_status'], 'reason' => $val['reason']));
                    } else {
                        $update = DB::table('classes')
                                ->where('id', $val['ids'])
                                ->update($input);
                    }

                    //Get all approved ids
                    array_push($ids, $val['ids']);

                    if (!$update) {
                        array_push($error, $val['ids']);
                    }
                }
            }
            if ($error) {
                return response()->json(['error' => 'Class ID# ' . json_encode($error) . ' cannot be updated']);
            } else {
                //add record in apporved_classlist table after approved classes
                //$inp['vendor_id'] = $request->vendorID;
                //$lastID = DB::table('approved_classlist')->insertGetId($inp);
                //Send Email to vendor after approved classes
//                Mail::raw([], function($message) use($html, $plain, $to, $subject, $formEmail, $formName) {
//                    $message->from($fromEmail, $fromName);
//                    $message->to($to);
//                    $message->subject($subject);
//                    $message->setBody($html, 'text/html'); // dont miss the '<html></html>' if the email dont contains it to decrease your spam score !!
//                    $message->addPart($plain, 'text/plain');
//                });

                $vendorClasses = Vendor
                                ::select('name', 'id', 'email')
                                ->where('id', $request->vendorID)->first();


                $jsonID = json_encode($ids);
                $vendorClasses->jsonID = $jsonID;

                Mail::to($vendorClasses->email)->send(new classApprovedEmail($vendorClasses));

                //logActivity
                LogActivity::addToLog('[Vendor ' . $request->vendorName . '] Classes ', 'approved');

                return response()->json(['response' => config('global.updatedRecords')]);
            }
        }
    }

    public function previousDetail(Request $request) {

        $id = $request->id;

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('pendingClasses-view');
        if (!$this->ViewAccess)
            return redirect('errors/401');

        $classList = DB::table('classes')
                        ->select('num_seats', 'available_seats', 'fitflow_seats', 'price', 'commission_perc')
                        ->where('id', $id)->first();


        $returnHTML = view('admin.pendingClasses.previousDetail')->with('classList', $classList)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

    //Commission
    public function pendingCommission(Request $request) {

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('pendingClasses-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        $Classes = DB::table('classes')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select('classes.id AS ID', DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS name_en'), 'classes.temp_price', 'classes.commission_perc', 'classes.temp_commission_perc'
                        , 'classes.id')
                ->where(array('classes.vendor_id' => $request->vendor_id, 'classes.status' => 1, 'class_master.status' => 1, 'classes.approved_status' => 0))
                ->get();

        //if Count 0 then redirect to pending vendor list
        if ($Classes->count() == 0)
            return redirect('admin/pendingVendorClasses');

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Classes)
                            ->editColumn('temp_commission_perc', function ($Classes) {
                                return '<div class="commission_field"><label style="color:red;cursor:pointer;">Click here to add commission</label></div>'
                                        . '<input type="hidden" value="' . $Classes->temp_price . '" name="temp_price" class="temp_price">'
                                        . '<input type="hidden" value="' . $Classes->id . '" name="ids" class="ids">';
                            })
                            ->setRowId(function ($Classes) {
                                return $Classes->id;
                            })
                            ->make();
        }

        $vendorName = DB::table('vendors')->select('name')->where('id', $request->vendor_id)->first();

        return view('admin.pendingClasses.pendingCommissions')
                        ->with('EditAccess', $this->EditAccess)
                        ->with('vendor_id', $request->vendor_id)
                        ->with('vendorName', $vendorName);
    }

    //Add Commission
    public function addCommission(Request $request) {

        $arrayData = $request->jsonData;
        $arrayData = array_filter($arrayData);
        //dd($arrayData);
        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('pendingClasses-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        if (!$this->EditAccess)
            return redirect('errors/401');
        //Ajax request
        if (request()->ajax()) {
            $error = array();
            $ids = array();

            foreach ($arrayData as $val) {
                if (!Arr::exists($val, 'temp_commission_perc')) {
                    
                } elseif (Arr::exists($val, 'temp_commission_perc')) {
                    if ($val['temp_commission_perc'] == '') {
                        array_push($error, $val['ids']);
                    } else {                       
                        $input['commission_perc'] = $val['temp_commission_perc'];
                        $commission_kd = ($val['temp_commission_perc'] * $val['temp_price']) / 100;
                        $input['commission_kd'] = $commission_kd;
                        $input['temp_commission_perc'] = null;
                        $input['temp_commission_kd'] = null;



                        $update = DB::table('classes')
                                ->where('id', $val['ids'])
                                ->update($input);

                        //Get all approved ids
                        array_push($ids, $val['ids']);

                        if (!$update) {
                            array_push($error, $val['ids']);
                        }
                    }
                }
            }
            if ($error) {
                return response()->json(['error' => 'Class ID# ' . json_encode($error) . ' '.config('global.errorInput')]);
            } else {

                //logActivity
                LogActivity::addToLog('[Vendor ' . $request->vendorName . '] Class Commission ', 'added');

                return response()->json(['response' => config('global.updatedRecords')]);
            }
        }
    }

}
