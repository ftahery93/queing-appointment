<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendorPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('vendor_permissions', function (Blueprint $table) {
            $table->collation = 'utf8_unicode_ci';
            $table->increments('id');
			$table->integer('vendor_id')->unsigned();
            $table->foreign('vendor_id')
                    ->references('id')->on('vendors')
                    ->onDelete('cascade');
            $table->string('groupname')->collation('utf8_unicode_ci');
            $table->text('permissions');
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
         Schema::dropIfExists('vendor_permissions');
    }
}
