<?php

namespace App\Http\Controllers\Vendor\Module2;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Input;
use Carbon\Carbon;
use App\Models\Vendor\ClassPackage;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;
use App\Helpers\VendorDetail;

class ClassPackageController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;
    protected $guard = 'vendor';
    protected $configName;

    public function __construct() {
        $this->middleware($this->guard);
        $this->middleware('vendorPermission:classPackages');
        $this->configName = config('global.fitflowVendor') . config('global.storeAddress') . '/' . config('global.M2');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('classPackages-create');

//Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('classPackages-delete');

//Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('classPackages-edit');

        $ClassPackage = ClassPackage::
               // leftjoin('vendor_branches', 'class_packages.branch_id', '=', 'vendor_branches.id')
                select('class_packages.id',  'class_packages.name_en', DB::raw('(CASE WHEN class_packages.num_points = 0 THEN "Unlimited" ELSE class_packages.num_points  END) AS num_points'),'class_packages.num_days', 'class_packages.price', 'class_packages.status', 'class_packages.created_at')
                ->where('class_packages.vendor_id', VendorDetail::getID())
                ->get();
        
      

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($ClassPackage)
                            ->editColumn('created_at', function ($ClassPackage) {
                                $newYear = new Carbon($ClassPackage->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('status', function ($ClassPackage) {
                                return $ClassPackage->status == 1 ? '<div class="label label-success status" sid="' . $ClassPackage->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary status"  sid="' . $ClassPackage->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('id', function ($ClassPackage) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $ClassPackage->id . '">';
                            })
                            ->editColumn('action', function ($ClassPackage) {
                                if ($this->EditAccess)
                                    return '<a href="' . url($this->configName . '/classPackages') . '/' . $ClassPackage->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>';
                            })
                            ->make();
        }

        return view('fitflowVendor.module2.classPackages.index')
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
        $this->CreateAccess = Permit::AccessPermission('classPackages-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        //Get all Gender Types
        $branches = DB::table('vendor_branches')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();

        return view('fitflowVendor.module2.classPackages.create')->with('branches', $branches);
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
        $validator = Validator::make($request->only(['vendor_id', 'name_en', 'name_ar', 'num_points', 'price', 'num_days', 'expired_notify_duration']), [
                    'vendor_id' => 'required',
                    'name_en' => 'required',
                    'name_ar' => 'required',
                    //'branch_id' => 'required',
                    'num_days' => 'required|numeric',
                    'num_points' => 'required|numeric',
                    'expired_notify_duration' => 'required|numeric|less_than:num_days',
                    'price' => array('required', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/')
                        ], $messsages);


        // validation failed
        if ($validator->fails()) {

            return redirect($this->configName . '/classPackages/create')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->all();
            
            //Increment offer value in vendors module1_offers column 
             if($input['has_offer']==1){
               VendorDetail::incrementOffers(2);
            }

           //Decrement offer value in vendors module1_offers column 
            if($input['has_offer']==0){
             VendorDetail::decrementOffers(2);
            }

            ClassPackage::create($input);

            //logActivity
            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] ClassPackage - ' . $request->name_en, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect($this->configName . '/classPackages');
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
        $this->EditAccess = Permit::AccessPermission('classPackages-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        $ClassPackage = ClassPackage::find($id);

        //Get all Gender Types
        $branches = DB::table('vendor_branches')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();

        // show the edit form and pass the nerd
        return View::make('fitflowVendor.module2.classPackages.edit')
                        ->with('branches', $branches)
                        ->with('ClassPackage', $ClassPackage);
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
        $this->EditAccess = Permit::AccessPermission('classPackages-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            $ClassPackage = ClassPackage::findOrFail($id);
            $ClassPackage->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $ClassPackage = ClassPackage::findOrFail($id);
        // validate
        Validator::extend('less_than', function($attribute, $value, $parameters) {
            $other = Input::get($parameters[0]);

            return isset($other) and intval($value) < intval($other);
        });
        $messsages = array(
            'expired_notify_duration.less_than' => config('global.lessthanValidate'),
        );

        $validator = Validator::make($request->only(['vendor_id', 'name_en', 'name_ar', 'num_points',  'price', 'num_days', 'expired_notify_duration']), [
                    'vendor_id' => 'required',
                    'name_en' => 'required',
                    'name_ar' => 'required',
                   // 'branch_id' => 'required',
                    'num_points' => 'required|numeric',
                    'num_days' => 'required|numeric',
                    'expired_notify_duration' => 'required|numeric|less_than:num_days',
                    'price' => array('required', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/')
                        ], $messsages);


        // validation failed
        if ($validator->fails()) {
            return redirect($this->configName . '/classPackages/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->all();
            
             //Increment offer value in vendors module1_offers column  
            if($input['has_offer']==1){
               VendorDetail::incrementOffers(2);
            }

           //Decrement offer value in vendors module1_offers column 
            if($input['has_offer']==0){
             VendorDetail::decrementOffers(2);
            }

            $ClassPackage->fill($input)->save();

            //logActivity
            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] ClassPackage - ' . $request->name_en, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect($this->configName . '/classPackages');
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
        $this->DeleteAccess = Permit::AccessPermission('classPackages-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $all_data = $request->except('_token', 'table-4_length');

        //logActivity
        //fetch title
        $ClassPackage = ClassPackage::
                select('name_en')
                ->whereIn('id', $all_data['ids'])
                ->get();

        $name = $ClassPackage->pluck('name_en');
        $groupname = $name->toJson();

        LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] ClassPackage - ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            
            //Decrement offer value in vendors module1_offers column 
            $ClassPackage = ClassPackage::
                select('has_offer')
                ->where('id', $id)
                ->first();


           if($ClassPackage->has_offer==1)               
             VendorDetail::decrementOffers(2);
           
            ClassPackage::destroy($id);
        }

        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect($this->configName . '/classPackages');
    }

}
