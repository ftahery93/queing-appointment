<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('classes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id')->unsigned();
            $table->foreign('vendor_id')
                    ->references('id')->on('vendors');
            $table->integer('branch_id')->unsigned()->nullable();
            $table->integer('class_master_id')->unsigned()->nullable();
            $table->text('description_en')->collation('utf8_unicode_ci')->nullable();
            $table->text('description_ar')->collation('utf8_unicode_ci')->nullable();
            $table->string('trainer_name_en')->collation('utf8_unicode_ci')->nullable();
            $table->string('trainer_name_ar')->collation('utf8_unicode_ci')->nullable();
            $table->text('hours')->nullable();
            $table->string('gender_type')->index()->nullable();
            $table->decimal('price', 10, 3);
            $table->boolean('status')->default(true);
            $table->integer('num_seats')->unsigned()->nullable();
            $table->integer('available_seats')->unsigned()->nullable()->comment = "Gym Seats";
            $table->integer('fitflow_seats')->unsigned()->nullable();
            $table->integer('temp_gym_seats')->unsigned()->nullable();
            $table->integer('temp_fitflow_seats')->unsigned()->nullable();
            $table->boolean('approved_status')->default(false)->comment = "Pending:0;Approved:1;Rejected:2";
            $table->text('reason')->nullable()->comment = "Reason for approved and rejected class";
            $table->text('rating')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('classes');
    }

}
