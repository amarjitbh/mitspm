<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProjectTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_tasks', function (Blueprint $table) {
            $table->increments('project_task_id');
            $table->integer('project_id')->nullable();
            $table->integer('project_board_id')->nullable();
            $table->integer('project_board_column_id')->nullable();
            $table->string('subject',255);
            $table->text('description');
            $table->integer('created_by')->nullable();
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
        Schema::drop('project_tasks');
    }
}
