<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewOfSystems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('review_of_systems', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id');
            $table->string('skin');
            $table->string('head');
            $table->string('eyes');
            $table->string('ears');
            $table->string('nose_or_sinuses');
            $table->string('mouth_or_throat');
            $table->string('neck');
            $table->string('breast');
            $table->string('respiratory_or_cardiac');
            $table->string('gastrointestinal');
            $table->string('urinary');
            $table->string('peripheral_vascular');
            $table->string('musculoskeletal');
            $table->string('neurologic');
            $table->string('hematologic');
            $table->string('endocrine');
            $table->string('psychiatric');
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
        Schema::dropIfExists('review_of_systems');
    }
}
