<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id')->unsigned();
            $table->string('name_en')->nullable();
            $table->string('name_ar')->nullable();
             $table->string('code')->nullable();
            $table->boolean('type',1)->default(1)->comment = "Percentage:1;Fixed Amount:2";
            $table->decimal('discount', 10, 3)->default('0.000');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('uses_total')->unsigned();
             $table->string('uses_customer',11)->nullable();
             $table->boolean('status')->default(true);
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
        Schema::drop('coupons');
    }
}
