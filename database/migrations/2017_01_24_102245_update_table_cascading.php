<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableCascading extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_project_columns', function (Blueprint $table) {
            $table->integer('project_board_column_id')->unsigned()->change();
            $table->foreign('project_board_column_id')
                ->references('project_board_column_id')
                ->on('project_board_column')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
