<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageToBeSendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_to_be_sends', function (Blueprint $table) {
            $table->increments('message_to_be_send_id');
            $table->tinyInteger('message_type');//1 for sms 2 for email
            $table->string('subject');
            $table->text('message_body');
            $table->string('email');
            $table->tinyInteger('is_send')->default('0');
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
        Schema::drop('message_to_be_sends');
    }
}
