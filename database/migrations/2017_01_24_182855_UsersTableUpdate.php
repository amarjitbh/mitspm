<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersTableUpdate extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){

        if(Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('country_id');
                $table->integer('country_timezone_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){

        if(Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('country_id');
                $table->dropColumn('countries_timezone_id');
            });
        }
    }

}
