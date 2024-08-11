<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLatestVitalSigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('latest_vital_signs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id');
            $table->string('temperature');
            $table->string('pulse_rate');
            $table->string('respiratory_rate');
            $table->string('blood_pressure');
            $table->string('oxygen_saturation');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('latest_vital_signs');
    }
}
