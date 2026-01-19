<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAppointmentByToRebookedInTelemedAssignDoctor extends Migration
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
           $table->renameColumn('appointment_by', 'rebook');
        });

        Schema::table('telemed_assign_doctor', function (Blueprint $table) {
            $table->boolean('rebook')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rebooked_in_telemed_assign_doctor', function (Blueprint $table) {
            //
            Schema::table('telemed_assign_doctor', function (Blueprint $table) {
                $table->renameColumn('rebook', 'appointment_by');
            });

            Schema::table('telemed_assign_doctor', function (Blueprint $table) {
                $table->integer('appointment_by')->nullable()->change();
            });
        });
    }
}
