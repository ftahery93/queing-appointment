<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassRatingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('class_ratings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('subscriber_id')->index()->unsigned()->nullable();
            $table->integer('module_id')->index()->unsigned()->nullable();
            $table->integer('vendor_id')->index()->unsigned()->nullable();
            $table->integer('class_id')->index()->unsigned()->nullable();
            $table->integer('rate')->unsigned()->nullable();            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('class_ratings');
    }
}
