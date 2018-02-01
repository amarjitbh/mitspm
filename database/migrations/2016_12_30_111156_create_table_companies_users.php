<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCompaniesUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_users', function (Blueprint $table) {
            $table->increments('company_user_id');
            $table->integer('company_id')->nullable() ;
            $table->integer('user_id')->nullable() ;
            $table->tinyInteger('role');
            $table->integer('is_deleted')->default(0);
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
        Schema::drop('company_users');
    }
}
