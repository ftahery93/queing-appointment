<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->collation = 'utf8_unicode_ci';
            $table->increments('id');
            $table->text('subject')->collation('utf8_unicode_ci');
            $table->text('content')->collation('utf8_unicode_ci');
            $table->text('url')->collation('utf8_unicode_ci');
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
       Schema::dropIfExists('email_templates');
    }
}
