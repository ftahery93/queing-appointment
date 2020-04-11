<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use App\Models\Admin\Vendor;
use DB;
use Carbon\Carbon;

class TrainerDetail {

    //Set Subscriber  status true   //0:new package;1:active;2:expired
    public static function setSubscriberPackageStatus() {

//        $table = 'trainer_subscribers_package_details';
//        $table2 = 'subscribers_package_details';
//
//        $expiredStatus = array('active_status' => 2);
//        DB::table($table)
//                ->whereDate('end_date', '<', Carbon::now())
//                ->where('active_status', '!=',2)
//                ->orwhere(function ($query) {
//                    $query->whereColumn('num_booked', '=', 'num_points')
//                    ->where('num_points', '>', 0);
//                })
//                ->where('module_id', 1)
//                ->update($expiredStatus);
//
//        $activeStatus = array('active_status' => 1);
//        DB::table($table)
//                ->whereDate('end_date', '>=', Carbon::now())
//                ->where('active_status', 0)
//                ->whereColumn('num_booked', '<', 'num_points')
//                ->where('num_points', '>', 0)
//                ->where('module_id', 1)
//                ->update($activeStatus);
//
//        //for unlimited classes
//        $activeStatus = array('active_status' => 1);
//        DB::table($table)->whereDate('end_date', '>=', Carbon::now())
//                ->where('num_points', '=', 0)
//                ->where('module_id', 1)
//                ->update($activeStatus);
//
//        //package status update in subscribers_package_details table
//        $expiredStatus = array('active_status' => 2);
//        DB::table($table2)->whereDate('end_date', '<', Carbon::now())
//                ->where('active_status', '!=',2)
//                ->orwhere(function ($query) {
//                    $query->whereColumn('num_booked', '=', 'num_points')
//                    ->where('num_points', '>', 0);
//                })
//                ->where('module_id', 1)
//                ->whereNotNull('trainer_id')
//                ->WhereNull('vendor_id')
//                ->update($expiredStatus);
//
//        $activeStatus = array('active_status' => 1);
//        DB::table($table2)->whereDate('end_date', '>=', Carbon::now())
//                ->whereColumn('num_booked', '<', 'num_points')
//                ->where('num_points', '>', 0)
//                ->where('module_id', 1)
//                ->whereNotNull('trainer_id')
//                ->WhereNull('vendor_id')
//                ->update($activeStatus);
//
//        $activeStatus = array('active_status' => 1);
//        DB::table($table2)->whereDate('end_date', '>=', Carbon::now())
//                ->where('num_points', '=', 0)
//                ->where('module_id', 1)
//                ->whereNotNull('trainer_id')
//                ->WhereNull('vendor_id')
//                ->update($activeStatus);
    }

    public static function setclassAtttend($id) {

        $table = 'trainer_subscribers_package_details';
        $table2 = 'subscribers_package_details';

        //Get number of class attend
        $classAttend = DB::table($table)->select('num_booked', 'start_date', 'end_date', 'subscriber_id')->where('id', $id)->first();

        $totalAttend = $classAttend->num_booked + 1;

        $activeStatus = array('num_booked' => $totalAttend);

        DB::table($table)->where('id', $id)->update($activeStatus);

        //subscribers_package_details table

        DB::table($table2)->whereDate('start_date', $classAttend->start_date)
                ->whereDate('end_date', $classAttend->end_date)
                ->where('module_id', 1)
                ->where('subscriber_id', $classAttend->subscriber_id)
                ->where('trainer_id', Auth::guard('trainer')->user()->id)
                ->update($activeStatus);
    }
    
     //increment value if any offer in module  
    public static function incrementOffers($id) {
        DB::table('trainers')->whereId($id)->increment('offers');
    }

    //decrement value if any offer in module  
    public static function decrementOffers($id) {
        DB::table('trainers')->whereId($id)->where('offers', '!=', 0)->decrement('offers');
    }

}
