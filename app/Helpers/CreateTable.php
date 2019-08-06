<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateTable {

    public static function createTable($table_name, $type) {
        //Type:1 for memebers table
        // check if table is not already exists
        if (!Schema::hasTable($table_name)) {

            if ($type == 1) { //1 for members
                Schema::create($table_name, function (Blueprint $table) use ($table_name) {
                    $table->increments('id');
                    $table->integer('subscriber_id')->nullable();
                    // if (count($fields) > 0) {
                    //     foreach ($fields as $field) {
                    //         $table->{$field['type']}($field['name']);
                    //     }
                    // }
                    $table->string('name')->collation('utf8_unicode_ci')->nullable();
                    $table->string('email')->nullable();
                    $table->string('mobile')->unique()->nullable();
                    $table->string('package_id')->collation('utf8_unicode_ci')->nullable();
                    $table->string('package_name')->nullable();
                    $table->string('package_name_ar')->nullable();
                    $table->decimal('price', 10, 3)->nullable();
                    $table->decimal('cash', 10, 3)->nullable();
                    $table->decimal('knet', 10, 3)->nullable();
                    $table->integer('area_id')->nullable();
                    $table->integer('gender_id')->nullable();
                    $table->date('dob')->nullable();
                    $table->date('start_date')->nullable();
                    $table->date('end_date')->nullable();
                    $table->date('notification_date')->index()->nullable();
                    $table->boolean('subscribed_from')->default(false)->comment = "vendor panel:0,fiflow applicationt:1";
                    $table->boolean('subscription')->default(false)->comment = "new:0,renew:1";
                    $table->boolean('status')->default(true);
                    $table->softDeletes();
                    $table->timestamps();
                });
            }

            if ($type == 2) { //2 for Subscribers Package Details
                Schema::create($table_name, function (Blueprint $table) use ($table_name) {
                    $table->increments('id');
                    $table->integer('subscriber_id')->index()->unsigned()->nullable();
                    $table->integer('member_id')->index()->unsigned()->nullable();
                    $table->integer('package_id')->index()->unsigned()->nullable();
                    $table->integer('module_id')->index()->unsigned()->nullable();
                    $table->integer('vendor_id')->index()->unsigned()->nullable();
                    $table->foreign('vendor_id')
                            ->references('id')->on('vendors');
                    $table->integer('trainer_id')->index()->unsigned()->nullable();
                    $table->foreign('trainer_id')
                            ->references('id')->on('trainers');
                    $table->integer('payment_id')->index()->unsigned()->nullable();
                    $table->foreign('payment_id')
                            ->references('id')->on('payment_details');
                    $table->string('name_en')->nullable();
                    $table->string('name_ar')->nullable();
                    $table->text('description_en')->nullable();
                    $table->text('description_ar')->nullable();
                    $table->string('area_name_en')->nullable();
                    $table->string('area_name_ar')->nullable();
                    $table->boolean('num_points')->nullable()->comment = "Unlimited:0";
                    $table->decimal('price', 10, 3);
                    $table->decimal('commission', 10, 3)->comment = "Admin Commission";
                    $table->decimal('profit', 10, 3)->comment = "vendor/trainer";
                    $table->integer('num_days')->nullable();
                    $table->date('notification_date')->index()->nullable();
                    $table->date('start_date')->index()->nullable();
                    $table->date('end_date')->index()->nullable();
                    $table->decimal('cash', 10, 3)->nullable();
                    $table->decimal('knet', 10, 3)->nullable();
                    $table->integer('num_booked')->unsigned()->default(false);
                    $table->integer('vendor_package_reference_id')->unsigned()->default(false);
                    $table->boolean('active_status')->default(false)->comment = "0:new package;1:active;2:expired";
                    $table->timestamps();
                });
            }

            if ($type == 3) { //3 for invoice
                Schema::create($table_name, function (Blueprint $table) use ($table_name) {
                    $table->increments('id');
                    $table->integer('receipt_num')->index()->unsigned()->nullable();
                    $table->integer('member_id')->index()->unsigned()->nullable();
                    $table->integer('subscribed_package_id')->index()->unsigned()->nullable();
                    $table->integer('package_id')->index()->unsigned()->nullable();
                    $table->string('package_name')->nullable();
                    $table->date('start_date')->index()->nullable();
                    $table->date('end_date')->index()->nullable();
                    $table->integer('collected_by')->nullable();
                    $table->decimal('cash', 10, 3)->nullable();
                    $table->decimal('knet', 10, 3)->nullable();
                    $table->decimal('price', 10, 3)->nullable();
                    $table->timestamps();
                });
            }

            if ($type == 4) { //2 for Booking Classes
                Schema::create($table_name, function (Blueprint $table) use ($table_name) {
                    $table->increments('id');
                    $table->integer('subscribed_package_id')->index()->unsigned()->nullable();
                    $table->integer('subscriber_id')->index()->unsigned()->nullable();
                    $table->integer('module_id')->index()->unsigned()->nullable();
                    $table->integer('vendor_id')->index()->unsigned()->nullable();
                    $table->integer('class_id')->index()->unsigned()->nullable();
                    $table->integer('class_master_id')->index()->unsigned()->nullable();
                    $table->integer('schedule_id')->index()->unsigned()->nullable();
                    $table->integer('governorate_id')->index()->unsigned()->nullable();
                    $table->integer('branch_id')->index()->unsigned()->nullable();
                    $table->integer('vendor_package_reference_id')->unsigned();
                    $table->softDeletes();
                    $table->timestamps();
                });
            }
        }
    }

}
