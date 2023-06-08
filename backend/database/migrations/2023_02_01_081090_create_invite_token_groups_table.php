<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInviteTokenGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('invite_token_groups', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('group_id')->index();
            $table->foreign('group_id')->references('id')->on('groups');
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
        Schema::dropIfExists('invite_token_groups');
    }
}
