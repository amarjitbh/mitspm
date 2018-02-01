<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Timezone extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('countries_timezone')) {

            Schema::create('countries_timezone', function (Blueprint $table) {
                $table->increments('countries_timezone_id');
                $table->integer('country_id')->nullable();
                $table->string('timezone', 255);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries_timezone');
    }
}
