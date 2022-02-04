<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrenatalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prenatal', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->integer('age');
            $table->integer('g_mother');
            $table->integer('p_mother');
            $table->boolean('maternal_ill');
            $table->string('maternal_ill_desc');
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
        Schema::dropIfExists('prenatal');
    }
}
