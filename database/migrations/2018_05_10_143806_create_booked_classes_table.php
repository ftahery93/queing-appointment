<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookedClassesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
//        Schema::create('booked_classes', function (Blueprint $table) {
//            $table->increments('id');
//            $table->integer('subscribed_package_id')->index()->unsigned()->nullable();
//            $table->integer('subscriber_id')->index()->unsigned()->nullable();
//            $table->integer('module_id')->index()->unsigned()->nullable();
//            $table->integer('vendor_id')->index()->unsigned()->nullable();
//            $table->integer('package_id')->index()->unsigned()->nullable();
//            $table->string('package_name')->nullable();
//            $table->decimal('price', 10, 3)->nullable();
//            $table->date('start_date')->nullable();
//            $table->date('end_date')->nullable();
//            $table->boolean('num_points')->nullable()->comment = "Unlimited:0";
//            $table->integer('booked')->unsigned()->nullable();            
//            $table->timestamps();
//        });
    }

    /** //
      }
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
       // Schema::drop('booked_classes');
    }

}
