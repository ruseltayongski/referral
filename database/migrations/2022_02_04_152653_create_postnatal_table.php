<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostnatalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postnatal', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->integer('breastfed');
            $table->string('formula_fed');
            $table->integer('semi_solid_food');
            $table->boolean('bcg');
            $table->integer('dpt_opv');
            $table->integer('hep_b');
            $table->boolean('measles');
            $table->boolean('mmr');
            $table->string('others');
            $table->string('milestone');
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
        Schema::dropIfExists('postnatal');
    }
}
