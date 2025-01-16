<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDateTimeSlotInTelemedAssignDoctor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telemed_assign_doctor', function (Blueprint $table) {
            //
            $table->date('appointed_date')->after('id')->nullable();
            $table->time('start_time')->after('appointed_date')->nullable();
            $table->time('end_time')->after('start_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('telemed_assign_doctor', function (Blueprint $table) {
            //
            $table->dropColumn('appointed_date');
            $table->dropColumn('start_time');
            $table->dropColumn('end_time');
        });
    }
}
