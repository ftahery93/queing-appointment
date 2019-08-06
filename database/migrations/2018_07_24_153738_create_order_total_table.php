<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderTotalTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('order_total', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->foreign('order_id')
                    ->references('id')->on('orders')
                    ->onDelete('cascade');
            $table->decimal('sub_total', 10, 3)->default('0.000');
            $table->decimal('delivery_charge', 10, 3)->default('0.000');
            $table->decimal('coupon_discount', 10, 3)->default('0.000');
            $table->decimal('total', 10, 3)->default('0.000');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('order_total');
    }

}
