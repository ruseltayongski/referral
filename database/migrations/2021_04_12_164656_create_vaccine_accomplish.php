<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVaccineAccomplish extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vaccine_accomplish', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('encoded_by')->nullable();
            $table->integer('vaccine_id')->nullable();
            $table->string('typeof_vaccine','100' )->nullable();
            $table->dateTime('date_first')->nullable();
            $table->dateTime('date_second')->nullable();
            $table->string('vaccinated_first')->nullable();
            $table->string('vaccinated_second')->nullable();
            $table->string('aefi_first')->nullable();
            $table->string('aefi_second')->nullable();
            $table->string('refused_first')->nullable();
            $table->string('refused_second')->nullable();
            $table->string('deferred_first')->nullable();
            $table->string('deferred_second')->nullable();
            $table->string('wastage_first')->nullable();
            $table->string('wastage_second')->nullable();
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
        Schema::dropIfExists('vaccine_accomplish');
    }
}
