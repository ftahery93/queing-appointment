<?php

namespace App\Http\Controllers\Admin;

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

class ClassPackageController extends Controller {

   
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:classPackages');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
      
        //Ajax request
        if (request()->ajax()) {          
                     
                $ClassPackageList = ClassPackage::
               join('vendors', 'vendors.id', '=', 'class_packages.vendor_id')
                ->select('vendors.name',  'class_packages.name_en', 'class_packages.num_points','class_packages.num_days','class_packages.price', 'class_packages.created_at')
                ->where('vendors.status', 1)
                ->whereNull('vendors.deleted_at'); 
          

             //if Request having Class ID
        if($request->has('id') && $request->get('id')!=0){ 
            $ID=$request->get('id');             
            $ClassPackageList->where('vendors.id', $ID);
        }
            
             $ClassPackage = $ClassPackageList->get();
             
            return Datatables::of($ClassPackage)
                            ->editColumn('created_at', function ($ClassPackage) {
                                $newYear = new Carbon($ClassPackage->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('num_points', function ($ClassPackage) {
                                return $ClassPackage->num_points == 0 ? 'Unlimited' : $ClassPackage->num_points;
                            })->make();
                           
                            // return $datatable
        }

        $Vendors = DB::table('vendors')
                ->select('name', 'id')
                ->where('status', 1)
                ->whereNull('vendors.deleted_at')
                ->get();

        return view('admin.classPackages.index')
                         ->with('Vendors', $Vendors);
    }
}
