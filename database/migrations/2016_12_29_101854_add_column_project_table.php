<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('updated_user_id')->nullable();
            $table->renameColumn('user_id', 'created_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->renameColumn('created_user_id','user_id');
            $table->dropColumn('updated_user_id');
        });
    }
}
