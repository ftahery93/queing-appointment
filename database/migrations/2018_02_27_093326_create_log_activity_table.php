<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogActivityTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('log_activities', function (Blueprint $table) {

            $table->increments('id');

            $table->string('subject');

            $table->string('url');

            $table->string('method');

            $table->string('ip');

            $table->string('agent')->nullable();

            $table->integer('user_id')->nullable();

            $table->boolean('user_type')->nullable()->comment = "Admin:0,Vendor:1,Trainer:2";

            $table->integer('vendor_id')->nullable();

            $table->integer('trainer_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('log_activities');
    }

}
