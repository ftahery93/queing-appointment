<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('customer_address', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->unsigned();
            $table->text('area')->nullable();
            $table->text('street')->nullable();
            $table->text('house_building_num')->nullable()->comment = "House/Building No.";
            $table->text('avenue')->nullable();
            $table->text('floor')->nullable();
            $table->text('flat')->nullable();
            $table->text('block')->nullable();
            $table->boolean('default_address')->default(true)->comment = "1:Set Default";
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
       Schema::drop('customer_address');
    }
}
