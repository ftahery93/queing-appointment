<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriberAttendTrainerTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('subscriber_attend_trainers', function (Blueprint $table) {
            $table->collation = 'utf8_unicode_ci';
            $table->increments('id');
            $table->integer('subscribed_package_id')->index()->unsigned()->nullable();
            $table->dateTime('date')->nullable();
            $table->text('description_en')->collation('utf8_unicode_ci')->nullable();
            $table->boolean('status')->default(false)->comment = "Attend:1";
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('subscriber_attend_trainers');
    }

}
