<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailLogEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('email-log.events_table_name'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('email_log_id');
            $table->string('provider');
            $table->string('provider_event_id')->unique();
            $table->string('name');
            $table->longText('data');
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
        Schema::dropIfExists(config('email-log.events_table_name'));
    }
}
