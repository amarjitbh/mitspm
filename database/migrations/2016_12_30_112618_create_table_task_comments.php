<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTaskComments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_comments', function (Blueprint $table) {
            $table->increments('task_comment_id');
            $table->integer('project_task_id')->nullable();
            $table->text('comment');
            $table->integer('posted_by_user_id');
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
        Schema::drop('task_comments');
    }
}
