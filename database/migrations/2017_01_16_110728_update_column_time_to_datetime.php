<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateColumnTimeToDatetime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_task_logged_time', function (Blueprint $table) {
            $table->datetime('start_time')->nullable()->change();
            $table->datetime('end_time')->nullable()->change();
        });
        Schema::table('project_task_assignees', function (Blueprint $table) {
            $table->string('logging_time')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_task_logged_time', function (Blueprint $table) {
            $table->time('start_time');
            $table->time('end_time');
        });
        Schema::table('project_task_assignees', function (Blueprint $table) {
            $table->time('logging_time')->nullable();
        });
    }
}
