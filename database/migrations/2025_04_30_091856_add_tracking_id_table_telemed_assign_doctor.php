<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrackingIdTableTelemedAssignDoctor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('telemed_assign_doctor', function (Blueprint $table) {
            $table->text('tracking_id')->nullable()->after('subopd_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('telemed_assign_doctor', function (Blueprint $table) {
            $table->dropColumn('tracking_id');
        });
    }
}
