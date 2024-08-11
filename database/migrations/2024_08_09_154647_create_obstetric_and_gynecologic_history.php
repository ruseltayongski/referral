<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateObstetricAndGynecologicHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('obstetric_and_gynecologic_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id');
            $table->string('menarche');
            $table->string('menopause');
            $table->string('menstrual_cycle');
            $table->string('menstrual_cycle_duration');
            $table->string('menstrual_cycle_padsperday');
            $table->string('menstrual_cycle_dysmenorrhea');
            $table->string('menstrual_cycle_medication');
            $table->string('contraceptive_history');
            $table->string('parity_g');
            $table->string('parity_p');
            $table->string('parity_ft');
            $table->string('parity_pt');
            $table->string('parity_a');
            $table->string('parity_l');
            $table->string('parity_lnmp');
            $table->string('parity_edc');
            $table->string('aog_lnmp');
            $table->string('aog_eutz');
            $table->string('prenatal_history');
            $table->string('pregnancy_order');
            $table->string('pregnancy_year');
            $table->string('pregnancy_gestation_completed');
            $table->string('pregnancy_outcome');
            $table->string('pregnancy_place_of_birth');
            $table->string('pregnancy_sex');
            $table->string('pregnancy_birth_weight');
            $table->string('pregnancy_present_status');
            $table->string('pregnancy_complication');
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
        Schema::dropIfExists('obstetric_and_gynecologic_history');
    }
}
