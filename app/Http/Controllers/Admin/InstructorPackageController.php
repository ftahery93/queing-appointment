<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Input;
use Carbon\Carbon;
use App\Models\Vendor\InstructorPackage;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;

class instructorPackageController extends Controller {

   
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:instructorPackages');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
      
        //Ajax request
        if (request()->ajax()) {          
                     
                $instructorPackageList = InstructorPackage::
                join('vendors', 'vendors.id', '=', 'instructor_packages.vendor_id')
                ->join('vendor_branches', 'instructor_packages.branch_id', '=', 'vendor_branches.id')
                ->select('vendors.name','vendor_branches.name_en As branch_name',  'instructor_packages.name_en', 'instructor_packages.num_points', 'instructor_packages.price', 'instructor_packages.created_at')
                ->where('vendors.status', 1)
                ->whereNull('vendors.deleted_at'); 
          

             //if Request having vendor ID
        if($request->has('id') && $request->get('id')!=0){ 
            $ID=$request->get('id');             
            $instructorPackageList->where('vendors.id', $ID);
        }
            
             $instructorPackage = $instructorPackageList->get();

            return Datatables::of($instructorPackage)
                            ->editColumn('created_at', function ($instructorPackage) {
                                $newYear = new Carbon($instructorPackage->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->make();
                           
                            // return $datatable
        }

        $vendors = DB::table('vendors')
                ->select('name', 'id')
                ->where('status', 1)
                 ->whereNull('deleted_at')
                ->get();
       
        return view('admin.instructorPackages.index')
                        ->with('Vendors', $vendors);
    }
}
