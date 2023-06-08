<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('event_tags', function (Blueprint $table) {
            $table->bigInteger('event_id')->index();
            $table->foreign('event_id')->references('id')->on('events')->cascadeOnDelete();
            $table->bigInteger('tag_id')->index();
            $table->foreign('tag_id')->references('id')->on('tags');
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
        Schema::dropIfExists('event_tags');
    }
}
