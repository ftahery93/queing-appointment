<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id')->unsigned()->nullable();
            $table->foreign('vendor_id')
                    ->references('id')->on('vendors');
            $table->integer('trainer_id')->unsigned()->nullable();
            $table->foreign('trainer_id')
                    ->references('id')->on('trainers');
            $table->integer('user_type')->unsigned()->comment = "Vendor:1,Trainer:2";
            $table->decimal('amount', 10, 3);
            $table->date('transferred_date')->nullable();
            $table->text('attachment')->nullable();
            $table->text('comment')->collation('utf8_unicode_ci')->nullable();
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->string('mobile')->index()->nullable();
            $table->string('acc_name')->index()->nullable();
            $table->bigInteger('acc_num')->index()->nullable();
            $table->bigInteger('ibn_num')->index()->nullable();
            $table->bigInteger('reference_num')->nullable();
            $table->bigInteger('payment_mode')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('transactions');
    }

}
