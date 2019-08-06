<?php

namespace App\Http\Controllers\Admin;

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

class vendorPackageController extends Controller {

   
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:vendorPackages');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
      
        //Ajax request
        if (request()->ajax()) {          
                     
                $vendorPackageList = vendorPackage::
                join('vendors', 'vendors.id', '=', 'vendor_packages.vendor_id')
                ->join('vendor_branches', 'vendor_packages.branch_id', '=', 'vendor_branches.id')
                ->select('vendors.name','vendor_branches.name_en As branch_name',  'vendor_packages.name_en', 'vendor_packages.num_days', 'vendor_packages.price', 'vendor_packages.created_at')
                ->where('vendors.status', 1)
                ->whereNull('vendors.deleted_at'); 
          

             //if Request having vendor ID
        if($request->has('id') && $request->get('id')!=0){ 
            $ID=$request->get('id');             
            $vendorPackageList->where('vendors.id', $ID);
        }
            
             $vendorPackage = $vendorPackageList->get();

            return Datatables::of($vendorPackage)
                            ->editColumn('created_at', function ($vendorPackage) {
                                $newYear = new Carbon($vendorPackage->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('num_points', function ($vendorPackage) {
                                return $vendorPackage->num_points == 0 ? 'Unlimited' : $vendorPackage->num_points;
                            })->make();
                           
                            // return $datatable
        }

        $vendors = DB::table('vendors')
                ->select('name', 'id')
                ->where('status', 1)
                 ->whereNull('deleted_at')
                ->get();

        return view('admin.vendorPackages.index')
                        ->with('Vendors', $vendors);
    }
}
