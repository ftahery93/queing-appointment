<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('packages', function (Blueprint $table) {
            $table->collation = 'utf8_unicode_ci';
            $table->increments('id');
            $table->string('name_en')->collation('utf8_unicode_ci');
            $table->string('name_ar')->collation('utf8_unicode_ci');
            $table->integer('num_points')->comment = "Unlimited:0";
            $table->decimal('price', 10, 3);
            $table->integer('num_days');
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
    public function down() {
        Schema::dropIfExists('packages');        
    }

}
