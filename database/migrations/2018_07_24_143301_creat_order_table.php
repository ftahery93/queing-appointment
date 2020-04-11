<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatOrderTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_no')->unsigned();
            $table->string('invoice_prefix', 26)->nullable();
            $table->integer('vendor_id')->unsigned();
            $table->integer('customer_id')->unsigned();
            $table->string('name', 32)->nullable();
            $table->string('email', 96)->nullable();
            $table->string('mobile', 32)->nullable();
            $table->integer('area_id')->unsigned();
            $table->integer('gender_id')->unsigned();
            $table->date('dob')->nullable();
            $table->text('comment')->nullable();
            $table->decimal('total', 10, 3)->default('0.000');
            $table->integer('order_status_id')->default(false);
            $table->decimal('commission', 10, 3)->default('0.000')->comment = "Admin Commission";
            $table->decimal('profit', 10, 3)->default('0.000')->comment = "vendor";
            $table->text('address_area')->nullable();
            $table->text('address_street')->nullable();
            $table->text('address_house_building_num')->nullable()->comment = "House/Building No.";
            $table->text('address_avenue')->nullable();
            $table->text('address_floor')->nullable();
            $table->text('address_flat')->nullable();
            $table->text('address_block')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('order');
    }

}
