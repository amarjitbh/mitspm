<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCountryTimeZoneTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if(Schema::hasTable('countries_timezone')) {
            Schema::table('countries_timezone', function (Blueprint $table) {
                $table->string('compare_utc');
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

        if(Schema::hasTable('countries_timezone')) {
            Schema::table('countries_timezone', function (Blueprint $table) {
                $table->dropColumn('compare_utc');
            });
        }
    }
}
