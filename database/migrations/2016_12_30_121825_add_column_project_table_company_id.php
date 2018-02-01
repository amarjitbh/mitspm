<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnProjectTableCompanyId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('projects','company_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->integer('company_id');
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
        if(Schema::hasColumn('projects','company_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }
    }
}
