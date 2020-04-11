<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('send_to')->nullable()->comment = "All Application Users:0,Registered Users:1,Non-Register Users:2";
            $table->text('subject')->collation('utf8_unicode_ci')->nullable();
            $table->text('subject_ar')->collation('utf8_unicode_ci')->nullable();
            $table->text('message')->collation('utf8_unicode_ci')->nullable();
            $table->text('message_ar')->collation('utf8_unicode_ci')->nullable();
            $table->text('link')->nullable();
            $table->dateTime('notification_date')->nullable();
            $table->boolean('sent_status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('notifications');
    }

}
