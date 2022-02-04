<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddToPatientForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_form', function (Blueprint $table) {
            $table->string('comorbidity_name')->nullable();
            $table->integer('comorbidity_year')->nullable();
            $table->string('other_comorbidity')->nullable();
            $table->string('prev_hospitalization')->nullable();
            $table->integer('menarche')->nullable();
            $table->boolean('menopause')->nullable();
            $table->string('mens_cycle')->nullable();
            $table->integer('mens_duration')->nullable();
            $table->boolean('dysme')->nullable();
            $table->string('dysme_medication')->nullable();
            $table->string('contraceptive_others')->nullable();
            $table->string('parity_g')->nullable();
            $table->string('parity_p')->nullable();
            $table->string('parity_ft')->nullable();
            $table->string('parity_pt')->nullable();
            $table->string('parity_a')->nullable();
            $table->string('parity_l')->nullable();
            $table->string('gyne_lnmp')->nullable();
            $table->string('gyne_aog_lnmp')->nullable();
            $table->string('gyne_aog_eutz')->nullable();
            $table->string('prenatal_history')->nullable();
            $table->string('smoking')->nullable();
            $table->string('smoking_remarks')->nullable();
            $table->boolean('alcohol')->nullable();
            $table->integer('alcohol_bottles')->nullable();
            $table->boolean('drugs')->nullable();
            $table->string('drugs_taken')->nullable();
            $table->string('current_meds')->nullable();
            $table->string('diet')->nullable();
            $table->string('diet_description')->nullable();
            $table->float('vital_temp')->nullable();
            $table->float('vital_pulse_rate')->nullable();
            $table->float('vital_respiratory')->nullable();
            $table->float('vital_bp')->nullable();
            $table->float('vital_o2_sat')->nullable();
            $table->integer('glasgow_pupil')->nullable();
            $table->integer('glasgow_motor')->nullable();
            $table->integer('glasgow_verbal')->nullable();
            $table->integer('glasgow_eye')->nullable();
            $table->integer('glasgow_gcs')->nullable();
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
    }
}
