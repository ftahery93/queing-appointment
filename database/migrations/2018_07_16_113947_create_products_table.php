<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id')->unsigned();
            $table->foreign('vendor_id')
                    ->references('id')->on('vendors');
            $table->string('model')->collation('utf8_unicode_ci');
            $table->string('location')->collation('utf8_unicode_ci');
            $table->integer('quantity')->unsigned()->default(false);
            $table->string('image')->collation('utf8_unicode_ci');
            $table->integer('stock_status_id');
            $table->decimal('price', 10, 3)->default('0.000');
            $table->integer('minimum')->unsigned()->default(true);
            $table->integer('sort_order')->unsigned()->default(false);
            $table->boolean('status')->default(true);
            $table->integer('viewed')->unsigned()->default(false);
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
       Schema::drop('products');
    }
}
