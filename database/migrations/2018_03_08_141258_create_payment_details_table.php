<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_details', function (Blueprint $table) {
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
            $table->string('payment_route')->nullable();
            $table->bigInteger('reference_id')->nullable();            
            $table->decimal('amount', 10, 3);
            $table->date('post_date')->nullable();
             $table->string('result')->nullable();
            $table->bigInteger('payid')->index()->unsigned()->nullable();    
            $table->boolean('card_type')->nullable()->comment = "KNET:1,CC:2";
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
       Schema::dropIfExists('payment_details');
    }
}
