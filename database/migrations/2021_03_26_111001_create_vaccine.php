<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVaccine extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vaccine', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('encoded_by')->nullable();
            $table->integer('facility_id')->nullable();
            $table->string('typeof_vaccine','100' )->nullable();
            $table->string('priority','100' )->nullable();
            $table->string('sub_priority','100' )->nullable();
            $table->integer('province_id')->nullable();
            $table->integer('muncity_id')->nullable();
            $table->string('no_eli_pop','255' )->nullable();
            $table->string('ownership','100' )->nullable();
            $table->string('nvac_allocated','100' )->nullable();
            $table->dateTime('first_dose')->nullable();
            $table->dateTime('second_dose')->nullable();
            $table->dateTime('dateof_del')->nullable();
            $table->string('tgtdoseper_day','100' )->nullable();
            $table->string('numof_vaccinated','100' )->nullable();
            $table->string('aef1','100' )->nullable();
            $table->string('aef1_qty','100' )->nullable();
            $table->string('deferred','100' )->nullable();
            $table->string('refused','100' )->nullable();
            $table->string('percent_coverage','100' )->nullable();
            $table->string('consumption_rate','100' )->nullable();
            $table->string('remaining_unvaccinated','100' )->nullable();
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
        Schema::dropIfExists('vaccine');
    }
}
