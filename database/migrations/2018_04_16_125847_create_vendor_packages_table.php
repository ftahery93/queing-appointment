<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendorPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('vendor_packages', function (Blueprint $table) {
            $table->collation = 'utf8_unicode_ci';
            $table->increments('id');
             $table->integer('vendor_id')->unsigned();
            $table->foreign('vendor_id')
                    ->references('id')->on('vendors')
                    ->onDelete('cascade');
            $table->string('name_en')->collation('utf8_unicode_ci');
            $table->string('name_ar')->collation('utf8_unicode_ci');
            $table->decimal('price', 10, 3);
            $table->integer('num_days');
            $table->integer('branch_id')->nullable();
            $table->integer('expired_notify_duration');
            $table->text('description_en')->collation('utf8_unicode_ci')->nullable();
            $table->text('description_ar')->collation('utf8_unicode_ci')->nullable();
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('vendor_packages');     
    }
}
