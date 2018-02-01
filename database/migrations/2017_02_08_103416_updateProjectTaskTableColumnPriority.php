<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProjectTaskTableColumnPriority extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('project_tasks')) {
            Schema::table('project_tasks', function (Blueprint $table) {
                $table->integer('priority');
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
        if(Schema::hasTable('project_tasks')) {
            Schema::table('project_tasks', function (Blueprint $table) {
                $table->dropColumn('priority');
            });
        }
    }
}
