<?php

namespace App\Http\Controllers\Vendor\Module1;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\VendorDetail;

class FavouriteController extends Controller {

    protected $guard = 'vendor';
    protected $configName;

    public function __construct() {
        $this->middleware($this->guard);
        $this->configName = config('global.fitflowVendor');
        $this->middleware('vendorPermission:reports');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $favouriteList = DB::table('favourites')
                ->select('subscriber_id','created_at')
                ->where('vendor_id', VendorDetail::getID());
    
         //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $favouriteList->whereBetween('created_at', [$start_date, $end_date]);
        }
        $Favourites = $favouriteList->get();
        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Favourites)
                            ->editColumn('subscriber_id', function ($Favourites) {
                                //Get Subscriber name
                                $username = DB::table('registered_users')
                                        ->select('name')
                                        ->where('id', $Favourites->subscriber_id)
                                        ->first();
                                return $username->name;
                            })
                            ->editColumn('created_at', function ($Favourites) {
                                $newYear = new Carbon($Favourites->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->make();
        }

        return view('fitflowVendor.module1.reports.favourites');
    }

}
