<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRecipientLevelToWebsocketNotifications extends Migration
{
    public function up()
    {
        Schema::table('websocket_notifications', function (Blueprint $table) {
            $table->string('recipient_level', 50)->nullable()->after('user_id');
            $table->index(['user_id', 'recipient_level', 'read_at']);
        });

        DB::table('websocket_notifications')
            ->join('users', 'users.id', '=', 'websocket_notifications.user_id')
            ->update(['recipient_level' => DB::raw('users.level')]);
    }

    public function down()
    {
        Schema::table('websocket_notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'recipient_level', 'read_at']);
            $table->dropColumn('recipient_level');
        });
    }
}