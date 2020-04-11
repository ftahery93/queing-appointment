<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscribersPackageDetailsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('subscribers_package_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('subscriber_id')->index()->unsigned()->nullable();
            $table->integer('package_id')->index()->unsigned()->nullable();
            $table->integer('module_id')->index()->unsigned()->nullable();
            $table->integer('vendor_id')->index()->unsigned()->nullable();
            $table->integer('trainer_id')->index()->unsigned()->nullable();
            $table->foreign('vendor_id')
                    ->references('id')->on('vendors');
            $table->foreign('trainer_id')
                    ->references('id')->on('trainers');
            $table->integer('payment_id')->index()->unsigned()->nullable();
            $table->foreign('payment_id')
                    ->references('id')->on('payment_details');
            $table->string('name_en')->nullable();
            $table->string('name_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
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
            $table->boolean('active_status')->default(false)->comment = "0:new package;1:active;2:expired";
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('subscribers_package_details');
    }

}
