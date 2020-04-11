<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendorBranchesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('vendor_branches', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id')->unsigned();
            $table->foreign('vendor_id')
                    ->references('id')->on('vendors')
                    ->onDelete('cascade');
            $table->string('gender_type')->index()->nullable();
            $table->string('name_en')->collation('utf8_unicode_ci');
            $table->string('name_ar')->collation('utf8_unicode_ci');
            $table->string('contact_person_en')->collation('utf8_unicode_ci')->nullable();
            $table->string('contact_person_ar')->collation('utf8_unicode_ci')->nullable();
            $table->text('shifting_hours')->nullable();
            $table->integer('area')->nullable();
            $table->text('amenities')->nullable();
            $table->text('address')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('is_main_branch')->default(false)->comment = "mainBranch:1";
            $table->text('contact')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('vendor_branches');
    }

}
