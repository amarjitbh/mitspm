<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserTypeFieldInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'user_type')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('user_type');
            });
        }
    }

    /**     * Reverse the migrations.     *     * @return void */
    public function down()
    {
        if (Schema::hasColumn('users', 'user_type')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('user_type');
            });
        }
    }
}
