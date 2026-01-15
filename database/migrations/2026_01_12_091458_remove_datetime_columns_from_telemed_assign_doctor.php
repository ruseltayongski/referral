<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveDatetimeColumnsFromTelemedAssignDoctor extends Migration
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
            $table->dropColumn([
                'appointed_date',
                'start_time',
                'end_time'
            ]);
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
            $table->date('appointed_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
        });
    }
}
