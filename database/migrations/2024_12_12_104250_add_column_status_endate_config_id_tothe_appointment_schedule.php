<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnStatusEndateConfigIdTotheAppointmentSchedule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('appointment_schedule', function (Blueprint $table) {
            $table->integer('configId')->nullable()->after('id');
            $table->date('date_end')->nullable()->after('appointed_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appointment_schedule', function (Blueprint $table){
            $table->dropColumn('configId');
            $table->dropColumn('date_end');
        });
    }
}
