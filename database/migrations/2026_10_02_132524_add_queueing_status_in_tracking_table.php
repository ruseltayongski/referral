<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQueueingStatusInTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity', function (Blueprint $table) {
            $table->string('email_sent_status')->nullable()->after('status');
            $table->string('email_sent_claim_token')->nullable()->after('email_sent_status');
            $table->string('email_sent_claimed_until')->nullable()->after('email_sent_claim_token');
            $table->string('email_sent_at')->nullable()->after('email_sent_claimed_until');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity', function (Blueprint $table) {
            $table->dropColumn('email_sent_status');
            $table->dropColumn('email_sent_claim_token');
            $table->dropColumn('email_sent_claimed_until');
            $table->dropColumn('email_sent_at');
        });
    }
}
