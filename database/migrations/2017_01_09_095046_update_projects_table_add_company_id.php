<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProjectsTableAddCompanyId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('projects','company_id')){
            Schema::table('projects', function (Blueprint $table) {
                $table->string('company_id',255);
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
        if(Schema::hasColumn('projects','company_id')){
            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }
    }
}
