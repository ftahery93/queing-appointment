<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductSpecialTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('product_special', function (Blueprint $table) {
            $table->increments('product_special_id');
            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')
                    ->references('product_id')->on('products')
                    ->onDelete('cascade');
            $table->decimal('price', 10, 3)->default('0.000');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('product_special');
    }

}
