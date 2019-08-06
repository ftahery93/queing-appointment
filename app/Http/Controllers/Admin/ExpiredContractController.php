<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Input;
use Carbon\Carbon;
use App\Models\Admin\Vendor;
use App\Models\Admin\Trainer;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;

class ExpiredContractController extends Controller {

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:contractExpired');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        
        $Contracts = collect([]);

        //Ajax request
        if (request()->ajax()) {
            if ($request->has('type') && $request->get('type') == 1) { //Type:1 Vendor
                $Contracts = Vendor::
                        select('name', 'contract_name', 'contract_startdate', 'contract_enddate','id')
                        ->whereDate('contract_enddate', '<', Carbon::now())
                        ->whereNull('deleted_at')
                        ->get();
            }
            if ($request->has('type') && $request->get('type') == 2) { //Type:2 Trainer
                $Contracts = Trainer::
                        select('name', 'contract_name', 'contract_startdate', 'contract_enddate','id')
                        ->whereDate('contract_enddate', '<', Carbon::now())
                        ->whereNull('deleted_at')
                        ->get();
            }



            return Datatables::of($Contracts)
                            ->editColumn('contract_startdate', function ($VendorPackage) {
                                $newYear = new Carbon($VendorPackage->contract_startdate);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('contract_enddate', function ($VendorPackage) {
                                $newYear = new Carbon($VendorPackage->contract_enddate);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('action', function ($Contracts) use($request) {
                                if ($request->get('type')==2)
                                    return '<a href="' . url('admin/trainers') . '/' . $Contracts->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>';
                           if ($request->get('type')==1)
                                    return '<a href="' . url('admin/vendors') . '/' . $Contracts->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>';
                                })->make();

            // return $datatable
        }



        return view('admin.expiredContracts.index');
    }

}
