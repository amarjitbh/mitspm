<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplateLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('template_logs')){
            Schema::create('template_logs', function (Blueprint $table) {
                $table->increments('template_log_id');
                $table->integer('action_id');
                $table->string('tags');
                $table->string('type', 255);
                $table->text('message');
                $table->timestamps();
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
        Schema::drop('template_logs');
    }
}
