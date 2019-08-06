<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Artisan;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CacheController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function configCache(Request $request) {
        try {
            Artisan::call('config:cache');
            $output = Artisan::output();
        } catch (Exception $e) {
            Flash::error($e->getMessage());
            return redirect()->back();
        }
    }

    public function routeCache(Request $request) {
        try {
            Artisan::call('route:cache');
            $output = Artisan::output();
        } catch (Exception $e) {
            Flash::error($e->getMessage());
            return redirect()->back();
        }
    }


    public function optimize(Request $request) {
        try {
            Artisan::call('optimize');
            $output = Artisan::output();
        } catch (Exception $e) {
            Flash::error($e->getMessage());
            return redirect()->back();
        }
    }
    
    //Cache Clear 
     public function configCacheClear(Request $request) {
        try {
            Artisan::call('config:clear');
            $output = Artisan::output();
        } catch (Exception $e) {
            Flash::error($e->getMessage());
            return redirect()->back();
        }
    }

    public function routeCacheClear(Request $request) {
        try {
            Artisan::call('route:clear');
            $output = Artisan::output();
        } catch (Exception $e) {
            Flash::error($e->getMessage());
            return redirect()->back();
        }
    }

    public function viewCacheClear(Request $request) {
        try {
            Artisan::call('view:clear');
            $output = Artisan::output();
        } catch (Exception $e) {
            Flash::error($e->getMessage());
            return redirect()->back();
        }
    }
    
     public function cacheClear(Request $request) {
        try {
            Artisan::call('cache:clear');
            $output = Artisan::output();
        } catch (Exception $e) {
            Flash::error($e->getMessage());
            return redirect()->back();
        }
    }
    
   

}
