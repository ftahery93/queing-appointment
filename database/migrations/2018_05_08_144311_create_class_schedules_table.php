<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassSchedulesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('class_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id')->unsigned();
            $table->foreign('vendor_id')
                    ->references('id')->on('vendors');
            $table->integer('class_id')->unsigned();
            $table->foreign('class_id')
                    ->references('id')->on('classes');
            $table->date('schedule_date')->nullable();
            $table->time('start')->nullable();
            $table->time('end')->nullable();
            $table->integer('booked')->unsigned()->nullable()->comment = "Gym Booking";
            $table->integer('app_booked')->unsigned()->nullable()->comment = "Fitflow Booking";
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('class_schedules');
    }

}
