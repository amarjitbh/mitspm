<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProjectTasksAssignee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_task_assignees', function (Blueprint $table) {
            $table->increments('project_task_assigne_id');
            $table->integer('project_task_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('assigned_by_user_id')->nullable();
            $table->boolean('logging_time');
            $table->integer('is_deleted')->default(0);
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
        Schema::drop('project_task_assignees');
    }
}
