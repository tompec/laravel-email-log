<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('email-log.table_name'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('from');
            $table->string('to');
            $table->string('subject');
            $table->text('body');

            $table->string('provider')->nullable();
            $table->string('provider_email_id')->nullable();

            $table->unsignedBigInteger('recipient_id')->nullable();
            $table->string('recipient_type')->nullable();

            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamps();

            $table->index(['recipient_id', 'recipient_type'], 'recipient');
            $table->index(['provider', 'provider_email_id'], 'provider_email_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('email-log.table_name'));
    }
}
