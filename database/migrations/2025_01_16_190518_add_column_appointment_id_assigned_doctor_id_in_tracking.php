<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnAppointmentIdAssignedDoctorIdInTracking extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracking', function (Blueprint $table) {
            //
            $table->integer('appointmentId')->after('department_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracking', function (Blueprint $table) {
            //
            $table->dropColumn('appointmentId');
        });
    }
}
