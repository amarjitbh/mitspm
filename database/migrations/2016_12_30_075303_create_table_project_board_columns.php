<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProjectBoardColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_board_column', function (Blueprint $table) {
            $table->increments('project_board_column_id');
            $table->integer('project_board_id')->nullable();
            $table->string('column_name',255);
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
        Schema::drop('project_board_column');
    }
}
