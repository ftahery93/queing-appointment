<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddColumnRegisteredusersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('registered_users', function($table) {
            $table->integer('area_id');
            $table->integer('gender_id');
            $table->date('dob')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
         Schema::table('registered_users', function($table) {
            $table->dropColumn('area_id');
            $table->dropColumn('gender_id');
            $table->dropColumn('dob')->nullable();
        });
    }

}
