<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_option', function (Blueprint $table) {
            $table->increments('product_option_id');
            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')
                    ->references('product_id')->on('products')
                    ->onDelete('cascade');
            $table->integer('option_id')->unsigned()->nullable();
            $table->text('value')->nullable();
            $table->boolean('required')->default(true);
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
       Schema::drop('product_option');
    }
}
