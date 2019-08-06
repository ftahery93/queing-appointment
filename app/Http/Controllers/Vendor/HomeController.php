<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Admin\Vendor;
use Illuminate\Support\Facades\Auth;
use DB;

class HomeController extends Controller {

    protected $guard = 'vendor';
    protected $configName;

    public function __construct() {
        $this->configName = config('global.fitflowVendor');
        $this->middleware('vendor');
    }

    public function index() {

        $userInfo = Auth::guard($this->guard)->user();
        $Vendor = Vendor::findOrFail($userInfo->vendor_id);
        $collection = collect(json_decode($Vendor->modules, true));

        //Get all module       
        $module1 = DB::table('modules')
                ->select('id', 'name_en','slug')
                ->where(array('status' => 1, 'id' => 1))
                ->first();

        $module2 = DB::table('modules')
                ->select('id', 'name_en','slug')
                ->where(array('status' => 1, 'id' => 2))
                ->first();

        $module3 = DB::table('modules')
                ->select('id', 'name_en','slug')
                ->where(array('status' => 1, 'id' => 3))
                ->first();

        $module4 = DB::table('modules')
                ->select('id', 'name_en','slug')
                ->where(array('status' => 1, 'id' => 4))
                ->first();

        return view('fitflowVendor.home')
                        ->with('module1', $module1)
                        ->with('module2', $module2)
                        ->with('module3', $module3)
                        ->with('module4', $module4)
                        ->with('collection', $collection);
    }

}
