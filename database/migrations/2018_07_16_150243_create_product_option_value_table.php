<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductOptionValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_option_value', function (Blueprint $table) {
            $table->increments('product_option_value_id');
            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')
                    ->references('product_id')->on('products')
                    ->onDelete('cascade');
            $table->integer('product_option_id')->unsigned()->nullable();
            $table->integer('option_id')->unsigned()->nullable();
            $table->integer('option_value_id')->unsigned()->nullable();
            $table->integer('quantity')->unsigned()->default(false);
            $table->decimal('price', 10, 3)->default('0.000');
            $table->string('price_prefix', 1);
            $table->integer('points')->unsigned()->default(false);
            $table->string('points_prefix', 1);
             $table->integer('weight')->default(false);
            $table->string('weight_prefix', 1);
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
        Schema::drop('product_option_value');
    }
}
