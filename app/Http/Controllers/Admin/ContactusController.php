<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use App\Models\Admin\Contactus;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;

class ContactusController extends Controller {

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:contactus');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $Contactus = Contactus::
                select('fullname', 'email', 'mobile', 'message', 'created_at')
                ->get();

         
        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Contactus)
                            ->editColumn('created_at', function ($Contactus) {
                                $newYear = new Carbon($Contactus->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->make();
        }
           
        return view('admin.contactus.index');
    }

}
