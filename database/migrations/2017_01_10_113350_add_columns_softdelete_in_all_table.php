<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsSoftdeleteInAllTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasColumn('companies','is_deleted')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->softDeletes();
                $table->dropColumn('is_deleted');
            });
        }
        if(Schema::hasColumn('company_users','is_deleted')) {
            Schema::table('company_users', function (Blueprint $table) {
                $table->softDeletes();
                $table->dropColumn('is_deleted');
            });
        }
        if(Schema::hasColumn('project_boards','is_deleted')) {
            Schema::table('project_boards', function (Blueprint $table) {
                $table->softDeletes();
                $table->dropColumn('is_deleted');
            });
        }
        if(Schema::hasColumn('project_board_column','is_deleted')) {
            Schema::table('project_board_column', function (Blueprint $table) {
                $table->softDeletes();
                $table->dropColumn('is_deleted');
            });
        }

        if(Schema::hasColumn('project_tasks','is_deleted')) {
            Schema::table('project_tasks', function (Blueprint $table) {
                $table->softDeletes();
                $table->dropColumn('is_deleted');
            });
        }
        if(Schema::hasColumn('project_task_assignees','is_deleted')) {
            Schema::table('project_task_assignees', function (Blueprint $table) {
                $table->softDeletes();
                $table->dropColumn('is_deleted');
            });
        }
        if(Schema::hasColumn('project_task_logged_time','is_deleted')) {
            Schema::table('project_task_logged_time', function (Blueprint $table) {
                $table->softDeletes();
                $table->dropColumn('is_deleted');
            });
        }
        if(Schema::hasColumn('project_task_revisions','is_deleted')) {
            Schema::table('project_task_revisions', function (Blueprint $table) {
                $table->softDeletes();
                $table->dropColumn('is_deleted');
            });
        }
        if(Schema::hasColumn('task_comments','is_deleted')) {
            Schema::table('task_comments', function (Blueprint $table) {
                $table->softDeletes();
                $table->dropColumn('is_deleted');
            });
        }
        if(Schema::hasColumn('user_projects','is_deleted')) {
            Schema::table('user_projects', function (Blueprint $table) {
                $table->softDeletes();
                $table->dropColumn('is_deleted');
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
        if(Schema::hasColumn('companies','is_deleted')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->dropSoftDeletes();
                $table->integer('is_deleted')->default(0);
            });
        }
        if(Schema::hasColumn('company_users','is_deleted')) {
            Schema::table('company_users', function (Blueprint $table) {
                $table->dropSoftDeletes();
                $table->integer('is_deleted')->default(0);
            });
        }
        if(Schema::hasColumn('project_boards','is_deleted')) {
            Schema::table('project_boards', function (Blueprint $table) {
                $table->dropSoftDeletes();
                $table->integer('is_deleted')->default(0);
            });
        }
        if(Schema::hasColumn('project_board_column','is_deleted')) {
            Schema::table('project_board_column', function (Blueprint $table) {
                $table->dropSoftDeletes();
                $table->integer('is_deleted')->default(0);
            });
        }

        if(Schema::hasColumn('project_tasks','is_deleted')) {
            Schema::table('project_tasks', function (Blueprint $table) {
                $table->dropSoftDeletes();
                $table->integer('is_deleted')->default(0);
            });
        }
        if(Schema::hasColumn('project_task_assignees','is_deleted')) {
            Schema::table('project_task_assignees', function (Blueprint $table) {
                $table->dropSoftDeletes();
                $table->integer('is_deleted')->default(0);
            });
        }
        if(Schema::hasColumn('project_task_logged_time','is_deleted')) {
            Schema::table('project_task_logged_time', function (Blueprint $table) {
                $table->dropSoftDeletes();
                $table->integer('is_deleted')->default(0);
            });
        }
        if(Schema::hasColumn('project_task_revisions','is_deleted')) {
            Schema::table('project_task_revisions', function (Blueprint $table) {
                $table->dropSoftDeletes();
                $table->integer('is_deleted')->default(0);
            });
        }
        if(Schema::hasColumn('task_comments','is_deleted')) {
            Schema::table('task_comments', function (Blueprint $table) {
                $table->dropSoftDeletes();
                $table->integer('is_deleted')->default(0);
            });
        }
        if(Schema::hasColumn('user_projects','is_deleted')) {
            Schema::table('user_projects', function (Blueprint $table) {
                $table->dropSoftDeletes();
                $table->integer('is_deleted')->default(0);
            });
        }
    }
}
