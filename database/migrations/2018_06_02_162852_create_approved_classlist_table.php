<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovedClasslistTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('approved_classlist', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id')->index()->unsigned()->nullable();
            $table->boolean('viewed')->default(false)->comment = "1:vendor viewed";
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('approved_classlist');
    }

}
