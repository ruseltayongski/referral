<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlasgowComaScale extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('glasgow_coma_scale', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id');
            $table->string('pupil_size_chart');
            $table->string('motor_response');
            $table->string('verbal_response');
            $table->string('eye_response');
            $table->string('gsc_score');
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
        Schema::dropIfExists('glasgow_coma_scale');
    }
}
