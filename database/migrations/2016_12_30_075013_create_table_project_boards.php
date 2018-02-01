<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProjectBoards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_boards', function (Blueprint $table) {
            $table->increments('project_board_id');
            $table->string('project_board_name',255);
            $table->integer('project_id')->nullable();
            $table->string('board_name',255);
            $table->text('description');
            $table->integer('created_by_user_id');
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
        Schema::drop('project_boards');
    }
}
