<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTimeFieldInLoogedinTAsk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_task_logged_time', function (Blueprint $table) {
           $table->time('start_time')->nullable()->change();
           $table->time('end_time')->nullable()->change();
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
        $table->time('start_time');
        });
    }
}
