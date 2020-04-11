<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_report', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('module_id')->index()->unsigned()->nullable();
            $table->decimal('total_sales', 10, 3);
            $table->decimal('vendor_amount', 10, 3); 
            $table->decimal('trainer_amount', 10, 3); 
            $table->decimal('total_net_profit', 10, 3); 
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
        Schema::dropIfExists('sales_report');
    }
}
