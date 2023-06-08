<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInviteTokenEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('invite_token_events', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('event_id')->index();
            $table->foreign('event_id')->references('id')->on('events');
            $table->string('token')->unique();
            $table->timestamp('created_at');
            $table->timestamp('expires_at');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invite_token_events');
    }
}
