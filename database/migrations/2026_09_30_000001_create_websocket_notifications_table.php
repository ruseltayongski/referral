<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebsocketNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('websocket_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('event_type', 50);
            $table->string('title', 100);
            $table->string('body', 500);
            $table->string('related_code', 100)->nullable();
            $table->unsignedInteger('related_id')->nullable();
            $table->string('destination_url', 500)->nullable();
            $table->char('notification_key', 64);
            $table->timestamp('occurred_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'notification_key']);
            $table->index(['user_id', 'read_at', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('websocket_notifications');
    }
}