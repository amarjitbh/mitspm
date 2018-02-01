<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBoardColumsAddColorCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('project_board_column')) {
            Schema::table('project_board_column', function (Blueprint $table) {
                $table->string('column_color_code');
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
        if(Schema::hasTable('project_board_column')) {
            Schema::table('project_board_column', function (Blueprint $table) {
                $table->dropColumn('column_color_code');
            });
        }
    }
}
