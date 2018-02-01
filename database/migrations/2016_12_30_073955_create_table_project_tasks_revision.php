<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProjectTasksRevision extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_task_revisions', function (Blueprint $table) {
            $table->increments('project_task_revision_id');
            $table->integer('project_task_id')->nullable();
            $table->string('subject',255);
            $table->text('description');
            $table->date('estimated_delivery_date');
            $table->time('estimated_delivery_time');
            $table->integer('updated_by')->nullable();
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
        Schema::drop('project_task_revisions');
    }
}
