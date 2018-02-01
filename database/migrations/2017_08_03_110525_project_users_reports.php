<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProjectUsersReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_users_reports', function(Blueprint $table){
            $table->increments('project_users_reports_id');
            $table->integer('project_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->date('report_date')->nullable();
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
        Schema::drop('project_users_reports');
    }
}
