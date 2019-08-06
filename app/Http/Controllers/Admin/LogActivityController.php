<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use App\Models\Admin\LogActivity;
use App\Models\Admin\VendorLogActivity;
use App\Models\Admin\TrainerLogActivity;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;

class LogActivityController extends Controller {

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:logActivity');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $LogActivity = LogActivity::
                select('subject', 'ip', 'url', 'created_at')
                ->whereNotIn('user_id', [1])
                ->where('user_type', 0)
                ->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($LogActivity)
                            ->editColumn('created_at', function ($LogActivity) {
                                $newYear = new Carbon($LogActivity->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->make();
        }

        return view('admin.logActivity.adminlog');
    }

    //VendorLog
    public function vendorLog(Request $request) {

        //Ajax request
        if (request()->ajax()) {

            if ($request->has('id') && $request->get('id') != 0) {
                $ID = $request->get('id');
                $LogActivity = VendorLogActivity::
                        select('subject', 'ip', 'url', 'created_at')
                        ->whereNotIn('user_id', [1])
                        ->where('user_type', 1)
                        ->where('vendor_id', 'like', "$ID%")
                        ->get();
            } else {
               $LogActivity = VendorLogActivity::
                        select('subject', 'ip', 'url', 'created_at')
                        ->whereNotIn('user_id', [1])
                        ->where('user_type', 1)
                        ->get();
            }

            return Datatables::of($LogActivity)
                            ->editColumn('created_at', function ($LogActivity) {
                                $newYear = new Carbon($LogActivity->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->make();
        }
        $Vendors = DB::table('vendors')
                ->select('name', 'id')
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->get();

        return view('admin.logActivity.vendorlog')->with('Vendors', $Vendors);
    }
    
     //TrainerLog
    public function trainerLog(Request $request) {

        //Ajax request
        if (request()->ajax()) {

            if ($request->has('id') && $request->get('id') != 0) {
                $ID = $request->get('id');
                $LogActivity = LogActivity::
                        select('subject', 'ip', 'url', 'created_at')
                        //->whereNotIn('user_id', [1])
                        ->where('user_type', 2)
                        ->where('trainer_id', 'like', "$ID%")
                        ->get();
            } else {
               $LogActivity = LogActivity::
                        select('subject', 'ip', 'url', 'created_at')
                        //->whereNotIn('user_id', [1])
                        ->where('user_type', 2)
                        ->get();
            }

            return Datatables::of($LogActivity)
                            ->editColumn('created_at', function ($LogActivity) {
                                $newYear = new Carbon($LogActivity->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->make();
        }
       $Trainers = DB::table('trainers')
                ->select('name', 'id')
                ->where('status', 1)
               ->whereNull('deleted_at')
                ->get();

        return view('admin.logActivity.trainerlog') ->with('Trainers', $Trainers);
    }

}
