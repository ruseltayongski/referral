<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonalAndSocialHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personal_and_social_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id');
            $table->string('smoking');
            $table->string('smoking_remarks');
            $table->string('alcohol_drinking');
            $table->string('illicit_drugs');
            $table->string('current_medications');
            $table->string('pertinent_laboratory_and_procedures');
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
        Schema::dropIfExists('personal_and_social_history');
    }
}
