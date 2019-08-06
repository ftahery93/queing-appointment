<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrainersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('trainers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('original_password');
            $table->text('civilid');
            $table->string('mobile')->index()->nullable();
            $table->decimal('commission', 10, 3);
            $table->text('activities')->nullable();
            $table->text('profile_image')->nullable();
            $table->string('acc_name')->nullable();
            $table->bigInteger('acc_num')->index()->nullable();
            $table->bigInteger('ibn_num')->index()->nullable();
            $table->integer('bank_id')->unsigned()->nullable();
            $table->string('contract_name')->nullable();
            $table->date('contract_startdate')->nullable();
            $table->date('contract_enddate')->nullable();
            $table->integer('area')->unsigned()->nullable();
            $table->integer('gender_type')->unsigned()->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->boolean('status')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('trainers');
    }

}
