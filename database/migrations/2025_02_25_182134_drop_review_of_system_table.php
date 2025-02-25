<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropReviewOfSystemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('review_of_system');
        Schema::create('review_of_system', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id')->nullable();
            $table->string('skin')->nullable();
            $table->string('head')->nullable();
            $table->string('eyes')->nullable();
            $table->string('ears')->nullable();
            $table->string('nose_or_sinuses')->nullable();
            $table->string('mouth_or_throat')->nullable();
            $table->string('neck')->nullable();
            $table->string('breast')->nullable();
            $table->text('respiratory_or_cardiac')->nullable();
            $table->text('gastrointestinal')->nullable();
            $table->string('urinary')->nullable();
            $table->string('peripheral_vascular')->nullable();
            $table->string('musculoskeletal')->nullable();
            $table->text('neurologic')->nullable();
            $table->string('hematologic')->nullable();
            $table->string('endocrine')->nullable();
            $table->string('psychiatric')->nullable();
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
        
    }
}
