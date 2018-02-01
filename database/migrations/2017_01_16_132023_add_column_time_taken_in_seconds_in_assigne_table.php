<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTimeTakenInSecondsInAssigneTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_task_assignees', function (Blueprint $table) {
            $table->string('time_taken_in_seconds')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_task_assignees', function (Blueprint $table) {
            $table->dropColumn('time_taken_in_seconds');
        });
    }
}
