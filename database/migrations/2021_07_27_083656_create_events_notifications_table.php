<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events_notifications', function (Blueprint $table) {
            $table->string('key');
            $table->string('label');
            $table->text('msg_template');
            $table->text('recipient_ids');
            $table->timestamps();

            $table->unique('key');
            $table->primary(['key']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events_notifications');
    }
}
