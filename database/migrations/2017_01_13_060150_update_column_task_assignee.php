<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateColumnTaskAssignee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_task_assignees', function (Blueprint $table) {
            $table->time('logging_time')->nullable()->change();

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
            $table->tinyint('logging_time');
        });
    }
}
