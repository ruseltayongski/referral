<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropPastMedicalHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('past_medical_history');
        Schema::create('past_medical_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id')->nullable();
            $table->string('commordities')->nullable();
            $table->string('commordities_hyper_year')->nullable();
            $table->string('commordities_diabetes_year')->nullable();
            $table->string('commordities_asthma_year')->nullable();
            $table->string('commordities_others')->nullable();
            $table->string('commordities_cancer')->nullable();
            $table->string('heredofamilial_diseases')->nullable();
            $table->string('heredo_hyper_side')->nullable();
            $table->string('heredo_diab_side')->nullable();
            $table->string('heredo_asthma_side')->nullable();
            $table->string('heredo_cancer_side')->nullable();
            $table->string('heredo_kidney_side')->nullable();
            $table->string('heredo_thyroid_side')->nullable();
            $table->string('heredo_others')->nullable();
            $table->string('allergies')->nullable();
            $table->string('allergy_food_cause')->nullable();
            $table->string('allergy_drugs_cause')->nullable();
            $table->string('allergy_others_cause')->nullable();
            $table->string('previous_hospitalization')->nullable();
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
      //
    }
}
