<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePregnancyHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pregnancy_history', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('order');
            $table->integer('year');
            $table->string('gestation');
            $table->string('outcome');
            $table->string('place');
            $table->string('sex');
            $table->float('birth_weight');
            $table->string('status');
            $table->string('complications');
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
        Schema::dropIfExists('pregnancy_history');
    }
}
