<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCcPaymentsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('cc_payments', function (Blueprint $table) {
            $table->increments('id');            
            $table->integer('reference_no')->unsigned();
            $table->bigInteger('response_code')->nullable();
            $table->text('response_desc')->nullable();
            $table->string('message')->nullable();
            $table->bigInteger('receipt_no')->nullable();
            $table->bigInteger('transaction_no')->nullable();
            $table->bigInteger('acquirer_response_code')->nullable();
            $table->bigInteger('auth_id')->nullable();
            $table->bigInteger('batch_no')->nullable();
            $table->bigInteger('card_type')->nullable();
            $table->date('date')->nullable();
            $table->decimal('amount', 10, 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('cc_payments');
    }

}
