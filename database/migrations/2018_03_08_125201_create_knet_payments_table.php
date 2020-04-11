<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKnetPaymentsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('knet_payments', function (Blueprint $table) {
            $table->increments('id');            
            $table->integer('payment_id')->unsigned();
            $table->decimal('amount', 10, 3);
            $table->bigInteger('track_id')->nullable();
            $table->bigInteger('transaction_id')->nullable();
            $table->bigInteger('auth')->nullable();
            $table->bigInteger('reference_id')->nullable();
            $table->string('result')->nullable();
            $table->date('post_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('knet_payments');
    }

}
