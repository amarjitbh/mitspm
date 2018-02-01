<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_projects', function (Blueprint $table) {
            $table->increments('user_project_id');
            $table->integer('user_id')->nullable();
            $table->integer('project_id')->nullable();
            $table->tinyInteger('is_admin')->nullable();
            $table->tinyInteger('is_deleted')->nullable();
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
        Schema::drop('user_projects');
    }
}
