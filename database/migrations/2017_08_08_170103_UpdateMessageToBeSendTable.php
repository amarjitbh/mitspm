<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMessageToBeSendTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('message_to_be_sends')) {
            Schema::table('message_to_be_sends', function (Blueprint $table) {
                $table->string('from_email');
                $table->string('from_company');
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
        if(Schema::hasTable('message_to_be_sends')) {
            Schema::table('message_to_be_sends', function (Blueprint $table) {
                $table->dropColumn('from_email');
                $table->dropColumn('from_company');
            });
        }
    }
}
