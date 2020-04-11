<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\Admin\Vendor;
use DB;
use Carbon\Carbon;

class VendorDetail {

    public static function getName() {
        //Get Vendorname
        $vendor = Vendor::
                select('name')
                ->where('id', Auth::guard('vendor')->user()->vendor_id)
                ->first();

        return $vendor->name;
    }

    public static function getID() {

        return Auth::guard('vendor')->user()->vendor_id;
    }

    public static function getPrefix() {
        //Get tablePrefix
        $vendor = Vendor::
                select('table_prefix')
                ->where('id', Auth::guard('vendor')->user()->vendor_id)
                ->first();

        return $vendor->table_prefix;
    }

    public static function getProfitCommission($price, $var) {

        //Get tablePrefix
        $vendor = Vendor::
                select('commission')
                ->where('id', Auth::guard('vendor')->user()->vendor_id)
                ->first();

        $collection = collect(json_decode($vendor->commission, true));
        $commission = ($price * $collection['1']) / 100;
        $profit = $price - $commission;
        //if $var=1 commission
        if ($var == 1) {
            return $commission;
        } else {
            return $profit;
        }
    }

    public static function getSalesCountDate() {

        //Get tablePrefix
        $vendor = Vendor::
                select('sale_setting')
                ->where('id', Auth::guard('vendor')->user()->vendor_id)
                ->first();

        return $vendor->sale_setting;
    }

    public static function getEmailID() {

        //Get tablePrefix
        $vendor = Vendor::
                select('email')
                ->where('id', Auth::guard('vendor')->user()->vendor_id)
                ->first();

        return $vendor->email;
    }

    //Set Subscriber  status    //0:new package;1:active;2:expired
    public static function setSubscriberPackageStatus() {

//        $table = VendorDetail::getPrefix() . 'subscribers_package_details';
//        $table2 = 'subscribers_package_details';
//        $expiredStatus = array('active_status' => 2);
//        DB::table($table)->whereDate('end_date', '<', Carbon::now())
//               ->where('active_status', '!=',2)
//                ->orwhere(function ($query) {
//                    $query->whereColumn('num_booked', '=', 'num_points')
//                    ->where('num_points', '>', 0);
//                })
//                ->where('module_id', 2)
//                ->update($expiredStatus);
//
//        $activeStatus = array('active_status' => 1);
//        DB::table($table)->whereDate('end_date', '>=', Carbon::now())
//                ->whereColumn('num_booked', '<', 'num_points')
//                ->where('num_points', '>', 0)
//                ->where('module_id', 2)
//                ->update($activeStatus);
//
//        //for unlimited classes
//        $activeStatus = array('active_status' => 1);
//        DB::table($table)->whereDate('end_date', '>=', Carbon::now())
//                ->where('num_points', '=', 0)
//                ->where('module_id', 2)
//                ->update($activeStatus);
//
//
//        //package status update in subscribers_package_details table
//        $expiredStatus = array('active_status' => 2);
//        DB::table($table2)->whereDate('end_date', '<', Carbon::now())
//                ->where('active_status', '!=',2)
//                ->orwhere(function ($query) {
//                    $query->whereColumn('num_booked', '=', 'num_points')
//                    ->where('num_points', '>', 0);
//                })
//                ->where('module_id', 2)
//                ->where('vendor_id', VendorDetail::getID())
//                ->update($expiredStatus);
//
//        $activeStatus = array('active_status' => 1);
//        DB::table($table2)->whereDate('end_date', '>=', Carbon::now())
//                ->whereColumn('num_booked', '<', 'num_points')
//                ->where('num_points', '>', 0)
//                ->where('module_id', 2)
//                ->where('vendor_id', VendorDetail::getID())
//                ->update($activeStatus);
//
//        //for unlimited classes
//        $activeStatus = array('active_status' => 1);
//        DB::table($table2)->whereDate('end_date', '>=', Carbon::now())
//                ->where('num_points', '=', 0)
//                ->where('module_id', 2)
//                ->where('vendor_id', VendorDetail::getID())
//                ->update($activeStatus);
    }

