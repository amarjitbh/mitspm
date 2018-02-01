<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskActivities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('task_activities')){
            Schema::create('task_activities', function (Blueprint $table) {
                $table->increments('task_activity_id');
                $table->integer('project_id');
                $table->integer('project_task_id');
                $table->text('message');
                $table->timestamps();
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
        Schema::drop('task_activities');
    }
}
