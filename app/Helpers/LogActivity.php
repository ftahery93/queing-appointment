<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Auth;
use Request;
use DB;
use App\Models\Admin\LogActivity as LogActivityModel;
use App\Models\Admin\VendorLogActivity as VendorLogActivityModel;
//use App\Models\Admin\TrainerLogActivity as TrainerLogActivityModel;

class LogActivity
{   
     
      public static function addToLog($module, $subject)

    {

    	$log = [];

        $username=auth()->check() ? auth()->user()->username : '';

    	$log['subject'] = $module.' has been '.$subject.' by '.$username;
    	$log['url'] = Request::fullUrl();

    	$log['method'] = Request::method();

    	$log['ip'] = Request::ip();

    	$log['agent'] = Request::header('user-agent');

    	$log['user_id'] = auth()->check() ? auth()->user()->id : 1;

	$log['vendor_id'] = 0;
        
        $log['trainer_id']=0;
		
        if (Auth::guard('trainer')->check()) {
         $log['user_type'] = 2; //Admin:0, vendor:1, trainer:2
          $log['trainer_id'] =Auth::guard('trainer')->user()->id; //trainer login id
          $log['subject'] = $module.' has been '.$subject.' by '.Auth::guard('trainer')->user()->username;
           LogActivityModel::create($log);
        }
        
        if (Auth::guard('vendor')->check()) {
         $log['user_id'] = Auth::guard('vendor')->check() ? Auth::guard('vendor')->user()->id : 1;
         $log['user_type'] = 1; //Admin:0, vendor:1, trainer:2
         $log['vendor_id'] =Auth::guard('vendor')->user()->vendor_id; //trainer login id
         $log['subject'] = $module.' has been '.$subject.' by '.Auth::guard('vendor')->user()->username;
         VendorLogActivityModel::create($log);
        }else{
            $log['user_type'] = 0;
            LogActivityModel::create($log);
        }
        
		
    	

    }


    public static function logActivityLists()

    {

    	return LogActivityModel::latest()->get();

    }
}