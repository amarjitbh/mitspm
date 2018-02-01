<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueTokenProjectInvite extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('project_invites','unique_token')) {
            Schema::table('project_invites', function (Blueprint $table) {
                $table->string('unique_token');
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
        if(Schema::hasColumn('project_invites','unique_token')) {
            Schema::table('project_invites', function (Blueprint $table) {
                $table->string('unique_token');
            });
        }
    }
}
