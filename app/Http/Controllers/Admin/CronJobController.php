<?php

namespace App\Http\Controllers\Admin;

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
use App\Helpers\Cron;

class CronJobController extends Controller {

    public function __construct() {
        
    }
    
     public function index(Request $request) {         
       Cron::moveClassSeats();
        Cron::setSubscriberPackageStatus();  
     }

}
