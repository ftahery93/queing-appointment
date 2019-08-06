<?php

namespace App\Http\Controllers\Vendor\Module1;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Input;
use Carbon\Carbon;
use App\Models\Vendor\VendorPackage;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;
use App\Helpers\VendorDetail;

class VendorPackageController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;
    protected $guard = 'vendor';
    protected $configName;

    public function __construct() {
        $this->middleware($this->guard);
        $this->middleware('vendorPermission:vendorPackages');
        $this->configName = config('global.fitflowVendor') . config('global.storeAddress') . '/' . config('global.M1');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('vendorPackages-create');

//Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('vendorPackages-delete');

//Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('vendorPackages-edit');

        $VendorPackage = VendorPackage::
                leftjoin('vendor_branches', 'vendor_packages.branch_id', '=', 'vendor_branches.id')
                ->select('vendor_packages.id', 'vendor_branches.name_en As branch', 'vendor_packages.name_en', 'vendor_packages.num_days', 'vendor_packages.price', 'vendor_packages.status', 'vendor_packages.created_at')
                ->where('vendor_packages.vendor_id', VendorDetail::getID())
                ->get();


        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($VendorPackage)
                            ->editColumn('created_at', function ($VendorPackage) {
                                $newYear = new Carbon($VendorPackage->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('status', function ($VendorPackage) {
                                return $VendorPackage->status == 1 ? '<div class="label label-success status" sid="' . $VendorPackage->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary status"  sid="' . $VendorPackage->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('id', function ($VendorPackage) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $VendorPackage->id . '">';
                            })
                            ->editColumn('action', function ($VendorPackage) {
                                if ($this->EditAccess)
                                    return '<a href="' . url($this->configName . '/packages') . '/' . $VendorPackage->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>';
                            })
                            ->make();
        }

        return view('fitflowVendor.module1.packages.index')
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
        $this->CreateAccess = Permit::AccessPermission('vendorPackages-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        //Get all Gender Types
        $branches = DB::table('vendor_branches')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->where('vendor_id', VendorDetail::getID())
                ->get();

        return view('fitflowVendor.module1.packages.create')->with('branches', $branches);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // validate
        Validator::extend('less_than', function($attribute, $value, $parameters) {
            $other = Input::get($parameters[0]);

            return isset($other) and intval($value) < intval($other);
        });
        $messsages = array(
            'expired_notify_duration.less_than' => config('global.lessthanValidate'),
        );
        $validator = Validator::make($request->only(['vendor_id', 'name_en', 'name_ar', 'price', 'branch_id', 'num_days', 'expired_notify_duration']), [
                    'vendor_id' => 'required',
                    'name_en' => 'required',
                    'name_ar' => 'required',
                    'branch_id' => 'required',
                    'num_days' => 'required|numeric',
                    'expired_notify_duration' => 'required|numeric|less_than:num_days',
                    'price' => array('required', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/')
                        ], $messsages);


        // validation failed
        if ($validator->fails()) {

            return redirect($this->configName . '/packages/create')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->all();

             //Increment offer value in vendors module1_offers column 
             if($input['has_offer']==1){
               VendorDetail::incrementOffers(1);
            }

           //Decrement offer value in vendors module1_offers column 
            if($input['has_offer']==0){
             VendorDetail::decrementOffers(1);
            }

            VendorPackage::create($input);

            //logActivity
            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] VendorPackage - ' . $request->name_en, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect($this->configName . '/packages');
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
        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('vendorPackages-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        $VendorPackage = VendorPackage::find($id);

        //Get all Gender Types
        $branches = DB::table('vendor_branches')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->where('vendor_id', VendorDetail::getID())
                ->get();

        // show the edit form and pass the nerd
        return View::make('fitflowVendor.module1.packages.edit')
                        ->with('branches', $branches)
                        ->with('VendorPackage', $VendorPackage);
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
        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('vendorPackages-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            $VendorPackage = VendorPackage::findOrFail($id);
            $VendorPackage->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $VendorPackage = VendorPackage::findOrFail($id);
        // validate
        Validator::extend('less_than', function($attribute, $value, $parameters) {
            $other = Input::get($parameters[0]);

            return isset($other) and intval($value) < intval($other);
        });
        $messsages = array(
            'expired_notify_duration.less_than' => config('global.lessthanValidate'),
        );

        $validator = Validator::make($request->only(['vendor_id', 'name_en', 'name_ar', 'branch_id', 'price', 'num_days', 'expired_notify_duration']), [
                    'vendor_id' => 'required',
                    'name_en' => 'required',
                    'name_ar' => 'required',
                    'branch_id' => 'required',
                    'num_days' => 'required|numeric',
                    'expired_notify_duration' => 'required|numeric|less_than:num_days',
                    'price' => array('required', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/')
                        ], $messsages);


        // validation failed
        if ($validator->fails()) {
            return redirect($this->configName . '/packages/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->all();

            //Increment offer value in vendors module1_offers column  
            if($input['has_offer']==1){
               VendorDetail::incrementOffers(1);
            }

           //Decrement offer value in vendors module1_offers column 
            if($input['has_offer']==0){
             VendorDetail::decrementOffers(1);
            }


            $VendorPackage->fill($input)->save();

            //logActivity
            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] VendorPackage - ' . $request->name_en, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect($this->configName . '/packages');
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
        $this->DeleteAccess = Permit::AccessPermission('vendorPackages-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $all_data = $request->except('_token', 'table-4_length');

        //logActivity
        //fetch title
        $VendorPackage = VendorPackage::
                select('name_en')
                ->whereIn('id', $all_data['ids'])
                ->get();

        $name = $VendorPackage->pluck('name_en');
        $groupname = $name->toJson();

        LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] VendorPackage - ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {

            //Decrement offer value in vendors module1_offers column 
            $VendorPackage = VendorPackage::
                select('has_offer')
                ->where('id', $id)
                ->first();


           if($VendorPackage->has_offer==1)               
             VendorDetail::decrementOffers(1);
            
            VendorPackage::destroy($id);
        }

        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect($this->configName . '/packages');
    }

}
