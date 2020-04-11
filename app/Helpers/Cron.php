<?php

namespace App\Helpers;

use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class Cron {

    public static function moveClassSeats() {
        //Get Vendorname
        $classes = DB::table('classes')
                ->select('id', 'temp_num_seats', 'temp_gym_seats', 'temp_fitflow_seats', 'temp_price', 'temp_commission_perc', 'temp_commission_kd')
                ->where('approved_status', 1)
                ->whereNotNull('temp_num_seats')
                ->whereNotNull('temp_gym_seats')
                ->whereNotNull('temp_fitflow_seats')
                ->whereNotNull('temp_price')
                ->get();

        foreach ($classes as $class) {

            $update = array('num_seats' => $class->temp_num_seats, 'available_seats' => $class->temp_gym_seats, 'fitflow_seats' => $class->temp_fitflow_seats
                , 'price' => $class->temp_price);
            $upd = DB::table('classes')
                    ->where('id', $class->id)
                    ->update($update);


            $updateNull = array('temp_num_seats' => null, 'temp_gym_seats' => null, 'temp_fitflow_seats' => null, 'temp_price' => null
                , 'temp_commission_perc' => null, 'temp_commission_kd' => null);
            DB::table('classes')
                    ->where('id', $class->id)
                    ->update($updateNull);
        }
    }

    //Subscription Expired Check for Vendor and Trainer
    //Set Subscriber  status true   //0:new package;1:active;2:expired
    public static function setSubscriberPackageStatus() {
        $table = 'subscribers_package_details';
        $table2 = 'trainer_subscribers_package_details';


        $expiredStatus = array('active_status' => 2);
        DB::table($table)
                ->whereDate('end_date', '<', Carbon::now())
                ->orwhere(function ($query) {
                    $query->whereColumn('num_booked', '=', 'num_points')
                    ->where('num_points', '>', 0);
                })
                ->where('active_status', '!=', 2)
                ->where('module_id', 1)
                ->whereNotNull('trainer_id')
                ->WhereNull('vendor_id')
                ->update($expiredStatus);

        DB::table($table2)->whereDate('end_date', '<', Carbon::now())
                ->orwhere(function ($query) {
                    $query->whereColumn('num_booked', '=', 'num_points')
                    ->where('num_points', '>', 0);
                })
                ->where('active_status', '!=', 2)
                ->where('module_id', 1)
                ->whereNotNull('trainer_id')
                ->WhereNull('vendor_id')
                ->update($expiredStatus);

        DB::table($table)->whereDate('end_date', '<', Carbon::now())
                ->orwhere(function ($query) {
                    $query->whereColumn('num_booked', '=', 'num_points')
                    ->where('num_points', '>', 0);
                })
                ->where('active_status', '!=', 2)
                ->where('module_id', 2)
                ->orwhere('module_id', 3)
                ->whereNotNull('vendor_id')
                ->WhereNull('trainer_id')
                ->update($expiredStatus);

        $vendors = DB::table('vendors')
                ->select('table_prefix', 'id')
                ->get();

        foreach ($vendors As $vendor) {

            $table_prefix = $vendor->table_prefix;
            $vendor_id = $vendor->id;

            $table3 = $table_prefix . 'subscribers_package_details';
            if (Schema::hasTable($table3)) {
                $expiredStatus = array('active_status' => 2);
                DB::table($table3)->whereDate('end_date', '<', Carbon::now())
                        ->orwhere(function ($query) {
                            $query->whereColumn('num_booked', '=', 'num_points')
                            ->where('num_points', '>', 0);
                        })
                        ->where('active_status', '!=', 2)
                        ->where('module_id', 2)
                        ->orwhere('module_id', 3)
                        ->update($expiredStatus);
            }
        }

        $activeStatus = array('active_status' => 1);
        //Get All Distinct Subscriber having end date greater than today date
        $Subscribers = DB::table($table)
                        ->select(DB::raw('DISTINCT subscriber_id'))
                        ->whereDate('end_date', '>=', Carbon::now())
                        ->where('active_status', 0)->get();

        foreach ($Subscribers As $Subscriber) {

            //for trainer
            DB::table($table)->whereDate('end_date', '>=', Carbon::now())
                    ->where('active_status', 0)
                    ->where('module_id', 1)
                    ->whereNotNull('trainer_id')
                    ->WhereNull('vendor_id')
                    ->where('subscriber_id', $Subscriber->subscriber_id)
                    ->orderby('created_at', 'ASC')
                    ->limit(1)
                    ->update($activeStatus);

            DB::table($table2)->whereDate('end_date', '>=', Carbon::now())
                    ->where('active_status', 0)
                    ->where('module_id', 1)
                    ->whereNotNull('trainer_id')
                    ->WhereNull('vendor_id')
                    ->where('subscriber_id', $Subscriber->subscriber_id)
                    ->orderby('created_at', 'ASC')
                    ->limit(1)
                    ->update($activeStatus);

            //for vendor
            DB::table($table)->whereDate('end_date', '>=', Carbon::now())
                    ->where('active_status', 0)
                    ->where('module_id', 2)
                    ->orwhere('module_id', 3)
                    ->whereNotNull('vendor_id')
                    ->WhereNull('trainer_id')
                    ->where('subscriber_id', $Subscriber->subscriber_id)
                    ->orderby('created_at', 'ASC')
                    ->limit(1)
                    ->update($activeStatus);

            foreach ($vendors As $vendor) {
                $table_prefix = $vendor->table_prefix;
                $vendor_id = $vendor->id;

                $table3 = $table_prefix . 'subscribers_package_details';
                if (Schema::hasTable($table3)) {
                    DB::table($table3)->whereDate('end_date', '>=', Carbon::now())
                            ->where('active_status', 0)
                            ->where('module_id', 2)
                            ->orwhere('module_id', 3)
                            ->whereNotNull('vendor_id')
                            ->WhereNull('trainer_id')
                            ->where('subscriber_id', $Subscriber->subscriber_id)
                            ->orderby('created_at', 'ASC')
                            ->limit(1)
                            ->update($activeStatus);
                }
            }
        }
    }

}
