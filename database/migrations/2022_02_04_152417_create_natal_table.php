<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNatalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('natal', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('place');
            $table->string('help');
            $table->string('process');
            $table->string('indication');
            $table->string('term');
            $table->float('weight');
            $table->string('br');
            $table->boolean('good_cry');
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
        Schema::dropIfExists('natal');
    }
}