    //increment value if any offer in module  
    public static function incrementOffers($module) {

        if ($module == 1)
            $module_offer = 'module1_offers';

        if ($module == 2)
            $module_offer = 'module2_offers';

        DB::table('vendors')->whereId(VendorDetail::getID())->increment($module_offer);
    }

    //decrement value if any offer in module  
    public static function decrementOffers($module) {

        if ($module == 1)
            $module_offer = 'module1_offers';

        if ($module == 2)
            $module_offer = 'module2_offers';
        DB::table('vendors')->whereId(VendorDetail::getID())->where($module_offer, '!=', 0)->decrement($module_offer);
    }

    //Set Subscriber  status for all vendors  //0:new package;1:active;2:expired
    public static function setSubscriberPackageStatusForAllVendors() {

//        $vendors = Vendor::
//                select('table_prefix', 'id')
//                ->get();
//
//        foreach ($vendors As $vendor) {
//
//            $table_prefix = $vendor->table_prefix;
//            $vendor_id = $vendor->id;
//
//            $table = $table_prefix . 'subscribers_package_details';
//            if (Schema::hasTable($table)) {
//                $table2 = 'subscribers_package_details';
//                $expiredStatus = array('active_status' => 2);
//                DB::table($table)->whereDate('end_date', '<', Carbon::now())
//                        ->where('active_status', '!=',2)
//                        ->orwhere(function ($query) {
//                            $query->whereColumn('num_booked', '=', 'num_points')
//                            ->where('num_points', '>', 0);
//                        })
//                        ->where('module_id', 2)
//                        ->orwhere('module_id', 3)
//                        ->update($expiredStatus);
//
//                $activeStatus = array('active_status' => 1);
//                DB::table($table)->whereDate('end_date', '>=', Carbon::now())
//                        ->whereColumn('num_booked', '<', 'num_points')
//                        ->where('num_points', '>', 0)
//                        ->where('module_id', 2)
//                        ->orwhere('module_id', 3)
//                        ->update($activeStatus);
//
//                //for unlimited classes
//                $activeStatus = array('active_status' => 1);
//                DB::table($table)->whereDate('end_date', '>=', Carbon::now())
//                        ->where('num_points', '=', 0)
//                        ->where('module_id', 2)
//                        ->orwhere('module_id', 3)
//                        ->update($activeStatus);
//
//
//                //package status update in subscribers_package_details table
//                $expiredStatus = array('active_status' => 2);
//                DB::table($table2)->whereDate('end_date', '<', Carbon::now())
//                        ->where('active_status', '!=',2)
//                        ->orwhere(function ($query) {
//                            $query->whereColumn('num_booked', '=', 'num_points')
//                            ->where('num_points', '>', 0);
//                        })
//                        ->where('module_id', 2)
//                        ->orwhere('module_id', 3)
//                        ->where('vendor_id', $vendor_id)
//                        ->update($expiredStatus);
//
//                $activeStatus = array('active_status' => 1);
//                DB::table($table2)->whereDate('end_date', '>=', Carbon::now())
//                        ->whereColumn('num_booked', '<', 'num_points')
//                        ->where('num_points', '>', 0)
//                        ->where('module_id', 2)
//                        ->orwhere('module_id', 3)
//                        ->where('vendor_id', $vendor_id)
//                        ->update($activeStatus);
//
//                //for unlimited classes
//                $activeStatus = array('active_status' => 1);
//                DB::table($table2)->whereDate('end_date', '>=', Carbon::now())
//                        ->where('num_points', '=', 0)
//                        ->where('module_id', 2)
//                        ->orwhere('module_id', 3)
//                        ->where('vendor_id', $vendor_id)
//                        ->update($activeStatus);
//            }
//        }
    }

    //Get Area name
    public static function getArea($lang, $packageID) {  //$lang:1 for en, 2:for ar
        $area = DB::table('vendor_packages')
                ->select('areas.name_en', 'areas.name_ar')
                ->join('vendor_branches', 'vendor_branches.id', '=', 'vendor_packages.branch_id')
                ->join('areas', 'areas.id', '=', 'vendor_branches.area')
                ->where('vendor_packages.vendor_id', VendorDetail::getID())
                ->where('vendor_packages.id', $packageID)
                ->first();

        if ($lang == 2)
            return $area->name_ar;
        else
            return $area->name_en;
    }

}
