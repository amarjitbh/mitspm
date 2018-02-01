<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewfieldInTaskActiviies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('task_activities', 'user_id')) {
            Schema::table('task_activities', function (Blueprint $table) {
                $table->integer('user_id');
            });
        }
    }

    /**     * Reverse the migrations.     *     * @return void */
    public function down()
    {
        if (Schema::hasColumn('task_activities', 'user_id')) {
            Schema::table('task_activities', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }
    }
}
