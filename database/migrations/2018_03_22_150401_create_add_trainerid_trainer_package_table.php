<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddTraineridTrainerPackageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trainer_packages', function($table) {
            $table->integer('trainer_id')->index()->unsigned()->nullable();
            $table->foreign('trainer_id')
                    ->references('id')->on('trainers');
        });
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('trainer_packages', function($table) {
            $table->integer('trainer_id')->index()->unsigned()->nullable();
            $table->foreign('trainer_id')
                    ->references('id')->on('trainers');
        });
    }
}
