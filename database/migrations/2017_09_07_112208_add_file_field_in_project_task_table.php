<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFileFieldInProjectTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('project_tasks', 'file')) {
            Schema::table('project_tasks', function (Blueprint $table) {
                $table->string('file');
            });
        }
    }

    /**     * Reverse the migrations.     *     * @return void */
    public function down()
    {
        if (Schema::hasColumn('project_tasks', 'file')) {
            Schema::table('project_tasks', function (Blueprint $table) {
                $table->dropColumn('file');
            });
        }
    }
}
