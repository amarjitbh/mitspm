<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableColumnProjectInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_invites', function (Blueprint $table) {
            $table->string('email');
            $table->integer('is_admin')->default(0);
            $table->renameColumn('user_id','invited_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_invites', function (Blueprint $table) {
            $table->renameColumn('invited_by_user_id','user_id');
            $table->dropColumn('is_admin');
            $table->dropColumn('email');
        });
    }
}
