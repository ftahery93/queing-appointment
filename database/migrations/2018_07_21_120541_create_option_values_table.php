<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOptionValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('option_values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('option_id')->unsigned();
            $table->foreign('option_id')
                    ->references('id')->on('options')
                    ->onDelete('cascade');
            $table->string('name_en')->collation('utf8_unicode_ci');
            $table->string('name_ar')->collation('utf8_unicode_ci');
            $table->integer('sort_order')->unsigned()->default(false);
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
       Schema::drop('option_values');
    }
}
