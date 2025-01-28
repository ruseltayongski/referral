<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTrackingTableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        Schema::table('tracking', function (Blueprint $table) {
           
            $table->dropColumn('asignedDoctorId');

            $table->renameColumn('appointmentId', 'subOpdId');
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
        Schema::table('tracking', function (Blueprint $table) {
           
            $table->integer('asignedDoctorId')->nullable();

            $table->renameColumn('subOpdId', 'appointmentId');
        });
    }
}
