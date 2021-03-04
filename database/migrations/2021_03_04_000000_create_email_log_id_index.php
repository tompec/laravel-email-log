<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailLogIdIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('email-log.events_table_name'), function (Blueprint $table) {
            $table->index('email_log_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('email-log.events_table_name'), function (Blueprint $table) {
            $table->dropIndex(['email_log_id']);
        });
    }
}
