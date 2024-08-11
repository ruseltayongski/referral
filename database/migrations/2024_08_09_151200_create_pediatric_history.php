<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePediatricHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pediatric_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id');
            $table->string('prenatal_a');
            $table->string('prenatal_g');
            $table->string('prenatal_p');
            $table->string('natal_born_at');
            $table->string('natal_by');
            $table->string('natal_via');
            $table->string('natal_term');
            $table->string('natal_weight');
            $table->string('natal_br');
            $table->string('natal_with_good_cry');
            $table->string('natal_other_complications');
            $table->string('post_natal_feeding_history');
            $table->string('post_natal_immunization_history');
            $table->string('post_natal_development_milestones');
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
        Schema::dropIfExists('pediatric_history');
    }
}
