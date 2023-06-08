<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->bigInteger('group_id')->index()->nullable();
            $table->foreign('group_id')->references('id')->on('groups')->cascadeOnDelete();
            $table->bigInteger('owner_id')->index();
            $table->foreign('owner_id')->references('id')->on('users');
            $table->bigInteger('category_id')->index()->nullable();
            $table->foreign('category_id')->references('id')->on('categories');
            $table->bigInteger('max_participants');
            $table->string('location');
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->timestamps();
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
        Schema::dropIfExists('events');
    }
}
