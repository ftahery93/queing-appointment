<?php

namespace App\Http\Controllers\Vendor;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use App\Models\Admin\VendorLogActivity;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\VendorDetail;

class LogActivityController extends Controller {

    protected $guard = 'vendor';
    protected $configName;
    
    public function __construct() {
        $this->middleware($this->guard);
        $this->configName = config('global.fitflowVendor');
        $this->middleware('vendorPermission:logActivity');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $Activity = VendorLogActivity::
                select('subject', 'ip', 'url', 'created_at')
                //->whereNotIn('user_id', [1])
                ->where('vendor_id', VendorDetail::getID());
        
        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $Activity->whereBetween('created_at',  [$start_date, $end_date]);
        }
       
        $LogActivity = $Activity->get();
        
       
        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($LogActivity)
                            ->editColumn('created_at', function ($LogActivity) {
                                $newYear = new Carbon($LogActivity->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('subject', function ($LogActivity) {
                                $text = explode(']', $LogActivity->subject, 2)[1];
                                return $text;
                            })
                            ->make();
        }

        return view('fitflowVendor.logActivity.index');
    }

}
