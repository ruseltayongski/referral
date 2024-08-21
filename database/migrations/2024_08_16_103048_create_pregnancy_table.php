<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePregnancyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pregnancy', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id')->nullable();
            $table->integer('pregnancy_order')->nullable();
            $table->string('pregnancy_year')->nullable();
            $table->string('pregnancy_gestation_completed')->nullable();
            $table->string('pregnancy_outcome')->nullable();
            $table->string('pregnancy_place_of_birth')->nullable();
            $table->string('pregnancy_sex')->nullable();
            $table->string('pregnancy_birth_weight')->nullable();
            $table->string('pregnancy_present_status')->nullable();
            $table->string('pregnancy_complication')->nullable();
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
        Schema::dropIfExists('pregnancy');
    }
}
